<?php
class Borrow {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Lấy tất cả phiếu mượn (có thể lọc theo trạng thái)
    public function getAllLoans($status = null) {
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id";
        
        if ($status) {
            $sql .= " WHERE l.status = :status";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['status' => $status]);
        } else {
            $sql .= " ORDER BY l.borrow_date DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }

    // Lấy danh sách quá hạn
    public function getOverdueLoans() {
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id
                WHERE l.status = 'borrowing' AND l.due_date < CURDATE()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy thông tin một phiếu mượn
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Loans WHERE loan_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Duyệt yêu cầu mượn
    public function approveRequest($loanId, $dueDate) {
        // 1. Cập nhật trạng thái Loan -> borrowing
        $stmt = $this->db->prepare("UPDATE Loans SET status = 'borrowing', due_date = :due_date WHERE loan_id = :id");
        $res = $stmt->execute(['due_date' => $dueDate, 'id' => $loanId]);
        
        if ($res) {
            // 2. Cập nhật trạng thái sách -> borrowed
            $loan = $this->getById($loanId);
            $stmtItem = $this->db->prepare("UPDATE Book_Items SET status = 'borrowed' WHERE book_items_id = :item_id");
            return $stmtItem->execute(['item_id' => $loan['book_items_id']]);
        }
        return false;
    }

    // Từ chối yêu cầu
    public function rejectRequest($loanId) {
        $stmt = $this->db->prepare("UPDATE Loans SET status = 'rejected' WHERE loan_id = :id");
        return $stmt->execute(['id' => $loanId]);
    }

    // Trả sách
    public function returnBook($loanId) {
        $stmt = $this->db->prepare("UPDATE Loans SET status = 'returned', return_date = CURDATE() WHERE loan_id = :id");
        $res = $stmt->execute(['id' => $loanId]);
        
        if ($res) {
            $loan = $this->getById($loanId);
            $stmtItem = $this->db->prepare("UPDATE Book_Items SET status = 'available' WHERE book_items_id = :item_id");
            return $stmtItem->execute(['item_id' => $loan['book_items_id']]);
        }
        return false;
    }

    // Tìm kiếm phiếu mượn
    public function searchLoans($keyword) {
        $keyword = "%$keyword%";
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id
                WHERE u.name LIKE :keyword OR u.email LIKE :keyword OR b.title LIKE :keyword OR bi.barcode LIKE :keyword
                ORDER BY l.borrow_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => $keyword]);
        return $stmt->fetchAll();
    }

    // Lấy danh sách phiếu mượn có phân trang và tìm kiếm
    public function getLoansPaginated($limit, $offset, $keyword = '') {
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id";
        
        if (!empty($keyword)) {
            $sql .= " WHERE u.name LIKE :keyword OR u.email LIKE :keyword OR b.title LIKE :keyword OR bi.barcode LIKE :keyword";
        }
        
        $sql .= " ORDER BY l.borrow_date DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', "%$keyword%");
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số phiếu mượn (để phân trang)
    public function countLoans($keyword = '') {
        $sql = "SELECT COUNT(*) as total 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id";
        
        if (!empty($keyword)) {
            $sql .= " WHERE u.name LIKE :keyword OR u.email LIKE :keyword OR b.title LIKE :keyword OR bi.barcode LIKE :keyword";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', "%$keyword%");
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? $row['total'] : 0;
    }

    // Tìm một bản sao sách đang có sẵn (available) và KHÔNG nằm trong danh sách chờ duyệt (pending)
    public function findAvailableCopy($bookId) {
        $sql = "SELECT bi.book_items_id 
                FROM Book_Items bi
                LEFT JOIN Loans l ON bi.book_items_id = l.book_items_id AND l.status = 'pending'
                WHERE bi.book_id = :book_id 
                AND bi.status NOT IN ('borrowed', 'lost', 'damaged', 'maintenance', 'Borrowed', 'Lost', 'Damaged')
                AND l.loan_id IS NULL
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetch();
    }

    // Tạo yêu cầu mượn mới (Pending)
    public function createLoanRequest($userId, $bookItemId, $dueDate) {
        // Ngày mượn là hôm nay, trạng thái pending
        $borrowDate = date('Y-m-d');
        
        $sql = "INSERT INTO Loans (user_id, book_items_id, borrow_date, due_date, status) 
                VALUES (:user_id, :item_id, :borrow_date, :due_date, 'pending')";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId, 
            'item_id' => $bookItemId, 
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate
        ]);
    }

    // Lấy lịch sử mượn của một user cụ thể
    public function getUserLoans($userId) {
        $sql = "SELECT l.*, b.title, b.author, bi.barcode, b.url 
                FROM Loans l
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id
                WHERE l.user_id = :user_id
                ORDER BY l.borrow_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    // Kiểm tra user có đang mượn hoặc chờ duyệt sách này không
    public function isUserBorrowingBook($userId, $bookId) {
        $sql = "SELECT COUNT(*) as total 
                FROM Loans l
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                WHERE l.user_id = :user_id 
                AND bi.book_id = :book_id 
                AND l.status IN ('pending', 'borrowing')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'book_id' => $bookId]);
        $row = $stmt->fetch();
        return ($row && $row['total'] > 0);
    }
}