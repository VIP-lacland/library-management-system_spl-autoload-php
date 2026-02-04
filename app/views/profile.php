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
</style>

<div class="container mt-5 mb-5">
    <div class="row g-5">
        <!-- User Profile Section (Left) -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100 rounded-lg">
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

        <!-- Loan History Section (Right) -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100 rounded-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa-solid fa-history me-2"></i>Lịch sử mượn sách</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive rounded-3">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên sách</th>
                                    <th>Ngày mượn</th>
                                    <th>Hạn trả</th>
                                    <th>Ngày trả</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($loans) && !empty($loans)): ?>
                                    <?php foreach ($loans as $loan): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($loan['book_title'] ?? 'N/A') ?></td>
                                            <td><?= date('d/m/Y', strtotime($loan['borrow_date'])) ?></td>
                                            <td><?= $loan['due_date'] ? date('d/m/Y', strtotime($loan['due_date'])) : '-' ?></td>
                                            <td><?= $loan['return_date'] ? date('d/m/Y', strtotime($loan['return_date'])) : '-' ?></td>
                                            <td>
                                                <?php
                                                $statusClass = 'secondary';
                                                $statusText = ucfirst($loan['status']);
                                                switch ($loan['status']) {
                                                    case 'borrowing': $statusClass = 'primary'; break;
                                                    case 'returned': $statusClass = 'success'; break;
                                                    case 'overdue': $statusClass = 'danger'; break;
                                                    case 'pending': $statusClass = 'warning text-dark'; break;
                                                    case 'rejected': $statusClass = 'secondary'; break;
                                                }
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Bạn chưa mượn cuốn sách nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>"><a class="page-link" href="index.php?action=profile&page=<?= $currentPage - 1 ?>">Previous</a></li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>"><a class="page-link" href="index.php?action=profile&page=<?= $i ?>"><?= $i ?></a></li>
                                <?php endfor; ?>
                                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>"><a class="page-link" href="index.php?action=profile&page=<?= $currentPage + 1 ?>">Next</a></li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>