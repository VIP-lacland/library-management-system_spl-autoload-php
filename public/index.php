<?php
session_start();

// autoload register
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../app/controllers/' . $class_name . '.php';
    if (file_exists($file)) require_once $file;

    $file = __DIR__ . '/../app/models/' . $class_name . '.php';
    if (file_exists($file)) require_once $file;

     $file = __DIR__ . '/../app/core/' . $class_name . '.php';
    if (file_exists($file)) require_once $file;
});

require_once '../app/config/config.php';

$url = isset($_GET['url']) ? $_GET['url'] : 'book/index';
$segment = explode('/', $url);

$controller_name = !empty($segment[0]) ? $segment[0] : 'book';
$action = !empty($segment[1]) ? $segment[1] :  'index';
$params = array_slice($segment,2);
$controllerClass = ucfirst($controller_name) . 'Controller';

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


