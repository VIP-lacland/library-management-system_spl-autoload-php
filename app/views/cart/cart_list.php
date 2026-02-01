<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Cart List</h2>

    <?php if (isset($_SESSION['flash'])): ?>
        <?php 
            // Hỗ trợ hiển thị flash message từ Controller (tuỳ theo cách bạn implement setFlash)
            $msg = $_SESSION['flash']['success'] ?? $_SESSION['flash']['error'] ?? '';
            $type = isset($_SESSION['flash']['success']) ? 'success' : 'danger';
            if($msg):
        ?>
        <div class="alert alert-<?= $type ?>">
            <?= $msg; unset($_SESSION['flash']); ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($books)): ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td style="width: 80px;">
                                        <?php if (!empty($book['url'])): ?>
                                            <img src="<?= htmlspecialchars($book['url']) ?>" alt="Cover" style="height: 60px; width: auto; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 60px; width: 40px;">No IMG</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= url('index.php?action=book-detail&id=' . $book['book_id']) ?>" class="text-decoration-none fw-bold">
                                            <?= htmlspecialchars($book['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td>
                                        <a href="<?= url('index.php?action=cart-remove&id=' . $book['book_id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to remove this book from the cart?');">
                                            <i class="fas fa-trash"></i> Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center bg-white">
                <a href="<?= url('index.php') ?>" class="btn btn-outline-secondary">← Continue Browsing</a>
                <a href="<?= url('index.php?action=cart-borrow-form') ?>" class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle"></i> Checkout
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h4>Your cart is empty</h4>
            <p class="mb-4">Browse our library and find some books to read!</p>
            <a href="<?= url('index.php') ?>" class="btn btn-primary">Go to Library</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
