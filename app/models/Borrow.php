<?php
class Borrow {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ─────────────────────────────────────────────
    // [1] Lấy tất cả phiếu mượn (có thể lọc theo trạng thái)
    // ─────────────────────────────────────────────
    public function getAllLoans($status = null) {
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id";
        
        if ($status) {
            $sql .= " WHERE l.status = :status ORDER BY l.borrow_date DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['status' => $status]);
        } else {
            $sql .= " ORDER BY l.borrow_date DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────────
    // [2] Lấy danh sách quá hạn
    // ─────────────────────────────────────────────
    public function getOverdueLoans() {
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id
                WHERE l.status = 'borrowing' AND l.due_date < CURDATE()
                ORDER BY l.due_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, b.book_id, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id
                WHERE l.loan_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function approveRequest($loanId, $dueDate) {
        $loan = $this->getById($loanId);
        if (!$loan || $loan['status'] !== 'pending') {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE Loans SET status = 'borrowing', due_date = :due_date, borrow_date = CURDATE() WHERE loan_id = :id AND status = 'pending'"
        );
        $res = $stmt->execute(['due_date' => $dueDate, 'id' => $loanId]);
        
        if ($res && $stmt->rowCount() > 0) {
            $stmtItem = $this->db->prepare(
                "UPDATE Book_Items SET status = 'borrowed' WHERE book_items_id = :item_id"
            );
            return $stmtItem->execute(['item_id' => $loan['book_items_id']]);
        }
        return false;
    }

    public function rejectRequest($loanId) {
        $loan = $this->getById($loanId);
        if (!$loan || $loan['status'] !== 'pending') {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE Loans SET status = 'rejected' WHERE loan_id = :id AND status = 'pending'"
        );
        $res = $stmt->execute(['id' => $loanId]);

        if ($res && $stmt->rowCount() > 0) {
            $stmtItem = $this->db->prepare(
                "UPDATE Book_Items SET status = 'available' WHERE book_items_id = :item_id"
            );
            return $stmtItem->execute(['item_id' => $loan['book_items_id']]);
        }
        return false;
    }


    public function returnBook($loanId) {
        $loan = $this->getById($loanId);
        if (!$loan || $loan['status'] !== 'borrowing') {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE Loans SET status = 'returned', return_date = CURDATE() WHERE loan_id = :id AND status = 'borrowing'"
        );
        $res = $stmt->execute(['id' => $loanId]);
        
        if ($res && $stmt->rowCount() > 0) {
            $stmtItem = $this->db->prepare(
                "UPDATE Book_Items SET status = 'available' WHERE book_items_id = :item_id"
            );
            return $stmtItem->execute(['item_id' => $loan['book_items_id']]);
        }
        return false;
    }

    public function searchLoans($keyword) {
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id
                WHERE u.name LIKE :kw1 OR u.email LIKE :kw2 OR b.title LIKE :kw3 OR bi.barcode LIKE :kw4
                ORDER BY l.borrow_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $searchParam = "%$keyword%";
        $stmt->bindValue(':kw1', $searchParam);
        $stmt->bindValue(':kw2', $searchParam);
        $stmt->bindValue(':kw3', $searchParam);
        $stmt->bindValue(':kw4', $searchParam);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getLoansPaginated($limit, $offset, $keyword = '') {
        $sql = "SELECT l.*, u.name as user_name, u.email, b.title as book_title, bi.barcode 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id";
        
        if (!empty($keyword)) {
            $sql .= " WHERE u.name LIKE :kw1 OR u.email LIKE :kw2 OR b.title LIKE :kw3 OR bi.barcode LIKE :kw4";
        }
        
        $sql .= " ORDER BY l.borrow_date DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($keyword)) {
            $searchParam = "%$keyword%";
            $stmt->bindValue(':kw1', $searchParam);
            $stmt->bindValue(':kw2', $searchParam);
            $stmt->bindValue(':kw3', $searchParam);
            $stmt->bindValue(':kw4', $searchParam);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countLoans($keyword = '') {
        $sql = "SELECT COUNT(*) as total 
                FROM Loans l
                JOIN Users u ON l.user_id = u.user_id
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id";
        
        if (!empty($keyword)) {
            $sql .= " WHERE u.name LIKE :kw1 OR u.email LIKE :kw2 OR b.title LIKE :kw3 OR bi.barcode LIKE :kw4";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!empty($keyword)) {
            $searchParam = "%$keyword%";
            $stmt->bindValue(':kw1', $searchParam);
            $stmt->bindValue(':kw2', $searchParam);
            $stmt->bindValue(':kw3', $searchParam);
            $stmt->bindValue(':kw4', $searchParam);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? $row['total'] : 0;
    }

    public function findAvailableCopy($bookId) {
        $sql = "SELECT bi.book_items_id 
                FROM Book_Items bi
                LEFT JOIN Loans l ON bi.book_items_id = l.book_items_id AND l.status IN ('pending', 'borrowing')
                WHERE bi.book_id = :book_id 
                AND bi.status = 'available'
                AND l.loan_id IS NULL
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetch();
    }


    public function createLoanRequest($userId, $bookItemId, $dueDate) {
        $borrowDate = date('Y-m-d');
        
        $sql = "INSERT INTO Loans (user_id, book_items_id, borrow_date, due_date, status) 
                VALUES (:user_id, :item_id, :borrow_date, :due_date, 'pending')";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id'     => $userId, 
            'item_id'     => $bookItemId, 
            'borrow_date' => $borrowDate,
            'due_date'    => $dueDate
        ]);
    }

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

    public static function isValidReturnDate($dateString) {
        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        if (!$date || $date->format('Y-m-d') !== $dateString) {
            return false;
        }

        $today = new DateTime('today');
        $maxDate = new DateTime('today');
        $maxDate->modify('+21 days');

        return ($date >= $today && $date <= $maxDate);
    }


    public function countLoansByUserId($userId) {
        $sql = "SELECT COUNT(*) as total FROM Loans WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }

    public function getLoansByUserIdPaginated($userId, $limit, $offset) {
        $sql = "SELECT l.*, b.title, b.author, bi.barcode, b.url 
                FROM Loans l
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                JOIN Books b ON bi.book_id = b.book_id
                WHERE l.user_id = :user_id
                ORDER BY l.borrow_date DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }    

    public function updateRenewal($loanId, $newDueDate) {
        $sql = "UPDATE Loans 
                SET due_date = :new_due_date, renewal_count = renewal_count + 1
                WHERE loan_id = :loan_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'new_due_date' => $newDueDate,
            'loan_id' => $loanId
        ]);
        return $result && $stmt->rowCount() > 0;
    }


    public function isBookReserved($bookId) {
        $sql = "SELECT COUNT(l.loan_id) as total
                FROM Loans l
                JOIN Book_Items bi ON l.book_items_id = bi.book_items_id
                WHERE bi.book_id = :book_id AND l.status = 'pending'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['book_id' => $bookId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row && $row['total'] > 0;
    }
}