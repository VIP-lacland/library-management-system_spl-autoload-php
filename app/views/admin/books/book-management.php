<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin_dashboard.css') ?>">
    <title>Book Management</title>
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
            
            <div class="container-fluid px-4">
                <h2 class="mb-2 mt-4">Book Management</h2>
                <p class="text-muted mb-4">View and manage books</p>

                <div class="container mt-4">
                    <!-- Flash Messages -->
                    <?php if (isset($data['message']) && $data['message']): ?>
                        <div class="alert alert-<?= $data['message_type'] === 'error' ? 'danger' : 'success' ?>">
                            <?= htmlspecialchars($data['message']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Search and Add -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="<?= url('admin.php') ?>" class="d-flex">
                               <input type="hidden" name="url" value="book/adminBookList">
                                <input type="text" name="keyword" class="form-control me-2" 
                                       placeholder="Search by title, author, publisher..." 
                                       value="<?= htmlspecialchars($data['keyword'] ?? '') ?>">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                                <?php if (!empty($keyword)): ?>
                                    <a href="<?= url('admin.php?url=book/adminBookList') ?>" class="btn btn-outline-secondary ms-2">Clear</a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <!-- NÚT IMPORT EXCEL -->
                            <a href="<?= url('admin.php?url=importBook/importBooks') ?>" class="btn btn-info">
                                <i class="fas fa-file-import"></i> Import Excel
                            </a>
                            <a href="<?= url('admin.php?url=book/addBook') ?>" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add New Book
                            </a>
                        </div>
                    </div>

                    <!-- Results Count -->
                    <?php if (isset($data['totalBooks'])): ?>
                        <div class="mb-3">
                            <p class="text-muted">
                                Found <?= $data['totalBooks'] ?> book<?= $data['totalBooks'] != 1 ? 's' : '' ?>
                                <?php if (!empty($data['keyword'])): ?>
                                    for "<strong><?= htmlspecialchars($data['keyword']) ?></strong>"
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Books Table -->
                    <?php if (empty($data['books'])): ?>
                        <div class="alert alert-info">
                            <?php if (!empty($data['keyword'])): ?>
                                No books found for "<?= htmlspecialchars($data['keyword']) ?>"
                            <?php else: ?>
                                No books found. <a href="<?= url('admin.php?url=book/addBook') ?>">Add your first book</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Cover</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Publisher</th>
                                    <th>Year</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['books'] as $book): ?>
                                    <tr>
                                        <td>#<?= $book['book_id'] ?></td>
                                        <td>
                                            <img src="<?= $book['url'] ? htmlspecialchars($book['url']) : url('public/images/no-image.png') ?>" 
                                                 width="50" height="70" style="object-fit: cover;" 
                                                 alt="<?= htmlspecialchars($book['title']) ?>">
                                        </td>
                                        <td><?= htmlspecialchars($book['title']) ?></td>
                                        <td><?= htmlspecialchars($book['author']) ?></td>
                                        <td><?= htmlspecialchars($book['publisher'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($book['publish_year'] ?? 'N/A') ?></td>
                                        <td>
                                            <a href="<?= url('admin.php?url=book/editBook&id=' . $book['book_id']) ?>" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="<?= url('admin.php?url=book/deleteBook') ?>" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this book?')">
                                                <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <?php if (isset($data['totalPages']) && $data['totalPages'] > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <li class="page-item <?= ($data['currentPage'] <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" 
                                       href="admin.php?url=book/adminBookList&page=<?= $currentPage - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>

                                <!-- Page Numbers -->
                                <?php 
                                $startPage = max(1, $data['currentPage'] - 2);
                                $endPage = min($data['totalPages'], $data['currentPage'] + 2);

                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="admin.php?url=book/adminBookList&page=1<?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= ($i == $data['currentPage']) ? 'active' : '' ?>">
                                        <a class="page-link" 
                                           href="admin.php?url=book/adminBookList&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($endPage < $data['totalPages']): ?>
                                    <?php if ($endPage < $data['totalPages'] - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" 
                                           href="admin.php?url=book/adminBookList&page=<?= $totalPages ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">
                                            <?= $totalPages ?>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next Button -->
                                <li class="page-item <?= ($data['currentPage'] >= $data['totalPages']) ? 'disabled' : '' ?>">
                                    <a class="page-link" 
                                       href="admin.php?url=book/adminBookList&page=<?= $currentPage + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>

                            <!-- Page Info -->
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Page <?= $data['currentPage'] ?> of <?= $data['totalPages'] ?>
                                    | Showing <?= count($data['books']) ?> of <?= $data['totalBooks'] ?> books
                                </small>
                            </div>
                        </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }

            // Auto-hide flash messages after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const fadeOut = () => {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 0.5s';
                        setTimeout(() => alert.remove(), 500);
                    };
                    fadeOut();
                });
            }, 5000);
        });
    </script>
</body>
</html>