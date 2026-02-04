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
                    <h2><i class="fas fa-clipboard-list me-2"></i>Borrow Requests</h2>
                </div>
                <div class="mb-3">
                    <a href="admin.php?action=borrow-list" class="btn btn-outline-primary">All History</a>
                    <a href="admin.php?action=borrow-requests" class="btn btn-warning active text-dark">Pending
                        Requests</a>
                    <a href="admin.php?action=borrow-overdue" class="btn btn-outline-danger">Overdue Books</a>
                </div>
                <?php if (isset($_SESSION['flash']['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['flash']['success'];
                        unset($_SESSION['flash']['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['flash']['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['flash']['error'];
                        unset($_SESSION['flash']['error']); ?></div>
                <?php endif; ?>
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Pending Approvals</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Book</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($requests)): ?>
                                        <?php foreach ($requests as $req): ?>
                                            <tr>
                                                <td>#<?= $req['loan_id'] ?></td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($req['user_name']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($req['email']) ?></small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($req['book_title']) ?></div>
                                                    <small class="text-muted">Barcode:
                                                        <?= htmlspecialchars($req['barcode']) ?></small>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($req['borrow_date'])) ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="admin.php?action=borrow-approve&id=<?= $req['loan_id'] ?>"
                                                            class="btn btn-success btn-sm"
                                                            onclick="return confirm('Approve this request?')"><i
                                                                class="fas fa-check"></i> Approve</a>
                                                        <a href="admin.php?action=borrow-reject&id=<?= $req['loan_id'] ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Reject this request?')"><i
                                                                class="fas fa-times"></i> Reject</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No pending requests found.</td>
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