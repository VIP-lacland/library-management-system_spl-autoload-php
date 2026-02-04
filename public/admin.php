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
require_once('../app/controllers/admin/CategoryController.php');
require_once('../app/controllers/admin/BorrowingController.php');

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Initialize controllers
$dashboardController = new DashboardController();
$categoryController  = new CategoryController();

// Admin routing
switch ($action) {
    case 'dashboard':
        $dashboardController->index();
        break;

    case 'categories':
        $categoryController->index();
        break;
}
