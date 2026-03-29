<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="<?= asset('css/login.css') ?>">
</head>

<body>

    <div class="login-container">

        <h2>User Login</h2>

        <?php if (isset($_SESSION['flash']['success'])): ?>
            <p class="success"><?= htmlspecialchars($_SESSION['flash']['success']); ?></p>
            <?php unset($_SESSION['flash']['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash']['error'])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION['flash']['error']); ?></p>
            <?php unset($_SESSION['flash']['error']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= url('?url=auth/login') ?>">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <div class="auth-message">
                <p>Don't have an account yet?</p>
                <a href="<?= url('?url=account/register') ?>">Register now</a>
            </div>
        </form>

        <a href="<?= url('index.php?url=auth/forgotPasswordForm') ?>">Forgot Password?</a>



    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.success');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 3000); // 3 seconds
            }
        });
    </script>
</body>

</html>