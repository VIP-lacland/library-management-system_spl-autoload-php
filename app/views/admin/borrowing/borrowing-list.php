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
                    <h2><i class="fas fa-history me-2"></i>Borrowing History</h2>
                </div>

                <!-- Search Form -->
                <form action="admin.php" method="GET" class="row g-2 mb-4">
                    <input type="hidden" name="action" value="borrow-list">
                    <div class="col-md-8">
                        <input type="text" name="keyword" class="form-control"
                            placeholder="Search by user, email, book title or barcode..."
                            value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                    <div class="col-md-4"><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button> <?php if (!empty($keyword)): ?><a href="admin.php?action=borrow-list" class="btn btn-secondary ms-1">Reset</a><?php endif; ?></div>
                </form>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <a href="admin.php?action=borrow-list" class="btn btn-primary active">All History</a>
                        <a href="admin.php?action=borrow-requests" class="btn btn-outline-warning">Pending Requests</a>
                        <a href="admin.php?action=borrow-overdue" class="btn btn-outline-danger">Overdue Books</a>
                    </div>
                </div>
                <?php if (isset($_SESSION['flash']['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['flash']['success'];
                                                                                    unset($_SESSION['flash']['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['flash']['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['flash']['error'];
                                                                                unset($_SESSION['flash']['error']); ?></div>
                <?php endif; ?>
                <div class="card shadow-sm">
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
                                        <th>Return Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($loans)): ?>
                                        <?php foreach ($loans as $loan): ?>
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
                                                <td><?= date('d/m/Y', strtotime($loan['due_date'])) ?></td>
                                                <td><?= $loan['return_date'] ? date('d/m/Y', strtotime($loan['return_date'])) : '-' ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'secondary';
                                                    $statusIcon = 'fa-circle';
                                                    switch ($loan['status']) {
                                                        case 'borrowing':
                                                            $statusClass = 'primary';
                                                            $statusIcon = 'fa-book-reader';
                                                            break;
                                                        case 'returned':
                                                            $statusClass = 'success';
                                                            $statusIcon = 'fa-check-circle';
                                                            break;
                                                        case 'overdue':
                                                            $statusClass = 'danger';
                                                            $statusIcon = 'fa-exclamation-circle';
                                                            break;
                                                        case 'pending':
                                                            $statusClass = 'warning text-dark';
                                                            $statusIcon = 'fa-hourglass-half';
                                                            break;
                                                        case 'rejected':
                                                            $statusClass = 'secondary';
                                                            $statusIcon = 'fa-times-circle';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>">
                                                        <i class="fas <?= $statusIcon ?> me-1"></i><?= ucfirst($loan['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($loan['status'] == 'borrowing' || $loan['status'] == 'overdue'): ?>
                                                        <a href="admin.php?action=borrow-return&id=<?= $loan['loan_id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Confirm return book?')"><i class="fas fa-undo"></i> Return</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">No records found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="admin.php?action=borrow-list&page=<?= $currentPage - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="admin.php?action=borrow-list&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="admin.php?action=borrow-list&page=<?= $currentPage + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
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