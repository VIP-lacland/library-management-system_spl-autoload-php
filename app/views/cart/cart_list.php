<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Cart List</h2>

    <!-- ✅ FIX: Flash messages - xử lý success và error riêng lẻ, clean hơn -->
    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']['error']); ?>
    <?php endif; ?>

    <?php if (!empty($books)): ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Book count badge -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">
                        <span class="badge bg-primary rounded-pill me-2"><?= count($books) ?>/5</span>
                        book<?= count($books) > 1 ? 's' : '' ?> in cart
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td style="width: 80px;">
                                        <?php if (!empty($book['url'])): ?>
                                            <img src="<?= htmlspecialchars($book['url']) ?>" alt="Cover"
                                                 style="height: 60px; width: auto; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded"
                                                 style="height: 60px; width: 40px; font-size: 0.7rem;">No IMG</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= url('index.php?action=book-detail&id=' . $book['book_id']) ?>"
                                           class="text-decoration-none fw-bold text-dark">
                                            <?= htmlspecialchars($book['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted"><?= htmlspecialchars($book['author']) ?></td>
                                    <td class="text-end">
                                        <a href="<?= url('index.php?action=cart-remove&id=' . $book['book_id']) ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Remove this book from cart?');">
                                            <i class="fas fa-trash"></i> Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer: Navigate -->
            <div class="card-footer d-flex justify-content-between align-items-center bg-white">
                <a href="<?= url('index.php') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Continue Browsing
                </a>
                <a href="<?= url('index.php?action=cart-borrow-form') ?>" class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle"></i> Checkout
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty cart state -->
        <div class="alert alert-info text-center py-5 border-0 shadow-sm">
            <i class="fas fa-shopping-cart fa-3x text-info mb-3"></i>
            <h4>Your cart is empty</h4>
            <p class="text-muted mb-4">Browse our library and find some books to read!</p>
            <a href="<?= url('index.php') ?>" class="btn btn-primary">
                <i class="fas fa-book-open"></i> Go to Library
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>