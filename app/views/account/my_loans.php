<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Thêm khoảng cách giữa nội dung và footer */
    .borrowed-books-container {
        min-height: calc(100vh - 250px);
        padding-bottom: 60px;
    }
    
    /* Custom spacing cho card */
    .borrowed-books-card {
        margin-bottom: 40px;
    }
</style>

<div class="container mt-5 borrowed-books-container">
    <h2 class="mb-4"><i class="fa-solid fa-hand-holding-heart"></i> My Borrowed Books</h2>

    <?php if (isset($_SESSION['flash'])): ?>
        <?php 
            $msg = $_SESSION['flash']['success'] ?? $_SESSION['flash']['error'] ?? '';
            $type = isset($_SESSION['flash']['success']) ? 'success' : 'danger';
            if($msg):
        ?>
        <div class="alert alert-<?= $type ?>">
            <?= $msg; unset($_SESSION['flash']); ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow-sm borrowed-books-card">
        <div class="card-body">
            <?php if (!empty($loans)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Book</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($loan['title']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($loan['author']) ?></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($loan['borrow_date'])) ?></td>
                                    <td>
                                        <?= $loan['due_date'] ? date('d/m/Y', strtotime($loan['due_date'])) : '<span class="text-muted">--</span>' ?>
                                    </td>
                                    <td>
                                        <?= $loan['return_date'] ? date('d/m/Y', strtotime($loan['return_date'])) : '<span class="text-muted">--</span>' ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $statusClass = 'secondary';
                                            $statusText = ucfirst($loan['status']);
                                            switch($loan['status']) {
                                                case 'pending': $statusClass = 'warning text-dark'; break;
                                                case 'borrowing': $statusClass = 'primary'; break;
                                                case 'returned': $statusClass = 'success'; break;
                                                case 'overdue': $statusClass = 'danger'; break;
                                                case 'rejected': $statusClass = 'danger'; break;
                                            }
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-book-open fa-3x text-muted mb-3"></i>
                    <p class="lead">You haven't borrowed any books yet.</p>
                    <a href="<?= url('index.php') ?>" class="btn btn-primary">Browse Books</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>