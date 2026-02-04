<?php
class BorrowController extends Controller {
    private $borrowModel;

    public function __construct() {
        $this->borrowModel = $this->model('Borrow');
    }

    public function renew($id) {
        // 1. Kiểm tra session đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?action=login');
            exit;
        }

        $loan = $this->borrowModel->getLoanById($id);

        if (!$loan) {
            $_SESSION['error'] = "Loan record not found.";
            $this->redirect('index.php?action=my_loans');
            exit;
        }

        // 2. CHECK ELIGIBILITY (Acceptance Criteria)
        
        // Check quá hạn
        if ($loan['due_date'] < date('Y-m-d')) {
            $_SESSION['error'] = "This book is overdue. Please return it to the library.";
            $this->redirect('index.php?action=my_loans');
            exit;
        }

        // Check giới hạn 2 lần (Renewal Limit)
        if ($loan['renewal_count'] >= 2) {
            $_SESSION['error'] = "Maximum renewal limit reached (2 times).";
            $this->redirect('index.php?action=my_loans');
            exit;
        }

        // Check Reservations (Task nhỏ từ Jira)
        if ($this->borrowModel->isBookReserved($loan['book_items_id'])) {
            $_SESSION['error'] = "Cannot renew: This book is reserved by another member.";
            $this->redirect('index.php?action=my_loans');
            exit;
        }

        // 3. THỰC HIỆN GIA HẠN (+7 ngày)
        $current_due_date = strtotime($loan['due_date']);
        $new_due_date = date('Y-m-d', strtotime('+7 days', $current_due_date));

        if ($this->borrowModel->updateRenewal($id, $new_due_date)) {
            $_SESSION['success'] = "Book extended! New due date: " . $new_due_date;
        } else {
            $_SESSION['error'] = "Internal error. Please try again later.";
        }

        $this->redirect('index.php?action=my_loans');
        exit;
    }
}