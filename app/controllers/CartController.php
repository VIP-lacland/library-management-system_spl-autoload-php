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

    // Thêm sách vào giỏ (Session)
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

        // 1. Kiểm tra: Không đặt hơn 1 cuốn cùng loại
        if (in_array($bookId, $_SESSION['cart'])) {
            $this->setFlash('error', 'This book is already in your cart.');
            $this->redirect(url('index.php?action=book-detail&id=' . $bookId));
            return;
        }

        // 1.5 Kiểm tra: User có đang mượn hoặc chờ duyệt cuốn này trong DB không
        if ($this->borrowModel->isUserBorrowingBook($_SESSION['user']['id'], $bookId)) {
            $this->setFlash('error', 'You are already borrowing or have a pending request for this book.');
            $this->redirect(url('index.php?action=book-detail&id=' . $bookId));
            return;
        }

        // 2. Kiểm tra: Tối đa 5 cuốn
        if (count($_SESSION['cart']) >= 5) {
            $this->setFlash('error', 'You can only borrow up to 5 books at a time.');
            $this->redirect(url('index.php?action=book-detail&id=' . $bookId));
            return;
        }

        // 3. Kiểm tra tồn kho (Tìm bản copy available và chưa bị ai request)
        $availableCopy = $this->borrowModel->findAvailableCopy($bookId);
        if (!$availableCopy) {
            $this->setFlash('error', 'This book is currently out of stock or all copies are pending approval.');
            $this->redirect(url('index.php?action=book-detail&id=' . $bookId));
            return;
        }

        // Thêm vào session
        $_SESSION['cart'][] = $bookId;

        $this->setFlash('success', 'Added to cart successfully!');
        $this->redirect(url('index.php?action=cart-list'));
    }

    // Hiển thị danh sách trong giỏ
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

    // Xóa khỏi giỏ
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

    // Xác nhận đặt sách (Lưu vào DB)
    public function checkout()
    {
        $this->requireLogin();
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->setFlash('error', 'Cart is empty.');
            $this->redirect(url('index.php'));
        }

        $userId = $_SESSION['user']['id'];
        $successCount = 0;

        foreach ($cart as $bookId) {
            // Tìm một bản copy đang available và chưa bị ai đặt (pending)
            $copy = $this->borrowModel->findAvailableCopy($bookId);
            
            if ($copy && $this->borrowModel->createLoanRequest($userId, $copy['book_items_id'])) {
                $successCount++;
            }
        }

        // Xóa giỏ hàng sau khi đặt
        unset($_SESSION['cart']);

        if ($successCount > 0) {
            $this->setFlash('success', "Request sent successfully for $successCount books. Please wait for approval.");
        } else {
            $this->setFlash('error', 'Failed to place request. Books might be unavailable.');
        }

        $this->redirect(url('index.php?action=index')); // Hoặc chuyển hướng về trang lịch sử mượn
    }
}
