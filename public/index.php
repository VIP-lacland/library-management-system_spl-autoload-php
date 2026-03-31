<?php
session_start();

// autoload register
spl_autoload_register(function ($class_name) {
    $dirs = [
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/controllers/admin/',
        __DIR__ . '/../app/model/',
        __DIR__ . '/../app/core/',
    ];
    foreach($dirs as $dir) {
        $file = $dir . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once '../app/config/config.php';

$isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
$defaultUrl = $isAdmin ? 'dashboard/index' : 'book/index';

$url = isset($_GET['url']) ? $_GET['url'] : $defaultUrl;
$segment = explode('/', $url);

$controller_name = $segment[0];
$action = $segment[1];
$params = array_slice($segment, 2);

$publicControllers = ['auth', 'book', 'category'];

if (!in_array($controller_name, $publicControllers) && !isset($_SESSION['user'])) {
    header('Location: index.php?url=auth/loginForm');
    exit;
}

$baseClass = ucfirst($controller_name) . 'Controller';
$controllerClass = $isAdmin ? 'Admin' . $baseClass : $baseClass;

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
