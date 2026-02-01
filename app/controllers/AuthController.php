<?php

class AuthController extends Controller
{
    public function loginForm()
    {
        if (isset($_SESSION['user'])) {
            if ($_SESSION['user']['role'] === 'admin') {
                $this->redirect(url('index.php?action=admin/dashboard'));
            } else {
                $this->redirect(url('index.php?action=index'));
            }
            exit;
        }

        $this->view('auth/login');
    }

    public function login()
    {
        if (!$this->isPost()) {
            $this->redirect(url('index.php?action=login'));
            return;
        }

        $email = $this->input('email');
        $password = $this->input('password');

        $userModel = $this->model('User');
        $user = $userModel->findByEmail($email);

        // Validate input
        if (!$user || $password !== $user['password']) {
            $this->setFlash('error', 'Invalid email or password');
            $this->redirect(url('index.php?action=login'));
            exit;
        }
        
        if ($user['role'] == 'reader' && $user['status'] != 'active') {
            $this->setFlash('error', 'Your account is blocked');
            $this->redirect(url('index.php?action=login'));
            exit;
        }

        // ✅ FIX: Lưu đầy đủ thông tin user vào session
        //    - Đổi 'username' -> 'name' để khớp với borrow_form view
        //    - Thêm 'phone' field cho borrow_form hiển thị
        $_SESSION['user'] = [
            'id'        => $user['user_id'],
            'name'      => $user['name'],          // ← đổi từ 'username'
            'full_name' => $user['full_name'] ?? '',
            'email'     => $user['email'],
            'phone'     => $user['phone'] ?? '',   // ← thêm phone
            'role'      => $user['role'] 
        ];

        if ($user['role'] === 'admin') {
            $this->redirect(url('admin.php?action=dashboard'));
        } else {
            $this->redirect(url('index.php?action=index'));
        }
        exit;
    }

    public function logout()
    {
        session_destroy();
        $this->redirect(url('index.php?action=index'));
    }

    public function forgotPasswordForm()
    {
        $this->view('auth/forgot-password');
    }

    public function forgotPassword()
    {
        $message = '';
        $error = '';

        if ($this->isPost()) {
            $email = trim($this->input('email'));

            if (empty($email)) {
                $error = 'Enter your email';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address';
            } else {
                $userModel = $this->model('User');
                $user = $userModel->getUserByEmail($email);

                if (!$user) {
                    $message = 'If the email exists in our system, you will receive a password reset link';
                } else {
                    $token = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    if ($userModel->createPasswordReset($user->user_id, $email, $token, $expiresAt)) {
                        $resetLink = url('index.php?action=reset-password&token=' . $token);

                        $subject = 'Reset Password – Library Management System';
                        $body = "Hello " . $user->name . ",\n\n";
                        $body .= "You have requested to reset your password.\n";
                        $body .= "Click the link below to reset your password:\n\n";
                        $body .= $resetLink . "\n\n";
                        $body .= "This link will expire in 1 hour.\n\n";
                        $body .= "If you did not request a password reset, please ignore this email.\n\n";
                        $body .= "Sincerely,\nLibrary Management System";

                        $headers = "From: noreply@library.com\r\n";
                        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                        mail($email, $subject, $body, $headers);

                        $message = 'If the email exists in our system, you will receive a password reset link.';
                    } else {
                        $error = 'An error occurred. Please try again later.';
                    }
                }
            }
        }

        $this->view('auth/forgot-password', ['message' => $message, 'error' => $error]);
    }

    public function resetPassword()
    {
        if ($this->isPost()) {
            $email = $this->input('email');
            $password = $this->input('password');
            $password_confirm = $this->input('password_confirm');

            $error = '';
            $userModel = $this->model('User');
            $user = $userModel->findByEmail($email);

            if (!$user) {
                $error = 'Email does not exist.';
            } elseif ($user['role'] !== 'reader') {
                $error = 'This feature is for reader only.';
            } elseif (empty($password)) {
                $error = 'Please enter a new password.';
            } elseif (strlen($password) < 6) {
                // ✅ FIX: Error message nói "8 characters" nhưng check < 6, đổi cho nhất quán
                $error = 'Password must be at least 6 characters.';
            } elseif ($password !== $password_confirm) {
                $error = 'Passwords do not match.';
            } else {
                if ($userModel->updatePassword($user['user_id'], $password)) {
                    $this->setFlash('success', 'Password has been reset successfully. Please log in.');
                    $this->redirect(url('index.php?action=login'));
                    return;
                } else {
                    $error = 'Something went wrong. Try again later.';
                }
            }
            
            $this->view('auth/reset-password', ['error' => $error, 'email' => $email]);

        } else {
            $this->view('auth/reset-password');
        }
    }

    public function resetPasswordForm()
    {
        $this->view('auth/reset-password');
    }
}