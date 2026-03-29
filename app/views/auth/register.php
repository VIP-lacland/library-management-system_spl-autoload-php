<?php
if (!defined('BASE_URL')) {
  require_once __DIR__ . '/../../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
<link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
  <title>Document</title>
</head>
<body>
    

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Library - Register Account</h4>
        </div>
        <div class="card-body">
          <?php if (isset($_SESSION['flash']['errors'])): ?>
            <div class="alert-danger">
              <ul>
                <?php foreach ($_SESSION['flash']['errors'] as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php unset($_SESSION['flash']['errors']); ?>
          <?php endif; ?>

          <!-- <?php if (isset($_SESSION['flash']['success'])): ?>
            <div class="alert-success">
              <?= htmlspecialchars($_SESSION['flash']['success']) ?>
            </div>
            <?php unset($_SESSION['flash']['success']); ?>
          <?php endif; ?> -->
          <div class="alert-container"></div>
        </div>

        <form action="<?= url('?url=account/registerProcess') ?>" method="POST">

          <!-- Họ tên -->
          <div class="mb-3 input_box">
            <label for="username" class="form-label">User name<br></label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($_SESSION['old_username']['username'] ?? '') ?>" required>
          </div>

          <!-- Email -->
          <div class="mb-3 input_box">
            <label for="email" class="form-label">Email<br></label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_SESSION['old_email']['email'] ?? '') ?>" required>
          </div>

          <!-- Mật khẩu -->
          <div class="mb-3 input_box">
            <label for="password" class="form-label">Password<br></label>
            <input type="password" class="form-control" id="password" name="password" required>
            <small class="text-muted"><br>At least 8 characters</small>
          </div>

          <!-- Xác nhận mật khẩu -->
          <div class="mb-3 input_box">
            <label for="confirm_password" class="form-label">Comfirm Password<br></label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
          </div>

          <!-- Nút submit -->
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Register</button>
          </div>
        </form>

        <!-- Link đăng nhập -->
        <div class="text-center mt-3">
          <p>Already have an account? <a href="?url=auth/loginForm">Log in now</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</body>
</html>
<script src="<?= asset('js/script.js') ?>"></script>
<?php
unset($_SESSION['old']);
?>


