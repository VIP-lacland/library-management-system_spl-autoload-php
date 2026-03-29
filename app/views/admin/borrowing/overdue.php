<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin_dashboard.css') ?>">
</head>

<body>
    <div class="d-flex" id="wrapper">
        <?php require_once __DIR__ . '/../components/sidebar.php'; ?>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                </div>
            </nav>
            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Overdue Books</h2>
                </div>
                <div class="mb-3">
                    <a href="admin.php?url=borrowing/listBorrowing" class="btn btn-outline-primary">All History</a>
                    <a href="admin.php?url=borrowing/requests" class="btn btn-outline-warning">Pending Requests</a>
                    <a href="admin.php?url=borrowing/overdue" class="btn btn-danger active">Overdue Books</a>
                </div>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Late Returns</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Book</th>
                                        <th>Borrowed Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($overdueLoans)): ?>
                                        <?php foreach ($overdueLoans as $loan): ?>
                                            <tr>
                                                <td>#<?= $loan['loan_id'] ?></td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($loan['user_name']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($loan['email']) ?></small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($loan['book_title']) ?></div>
                                                    <small class="text-muted">Barcode: <?= htmlspecialchars($loan['barcode']) ?></small>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($loan['borrow_date'])) ?></td>
                                                <td class="text-danger fw-bold"><?= date('d/m/Y', strtotime($loan['due_date'])) ?></td>
                                                <td><span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i>Overdue</span></td>
                                                <td>
                                                    <a href="admin.php?url=borrowing/returnBook&id=<?= $loan['loan_id'] ?>&from=overdue?>" class="btn btn-primary btn-sm" onclick="return confirm('Mark this book as returned?')"><i class="fas fa-undo"></i> Mark Returned</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">No overdue books found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }
        });
    </script>
</body>

</html>