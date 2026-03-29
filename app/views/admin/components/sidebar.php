<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '../../../../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="border-end bg-dark" id="sidebar-wrapper">
        <div class="sidebar-heading text-white"><i class="fas fa-book-reader me-2"></i>LMS Admin</div>
        <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action p-3 <?= $act === 'dashboard' ? 'active' : '' ?>" href="admin.php?url=category/index">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a class="list-group-item list-group-item-action p-3" href="admin.php?url=book/adminBookList">
                <i class="fas fa-book me-2"></i> Book Management
            </a>
            <a class="list-group-item list-group-item-action p-3" href="admin.php?url=category/categoryList">
                <i class="fas fa-list me-2"></i> Category
            </a>
            <!-- Active nếu action bắt đầu bằng 'borrow' (borrow-list, borrow-requests,...) -->
            <a class="list-group-item list-group-item-action p-3 <?= strpos($act, 'borrow') === 0 ? 'active' : '' ?>" href="admin.php?url=borrowing/listBorrowing">
                <i class="fas fa-exchange-alt me-2"></i> Borrow & Return
            </a>
            <a class="list-group-item list-group-item-action p-3" href="admin.php?url=user/getAllUser">
                <i class="fas fa-users me-2"></i> User
            </a>
            <a class="list-group-item list-group-item-action p-3 mt-4 border-top border-secondary" href="index.php?url=logout">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </div>
</body>

</html>
