<?php
// core/Controller.php

class Controller {
    /**
     * Load view
     */
    protected function view($viewName, $data = []) {
        // Chuyển array thành các biến riêng lẻ
        extract($data);
        
        // Tạo đường dẫn đến view file
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        
        // Kiểm tra file view có tồn tại không
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View does not exist: $viewName");
        }
    }
    
    
    /**
     * Load model
     */
    protected function model($modelName) {
        $modelFile = __DIR__ . '/../models/' . $modelName . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $modelName();
        } else {
            die("Model does not exist: $modelName");
        }
    }
    
    /**
     * Redirect đến URL khác
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Trả về JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Kiểm tra request có phải POST không
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Kiểm tra request có phải GET không
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Lấy input từ POST
     */
    protected function input($key, $default = null) {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Set flash message
     */
    protected function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Get flash message
     */
    protected function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }
    
    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user']);
    }
    
    /**
     * Yêu cầu đăng nhập
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please log in to continue.');
            $this->redirect(url('index.php?action=login'));
        }
    }
}