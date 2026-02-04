<?php require_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="container mt-4">
    <h2>Edit Category</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=category-update&id=<?= $category['category_id'] ?>" method="POST">
        <div class="mb-3">
            <label class="form-label">Category Name *</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($category['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($category['description']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="index.php?action=category-list" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
