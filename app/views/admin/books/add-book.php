<?php
require_once __DIR__ . '../../../../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin_dashboard.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <title><?= $title ?? 'Add New Book' ?></title>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <?php require_once __DIR__ . '/../components/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </nav>
            
            <div class="container-fluid px-4 py-4">
                <?php if (isset($message) && $message): ?>
                    <div class="alert alert-<?= $message_type === 'error' ? 'danger' : 'success' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <div class="book-form-container">
                    <h2 class="book-form-title">
                        <i class="fas fa-book me-2"></i>
                        <?= $title ?? 'Add New Book' ?>
                    </h2>
                    
                    <form method="POST" action="index.php?url=<?= isset($book_id) ? 'book/editBook&id=' . $book_id : 'book/addBook' ?>">
                        <!-- Title -->
                        <div class="form-group">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" class="form-control" 
                                   value="<?= htmlspecialchars($bookData['title'] ?? '') ?>"
                                   required maxlength="100">
                            <div class="form-text">Book title (max 100 characters)</div>
                        </div>

                        <!-- Author -->
                        <div class="form-group">
                            <label class="form-label">Author *</label>
                            <input type="text" name="author" class="form-control" 
                                   value="<?= htmlspecialchars($bookData['author'] ?? '') ?>"
                                   required maxlength="100">
                            <div class="form-text">Author name (max 100 characters)</div>
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">-- Select Category --</option>
                                <?php if (isset($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>"
                                            <?= (isset($bookData['category_id']) && $bookData['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Publisher & Year -->
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="form-label">Publisher</label>
                                <input type="text" name="publisher" class="form-control" 
                                       value="<?= htmlspecialchars($bookData['publisher'] ?? '') ?>"
                                       maxlength="100">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Publish Year</label>
                                <input type="number" name="publish_year" class="form-control" 
                                       value="<?= htmlspecialchars($bookData['publish_year'] ?? '') ?>"
                                       min="1000" max="<?= date('Y') ?>">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"
                                      maxlength="65535"><?= htmlspecialchars($bookData['description'] ?? '') ?></textarea>
                        </div>

                        <!-- Image URL -->
                        <div class="form-group">
                            <label class="form-label">Cover Image URL</label>
                            <input type="text" name="url" class="form-control" 
                                   value="<?= htmlspecialchars($bookData['url'] ?? '') ?>"
                                   placeholder="https://example.com/image.jpg"
                                   maxlength="250">
                            <div class="form-text">Optional image URL (max 250 characters)</div>
                            <div class="img-preview-container">
                                <?php if (!empty($bookData['url'])): ?>
                                    <img src="<?= htmlspecialchars($bookData['url']) ?>" 
                                         class="img-preview active"
                                         onerror="this.classList.remove('active')">
                                <?php else: ?>
                                    <img src="" class="img-preview">
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="index.php?url=book/adminBookList" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> 
                                <?= isset($book_id) ? 'Update Book' : 'Save Book' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }

            // Image preview
            const urlInput = document.querySelector('input[name="url"]');
            const imgPreview = document.querySelector('.img-preview');
            
            if (urlInput && imgPreview) {
                urlInput.addEventListener('input', function() {
                    if (this.value && this.value.startsWith('http')) {
                        imgPreview.src = this.value;
                        imgPreview.classList.add('active');
                        imgPreview.onerror = () => imgPreview.classList.remove('active');
                    } else {
                        imgPreview.classList.remove('active');
                    }
                });
            }

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
        });
    </script>
</body>
</html>