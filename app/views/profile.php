<?php require_once __DIR__ . '/layouts/header.php'; ?>

<style>
    /* Custom styles for profile page to match header theme */
    .card-header.bg-primary {
        background-color: #667eea !important;
    }
    .btn-primary {
        background-color: #667eea;
        border-color: #667eea;
    }
    .btn-primary:hover {
        background-color: #5568d3; /* Darker shade from header.css */
        border-color: #5568d3;
    }
    .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
    }
    .page-link {
        color: #667eea;
    }
    
    /* CSS để căn giữa form */
    .profile-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 40px 0;
    }
    
    .profile-card-container {
        width: 100%;
        max-width: 600px;
    }
</style>

<div class="container mt-5 mb-5">
    <div class="profile-wrapper">
        <div class="profile-card-container">
            <!-- User Profile Section -->
            <div class="card shadow-sm rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa-solid fa-user-pen me-2"></i>Hồ sơ của tôi</h4>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Flash Messages -->
                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success rounded-lg">
                            <i class="fa-solid fa-check-circle me-2"></i>
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger rounded-lg">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($user)): ?>
                    <form action="<?= url('index.php?action=update-profile') ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control rounded-3" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control rounded-3" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                            <div class="form-text">Email không thể thay đổi.</div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control rounded-3" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control rounded-3" id="address" name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3"><i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi</button>
                        </div>
                    </form>
                    <?php else: ?>
                        <p class="text-center">Không thể tải thông tin người dùng.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>