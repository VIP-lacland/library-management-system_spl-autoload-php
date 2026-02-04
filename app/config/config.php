<?php
/**
 * Configuration File
 */

// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Beng1936@');
define('DB_NAME', 'library_management');

// App Info
define('APP_NAME', 'Library Management System');

// URL - Tự động xác định BASE_URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
// Lấy đường dẫn thư mục public từ script name, loại bỏ /index.php nếu có
$script_name = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$dynamic_base_url = rtrim("{$protocol}://{$host}{$script_name}", '/');

define('BASE_URL', $dynamic_base_url);
define('URL_ROOT', $dynamic_base_url);

// App Root Path
define('APP_ROOT', dirname(__DIR__, 1));

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error Display
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper Functions
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset($file) {
    return BASE_URL . '/' . ltrim($file, '/');
}