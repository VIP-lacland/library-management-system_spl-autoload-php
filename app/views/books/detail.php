<?php
require_once __DIR__ . '/../../config/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= asset('css/detail.css') ?>">
    <title>Chi tiết sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>
<body>
    <main>
        <div class="container">
            <!-- Hiển thị thông báo lỗi/thành công -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php 
                    $msg = $_SESSION['flash']['success'] ?? $_SESSION['flash']['error'] ?? '';
                    $type = isset($_SESSION['flash']['success']) ? 'success' : 'danger';
                    if($msg):
                ?>
                <div class="alert alert-<?= $type ?> mt-3">
                    <?= $msg; unset($_SESSION['flash']); ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="book-detail">
        <?php if ($book): ?>
            <div class="row">
                <!-- Cột trái: Ảnh sách -->
                <div class="col-md-4 text-center mb-4">
                    <?php if (!empty($book['url'])): ?>
                        <img src="<?= htmlspecialchars($book['url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="img-fluid rounded shadow" style="max-height: 500px; width: auto;">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded shadow" style="height: 400px; width: 100%;">
                            <span>No Image</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Cột phải: Thông tin chi tiết -->
                <div class="col-md-8">
                    <div class="book-header mb-3">
                        <h1 class="fw-bold"><?php echo htmlspecialchars($book['title']); ?></h1>
                        <p class="fs-5 text-muted">
                            <strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?>
                        </p>
                    </div>

                    <div class="book-info mb-4">
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($book['category_name'] ?? 'N/A'); ?></p>
                        <p><strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher'] ?? 'N/A'); ?></p>
                        <p><strong>Year of publication:</strong> <?php echo htmlspecialchars($book['publish_year'] ?? 'N/A'); ?></p>

                        <div class="description mt-3">
                            <strong>Description:</strong><br>
                            <div class="text-justify">
                                <?php echo nl2br(htmlspecialchars($book['description'] ?? '')); ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($statuses)): ?>
                        <div class="status mb-4">
                            <h5>Book condition</h5>
                            <ul class="list-unstyled">
                                <?php foreach ($statuses as $item): ?>
                                    <li>
                                        <span class="badge bg-info text-dark"><?php echo ucfirst(htmlspecialchars($item['status'])); ?></span>: 
                                        <strong><?php echo htmlspecialchars($item['total']); ?></strong> cuốn
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <!-- Form thêm vào giỏ -->
                        <form action="<?= url('index.php?url=cart/add') ?>" method="POST">
                            <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                        
                        <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary btn-lg">
                            ← Back to list
                        </a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-error">
                <p>No books found.</p>
            </div>
            <div class="btn-group">
                <a href="<?= BASE_URL ?> ?>" class="btn btn-secondary">
                    ← Back to the list
                </a>
            </div>
        <?php endif; ?>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMenu() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const navMenu = document.getElementById('navMenu');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (!event.target.closest('nav') && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
            }
        });

        // Handle dropdown on mobile
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) {
                    if (this.querySelector('.dropdown-content')) {
                        e.preventDefault();
                        this.classList.toggle('active');
                    }
                }
            });
        });
    </script>
</body>
</html>
