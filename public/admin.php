<?php
session_start();

require_once '../app/config/config.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['flash']['error'] = 'Bạn không có quyền truy cập.';
    header('Location: index.php?action=login');
    exit;
}

// Admin Controllers
require_once('../app/controllers/admin/DashboardController.php');
require_once('../app/controllers/admin/AdminBookController.php');
require_once('../app/controllers/admin/AdminUserController.php');
require_once('../app/controllers/admin/AdminCategoryController.php');
require_once('../app/controllers/admin/BorrowingController.php');
require_once('../app/controllers/admin/AdminImportBookController.php');

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Initialize controllers
$dashboardController = new DashboardController();
$bookController = new AdminBookController();
$userController = new AdminUserController();
$categoryController = new AdminCategoryController();
$borrowingController = new BorrowingController();
$importBookController = new AdminImportBookController();

// Admin routing
switch ($action) {
    // ===== DASHBOARD =====
    case 'dashboard':
        $dashboardController->index();
        break;

    // ===== USER MANAGEMENT =====
    case 'users':
        $userController->getAllUser();
        break;

    case 'blockUser':
        $userController->blockUser();
        break;

    case 'unblockUser':
        $userController->unblockUser();
        break;

    // ===== BORROWING MANAGEMENT =====
    case 'borrow-list':
        $borrowingController->listBorrowings();
        break;

    case 'borrow-requests':
        $borrowingController->requests();
        break;

    case 'borrow-overdue':
        $borrowingController->overdue();
        break;

    case 'borrow-approve':
        $borrowingController->approve();
        break;

    case 'borrow-reject':
        $borrowingController->reject();
        break;

    case 'borrow-return':
        $borrowingController->returnBook();
        break;

    // ===== BOOK MANAGEMENT =====
    case 'book-management':
        $bookController->adminBookList();
        break;

    case 'add-book':
        $bookController->addBook();
        break;

    case 'delete-book':
        $bookController->deleteBook();
        break;

    case 'edit-book':
        $bookController->editBook();
        break;

    // ===== IMPORT BOOKS =====
    case 'import-books':
        // Hiển thị trang import
        $importBookController->importBooks();
        break;

    case 'import-books-process':
        // Xử lý import file CSV
        $importBookController->process();
        break;

    case 'import-books-download-template':
        // Tải file CSV mẫu
        $importBookController->downloadTemplate();
        break;

    // ===== CATEGORY MANAGEMENT =====
    case 'category-list':
        $categoryController->categoryList();
        break;

    case 'add-category':
        $categoryController->addCategory();
        break;

    case 'delete-category':
        $categoryController->deleteCategory();
        break;

    case 'edit-category':
        $categoryController->editCategory();
        break;

    // ===== DEFAULT =====
    default:
        $dashboardController->index();
        break;
}
?>