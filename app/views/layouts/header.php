<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/home.css') ?>">

</head>
<body>
    <header>
        <!-- Top Header -->
        <div class="header-top">
            <a href="<?= url('index.php?action=index') ?>" class="logo">
                <i class="fa-solid fa-book-open"></i>
                <span>Library System</span>
            </a>

            <!-- Search Form -->
            <form action="<?= url('index.php') ?>" method="GET" class="search-container">
                <input type="hidden" name="action" value="index">
                <input type="text" name="keyword" class="search-bar" placeholder="Search books, authors..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-menu">
                    <?php if ($_SESSION['user']['role'] === 'reader'): ?>
                        <span class="username">
                            <i class="fa-solid fa-user"></i>
                            Hello, <?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>
                        </span>
                    <?php endif; ?>
                    <a href="<?= url('index.php?url=auth/logout') ?>" class="logout-btn">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Logout
                    </a>
                </div>
            <?php else: ?>
                <a href="<?= url('index.php?url=auth/loginForm') ?>" class="login-btn">
                <!-- <a href="<?= url('index.php?action=login') ?>" class="login-btn"> -->
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Login
                </a>
            <?php endif; ?>
        </div>

        <!-- Navigation Bar -->
        <nav>
            <div class="nav-container">
                <button class="menu-toggle" onclick="toggleMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                
                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item">
                        <a href="<?= url('index.php?url=book/index') ?>" class="nav-link">
                            <i class="fa-solid fa-house"></i>
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= url('index.php?url=book/index') ?>" class="nav-link">
                            <i class="fa-solid fa-book"></i>
                            Books
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= url('index.php?url=cart/index') ?>" class="nav-link">
                            <i class="fa-solid fa-cart-shopping"></i>
                            Cart
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?= count($_SESSION['cart']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= url('index.php?url=book/index') ?>" class="nav-link">
                            <i class="fa-solid fa-tags"></i>
                            Categories
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fa-solid fa-user-circle"></i>
                            Profile
                            <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="dropdown-content">
                            <a href="<?= url('index.php?url=profile/showProfile') ?>"><i class="fa-solid fa-user"></i> My Profile</a>
                            <a href="<?= url('index.php?url=account/myLoans') ?>"><i class="fa-solid fa-hand-holding-heart"></i> My Borrowed Books</a>
                            <a href="<?= url('index.php?url=account/changePassword') ?>"><i class="fa-solid fa-key"></i> Change Password</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>