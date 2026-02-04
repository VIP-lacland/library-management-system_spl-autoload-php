<?php
class Borrow {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Lấy thông tin đơn mượn
    public function getLoanById($loan_id) {
        $stmt = $this->db->prepare("SELECT * FROM Loans WHERE loan_id = :id");
        $stmt->execute(['id' => $loan_id]);
        return $stmt->fetch();
    }

    /**
     * TASK: Check Reservations board
     * Kiểm tra xem sách này có đang bị người khác đặt trước không
     */
    public function isBookReserved($book_items_id) {
        // Hiện tại schema chưa có bảng Reservations, nên trả về false.
        // Đây là code chờ sẵn khi nhóm trưởng thêm bảng Reservations.
        /*
        $sql = "SELECT COUNT(*) FROM Reservations 
                WHERE book_items_id = :id AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $book_items_id]);
        return $stmt->fetchColumn() > 0;
        */
        return false; 
    }

    /**
     * TASK: Update due_date and renewal_count simultaneously
     */
    public function updateRenewal($loan_id, $new_due_date) {
        try {
            // Câu query cập nhật 2 cột cùng lúc theo yêu cầu Jira
            $sql = "UPDATE Loans 
                    SET due_date = :new_due, 
                        renewal_count = renewal_count + 1,
                        status = 'renewal'
                    WHERE loan_id = :id 
                    AND renewal_count < 2 
                    AND return_date IS NULL";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'new_due' => $new_due_date,
                'id'      => $loan_id
            ]);
        } catch (PDOException $e) {
            error_log("Renewal Error: " . $e->getMessage());
            return false;
        }
    }
}