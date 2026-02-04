

<?php
session_start();



require_once '../app/config/config.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';

// User Controllers
require_once('../app/controllers/BookController.php');
require_once('../app/controllers/AccountController.php');
require_once('../app/controllers/AuthController.php');
require_once('../app/controllers/CategoryController.php');


// Get action from URL parameter
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Initialize controllers
$bookController    = new BookController();
$accountController = new AccountController();
$authController    = new AuthController();
$categoryController = new CategoryController();


// Routing
switch ($action) {

    case 'index':
    case '':
        $bookController->index();
        break;

    case 'book-detail':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            die('Invalid book ID');
        }
        $bookController->detail($id);
        break;

    case 'register':
        $accountController->register();
        break;

    case 'register/process':
        $accountController->registerProcess();
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            $authController->loginForm();
        }
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'change-password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountController->changePassword();
        } else {
            $accountController->changePasswordForm();
        }
        break;

    case 'forgot-password':
        $authController->forgotPassword();
        break;

    case 'reset-password':
        $authController->resetPassword();
        break;

    // ================= CATEGORY CRUD (ADMIN) =================

case 'categories':
    $categoryController->index();
    break;

case 'category-create':
    $categoryController->create();
    break;

case 'category-store':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryController->store();
    }
    break;

case 'category-edit':
    $categoryController->edit();
    break;

case 'category-update':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryController->update();
    }
    break;

case 'category-delete':
    $categoryController->delete();
    break;


    default:
        // Nếu action không tồn tại → quay về trang chủ
        $bookController->index();
        break;
}
