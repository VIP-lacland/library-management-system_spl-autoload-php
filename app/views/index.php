<?php
// Sửa đường dẫn config: Từ app/views nhảy ra app/ rồi vào config/
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../config/config.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../../public/css/home.css">
    <link rel="stylesheet" href="../../public/css/header.css">
</head>

<?php 
// SỬA: Thêm dấu "/" trước "layouts" để tránh dính chữ (Ví dụ: views/layouts thay vì viewslayouts)
require_once __DIR__ . '/layouts/header.php'; 
?>

<body>


    <div class="container">
        <div class="row g-4 mt-3">
            <?php if (isset($books) && !empty($books)): ?>
                <?php foreach ($books as $book): ?>
                    <div class="col-md-3">
                        <div class="card h-100 shadow-sm border-0">

                            <div class="card-img-container">
                                <img src="<?= htmlspecialchars($book["url"]) ?>" class="card-img-top" alt="Book cover">
                            </div>

                            <div class="card-body d-flex flex-column">
                                <div class="card-content-box">
                                    <h5 class="card-title"><?= htmlspecialchars($book["title"]) ?></h5>
                                    <p class="card-author">Author: <?= htmlspecialchars($book["author"]) ?></p>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <a href="index.php?action=book-detail&id=<?= $book['book_id'] ?>" class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">There are no books in the library.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // JS Toggle Menu giữ nguyên của bạn
        function toggleMenu() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            const navMenu = document.getElementById('navMenu');
            if (navMenu && !event.target.closest('nav') && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
            }
        });
    </script>

    <?php require_once __DIR__ . '/layouts/footer.php'; ?>
</body>
</html>