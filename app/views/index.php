<?php require_once __DIR__ . '/layouts/header.php'; ?>

<?php
// Logic tìm kiếm cho User
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if (!empty($keyword)) {
    // Sử dụng Database singleton để lấy kết nối thay vì global $conn
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT b.*, c.name AS category_name 
            FROM Books b 
            LEFT JOIN Categories c ON b.category_id = c.category_id
            WHERE b.title LIKE :kw1 
               OR b.author LIKE :kw2 
               OR b.publisher LIKE :kw3 
               OR c.name LIKE :kw4";

    $stmt = $db->prepare($sql);
    $searchParam = "%" . $keyword . "%";
    $stmt->bindValue(':kw1', $searchParam);
    $stmt->bindValue(':kw2', $searchParam);
    $stmt->bindValue(':kw3', $searchParam);
    $stmt->bindValue(':kw4', $searchParam);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="container">
    <div class="row g-4 mt-3">
        <?php if (isset($books) && !empty($books)): ?>
            <?php foreach ($books as $book): ?>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">

                        <div class="card-img-container">
                            <img src="<?= $book["url"] ?>" class="card-img-top" alt="Book cover">
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="card-content-box">
                                <h5 class="card-title"><?= htmlspecialchars($book["title"]) ?></h5>
                                <p class="card-author">Author: <?= htmlspecialchars($book["author"]) ?></p>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <a href="<?= url('index.php?action=book-detail&id=' . $book['book_id']) ?>" class="btn btn-primary w-100">View Details</a>
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
</main>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>