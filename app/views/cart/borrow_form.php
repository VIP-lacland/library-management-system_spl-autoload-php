<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="fas fa-file-alt me-2"></i>Borrow Request</h2>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mb-4">
                        Review your selection and set a return date to submit your borrowing request.
                    </p>

                    <!-- Flash messages -->
                    <?php if (isset($_SESSION['flash']['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['flash']['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['flash']['error']); ?>
                    <?php endif; ?>

                    <!-- ✅ Preview: Danh sách sách trong cart -->
                    <?php if (!empty($books)): ?>
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase fw-semibold mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                            <i class="fas fa-books me-1"></i> Books in your cart
                        </h6>
                        <div class="border rounded overflow-hidden">
                            <?php foreach ($books as $idx => $book): ?>
                                <div class="d-flex align-items-center p-2 gap-3 <?= ($idx > 0) ? 'border-top' : '' ?> bg-light bg-opacity-50">
                                    <?php if (!empty($book['url'])): ?>
                                        <img src="<?= htmlspecialchars($book['url']) ?>" alt="Cover"
                                             style="height: 48px; width: auto; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded"
                                             style="height: 48px; width: 32px; font-size: 0.65rem;">IMG</div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-bold text-truncate" style="font-size: 0.9rem;">
                                            <?= htmlspecialchars($book['title']) ?>
                                        </div>
                                        <small class="text-muted"><?= htmlspecialchars($book['author']) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form action="<?= url('index.php?action=cart-checkout') ?>" method="POST">
                        <!-- Read-only: Thông tin user từ session (không gửi lên server) -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control bg-light"
                                   value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>"
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control bg-light"
                                   value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>"
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control bg-light"
                                   value="<?= htmlspecialchars($_SESSION['user']['phone'] ?? '') ?>"
                                   readonly>
                        </div>

                        <!-- Duy nhất field này được gửi lên server -->
                        <div class="mb-4">
                            <label for="return_day" class="form-label">
                                Return Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="return_day" name="return_day" required>
                            <div class="form-text">Select a return date within 3 weeks from today.</div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="<?= url('index.php?action=cart-list') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const returnDateInput = document.getElementById('return_day');

    // Min: hôm nay
    const today = new Date();
    returnDateInput.min = today.toISOString().split('T')[0];

    // Max: hôm nay + 21 ngày
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 21);
    returnDateInput.max = maxDate.toISOString().split('T')[0];
});
</script>