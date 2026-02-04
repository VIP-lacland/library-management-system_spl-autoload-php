<?php

class ProfileController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function showProfile()
    {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user']['id'])) {
            $this->redirect('index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user']['id'];

        // 2. Lấy thông tin user
        $user = $this->userModel->findById($userId);

        // 3. Lấy lịch sử mượn sách (phân trang)
        $borrowModel = $this->model('Borrow');
        $limit = 5;
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($currentPage - 1) * $limit;

        $totalLoans = $borrowModel->countLoansByUserId($userId);
        $totalPages = $totalLoans > 0 ? ceil($totalLoans / $limit) : 0;
        $loans = $borrowModel->getLoansByUserIdPaginated($userId, $limit, $offset);

        // 3. Hiển thị view
        $this->view('profile', [
            'user' => $user,
            'success' => $this->getFlash('success'),
            'errors' => $this->getFlash('errors'),
            'loans' => $loans,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    public function updateProfile()
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?action=profile');
            return;
        }

        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user']['id'])) {
            $this->redirect('index.php?action=login');
            return;
        }

        $userId = $_SESSION['user']['id'];

        // 2. Lấy dữ liệu từ form
        $data = [
            'name' => $this->input('name'),
            'phone' => $this->input('phone'),
            'address' => $this->input('address')
        ];

        // 3. Validate dữ liệu (đơn giản)
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'Họ và tên không được để trống.';
        }

        if (!empty($errors)) {
            $this->setFlash('errors', $errors);
            $this->redirect('index.php?action=profile');
            return;
        }

        // 4. Cập nhật vào database
        if ($this->userModel->updateProfile($userId, $data)) {
            $_SESSION['user']['username'] = $data['name'];
            $this->setFlash('success', 'Cập nhật thông tin thành công!');
        } else {
            $this->setFlash('errors', ['Có lỗi xảy ra, không thể cập nhật thông tin.']);
        }

        $this->redirect('index.php?action=profile');
    }
}
