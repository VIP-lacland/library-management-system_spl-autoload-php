<?php
session_start();


spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../app/core/' . $class_name . '.php';
    if (file_exists($file)) { require_once $file; return; }

    $file = __DIR__ . '/../app/controllers/admin/' . $class_name . '.php';
    if (file_exists($file)) { require_once $file; return; }

    $file = __DIR__ . '/../app/models/' . $class_name . '.php';
    if (file_exists($file)) { require_once $file; return; }
});

require_once '../app/config/config.php';
// require_once '../app/core/Controller.php';
// require_once '../app/core/Database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    $_SESSION['flash']['error'] = 'Bạn không có quyền truy cập.';
    header('Location: index.php?url=auth/loginForm');
    exit;
}


// Get action from URL
$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard/index';
$segment = explode('/', $url);

$controller_name = !empty($segment[0]) ? $segment[0] : 'dashboard';
$action = !empty($segment[1]) ? $segment[1] : 'index';
$params = array_slice($segment,2);
$controllerClass = 'Admin' . ucfirst($controller_name) . 'Controller';


try {
    $controller = new $controllerClass();
} catch (Error $e) {
    http_response_code(404);
    error_log($e->getMessage());
    echo "Page not found.";
    exit;
}

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo "Action '{$action}' does not exist in '{$controllerClass}'.";
    exit;
}

call_user_func_array([$controller, $action], $params);

// switch ($action) {
//     // ===== DASHBOARD =====
//     case 'dashboard':
//         $dashboardController->index();
//         break;

//     // ===== USER MANAGEMENT =====
//     case 'users':
//         $userController->getAllUser();
//         break;

//     case 'blockUser':
//         $userController->blockUser();
//         break;

//     case 'unblockUser':
//         $userController->unblockUser();
//         break;

//     // ===== BORROWING MANAGEMENT =====
//     case 'borrow-list':
//         $borrowingController->listBorrowing();
//         break;

//     case 'borrow-requests':
//         $borrowingController->requests();
//         break;

//     case 'borrow-overdue':
//         $borrowingController->overdue();
//         break;

//     case 'borrow-approve':
//         $borrowingController->approve();
//         break;

//     case 'borrow-reject':
//         $borrowingController->reject();
//         break;

//     case 'borrow-return':
//         $borrowingController->returnBook();
//         break;

//     // ===== BOOK MANAGEMENT =====
//     case 'book-management':
//         $bookController->adminBookList();
//         break;

//     case 'add-book':
//         $bookController->addBook();
//         break;

//     case 'delete-book':
//         $bookController->deleteBook();
//         break;

//     case 'edit-book':
//         $bookController->editBook();
//         break;

//     // ===== IMPORT BOOKS =====
//     case 'import-books':
//         // Hiển thị trang import
//         $importBookController->importBooks();
//         break;

//     case 'import-books-process':
//         // Xử lý import file CSV
//         $importBookController->process();
//         break;

//     case 'import-books-download-template':
//         // Tải file CSV mẫu
//         $importBookController->downloadTemplate();
//         break;

//     // ===== CATEGORY MANAGEMENT =====
//     case 'category-list':
//         $categoryController->categoryList();
//         break;

//     case 'add-category':
//         $categoryController->addCategory();
//         break;

//     case 'delete-category':
//         $categoryController->deleteCategory();
//         break;

//     case 'edit-category':
//         $categoryController->editCategory();
//         break;

//     // ===== DEFAULT =====
//     default:
//         $dashboardController->index();
//         break;
// }