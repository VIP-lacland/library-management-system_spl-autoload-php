<?php

class CartController extends Controller
{
    private $bookModel;
    private $borrowModel;

    public function __construct()
    {
        $this->bookModel = $this->model('Book');
        $this->borrowModel = $this->model('Borrow');
    }

    // ─────────────────────────────────────────────
    // Thêm sách vào giỏ (Session)
    // ─────────────────────────────────────────────
    public function add()
    {
        $this->requireLogin();
        if (!$this->isPost()) {
            $this->redirect(url('index.php'));
        }

        $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

        if ($bookId <= 0) {
            $this->setFlash('error', 'Invalid book.');
            $this->redirect(url('index.php'));
        }

        // Khởi tạo giỏ hàng nếu chưa có
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // 1. Kiểm tra: Không đặt hơn 1 cuốn cùng loại trong cart
        if (in_array($bookId, $_SESSION['cart'])) {
            $this->setFlash('error', 'This book is already in your cart.');
            $this->redirect(url('index.php?action=book-detail&id=' . $bookId));
            return;
        }

        // 2. Kiểm tra: User đang mượn hoặc chờ duyệt cuốn này trong DB
        if ($this->borrowModel->isUserBorrowingBook($_SESSION['user']['id'], $bookId)) {
            $this->setFlash('error', 'You are already borrowing or have a pending request for this book.');
            $this->redirect(url('index.php?action=book-detail&id=' . $bookId));
            return;
        }

        // 3. Kiểm tra: Tối đa 5 cuốn trong giỏ
        if (count($_SESSION['cart']) >= 5) {
            $this->setFlash('error', 'You can only borrow up to 5 books at a time.');
            $this->redirect(url('index.php?action=book-detail&id=' . $bookId));
            return;
        }

        // 4. Kiểm tra tồn kho: Tìm bản copy available và chưa bị ai pending/borrowing
        $availableCopy = $this->borrowModel->findAvailableCopy($bookId);
        if (!$availableCopy) {
            $this->setFlash('error', 'This book is currently out of stock or all copies are pending approval.');
            $this->redirect(url('index.php?action=book-detail&id=' . $bookId));
            return;
        }

        // Thêm vào session cart
        $_SESSION['cart'][] = $bookId;

        $this->setFlash('success', 'Added to cart successfully!');
        $this->redirect(url('index.php?action=cart-list'));
    }

    // ─────────────────────────────────────────────
    // Hiển thị danh sách sách trong giỏ
    // ─────────────────────────────────────────────
    public function index()
    {
        $this->requireLogin();
        $cart = $_SESSION['cart'] ?? [];
        $books = [];

        if (!empty($cart)) {
            foreach ($cart as $id) {
                $book = $this->bookModel->getById($id);
                if ($book) {
                    $books[] = $book;
                }
            }
        }

        $this->view('cart/cart_list', ['books' => $books]);
    }

    // ─────────────────────────────────────────────
    // Hiển thị form mượn sách (chọn ngày trả)
    // ─────────────────────────────────────────────
    public function borrowForm()
    {
        $this->requireLogin();
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $this->setFlash('error', 'Your cart is empty. Please add books before borrowing.');
            $this->redirect(url('index.php'));
            return; 
        }

        // Lấy danh sách sách trong cart để hiển thị preview trên form
        $books = [];
        foreach ($cart as $id) {
            $book = $this->bookModel->getById($id);
            if ($book) {
                $books[] = $book;
            }
        }

        $this->view('cart/borrow_form', ['books' => $books]);
    }

    // ─────────────────────────────────────────────
    // Xóa sách khỏi giỏ
    // ─────────────────────────────────────────────
    public function remove()
    {
        $this->requireLogin();
        $bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (isset($_SESSION['cart'])) {
            $key = array_search($bookId, $_SESSION['cart']);
            if ($key !== false) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
                $this->setFlash('success', 'Removed book from cart.');
            }
        }
        $this->redirect(url('index.php?action=cart-list'));
    }

    // ─────────────────────────────────────────────
    // Xác nhận đặt sách (Lưu yêu cầu mượn vào DB)
    // Flow: Cart -> Borrow Form -> Checkout -> My Loans
    // ─────────────────────────────────────────────
    public function checkout()
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect(url('index.php?action=cart-borrow-form'));
            return;
        }

        $cart = $_SESSION['cart'] ?? [];
        $returnDay = trim($_POST['return_day'] ?? '');

        // ── Validation ──────────────────────────────

        if (empty($cart)) {
            $this->setFlash('error', 'Cart is empty.');
            $this->redirect(url('index.php'));
            return;
        }

        if (empty($returnDay)) {
            $this->setFlash('error', 'Please select a return date.');
            $this->redirect(url('index.php?action=cart-borrow-form'));
            return;
        }

        // ✅ FIX: Server-side validation cho ngày trả (không chỉ dựa client-side)
        //    Đảm bảo: format đúng Y-m-d, nằm trong range [today, today+21 days]
        if (!Borrow::isValidReturnDate($returnDay)) {
            $this->setFlash('error', 'Invalid return date. Please select a date between today and 3 weeks from now.');
            $this->redirect(url('index.php?action=cart-borrow-form'));
            return;
        }

        // ── Tạo loan requests cho từng sách trong cart ──

        $userId = $_SESSION['user']['id'];
        $successCount = 0;
        $failedBooks = [];

        foreach ($cart as $bookId) {
            // Re-check availability at checkout time (quan trọng!)
            $copy = $this->borrowModel->findAvailableCopy($bookId);
            
            if ($copy && $this->borrowModel->createLoanRequest($userId, $copy['book_items_id'], $returnDay)) {
                $successCount++;
            } else {
                // Lưu lại tên sách thất bại để báo user
                $book = $this->bookModel->getById($bookId);
                $failedBooks[] = $book ? htmlspecialchars($book['title']) : "Book #$bookId";
            }
        }

        // Xóa giỏ hàng sau khi checkout (dù thành công hay không)
        unset($_SESSION['cart']);

        // ── Set flash message phù hợp ──

        if ($successCount > 0 && empty($failedBooks)) {
            // Tất cả thành công
            $this->setFlash('success', "Request sent successfully for $successCount book(s). Please wait for admin approval.");
        } elseif ($successCount > 0 && !empty($failedBooks)) {
            // Một phần thành công
            $failedList = implode(', ', $failedBooks);
            $this->setFlash('success', "$successCount book(s) requested successfully.");
            $this->setFlash('error', "Could not request: $failedList (currently unavailable).");
        } else {
            // Tất cả thất bại
            $this->setFlash('error', 'Failed to place any requests. The books might be unavailable.');
        }

        $this->redirect(url('index.php?action=my-loans'));
    }
}