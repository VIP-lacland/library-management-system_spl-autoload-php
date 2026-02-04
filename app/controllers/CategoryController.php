<?php

class CategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        // Simple admin check. A real app should use a more robust role-based system.
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->setFlash('error', 'Access Denied: You do not have permission to access this page.');
            $this->redirect('index.php?action=login');
            exit;
        }
        $this->categoryModel = $this->model('Category');
    }

    /**
     * Display a list of all categories.
     */
    public function index()
    {
        $categories = $this->categoryModel->getAllCategories();
        $this->view('admin/category/category-list', [
            'categories' => $categories,
            'title' => 'Category Management',
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $this->view('admin/category/add-category', [
            'title' => 'Add New Category',
            'error' => $this->getFlash('error'),
            'old' => $this->getFlash('old')
        ]);
    }

    /**
     * Store a new category in the database.
     */
    public function store()
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?action=categories');
            return;
        }

        $name = trim($this->input('name') ?? '');
        $description = trim($this->input('description') ?? '');

        if (empty($name)) {
            $this->setFlash('error', 'Category name cannot be empty.');
            $this->setFlash('old', ['name' => $name, 'description' => $description]);
            $this->redirect('index.php?action=category-create');
            return;
        }

        if ($this->categoryModel->isNameExists($name)) {
            $this->setFlash('error', 'A category with this name already exists.');
            $this->setFlash('old', ['name' => $name, 'description' => $description]);
            $this->redirect('index.php?action=category-create');
            return;
        }

        if ($this->categoryModel->create($name, $description)) {
            $this->setFlash('success', 'Category created successfully.');
            $this->redirect('index.php?action=categories');
        } else {
            $this->setFlash('error', 'Failed to create category. Please try again.');
            $this->setFlash('old', ['name' => $name, 'description' => $description]);
            $this->redirect('index.php?action=category-create');
        }
    }

    /**
     * Show the form for editing a category.
     */
    public function edit()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'Invalid category ID.');
            $this->redirect('index.php?action=categories');
            return;
        }

        $category = $this->categoryModel->getById($id);

        if (!$category) {
            $this->setFlash('error', 'Category not found.');
            $this->redirect('index.php?action=categories');
            return;
        }

        $this->view('admin/category/edit-category', [
            'category' => $category,
            'title' => 'Edit Category',
            'error' => $this->getFlash('error')
        ]);
    }

    /**
     * Update a category in the database.
     */
    public function update()
    {
        if (!$this->isPost()) {
            $this->redirect('index.php?action=categories');
            return;
        }
        
        $id = (int)$this->input('id');
        $name = trim($this->input('name') ?? '');
        $description = trim($this->input('description') ?? '');

        if (empty($name)) {
            $this->setFlash('error', 'Category name cannot be empty.');
            $this->redirect('index.php?action=category-edit&id=' . $id);
            return;
        }
        
        if ($this->categoryModel->isNameExists($name, $id)) {
            $this->setFlash('error', 'Another category with this name already exists.');
            $this->redirect('index.php?action=category-edit&id=' . $id);
            return;
        }

        if ($this->categoryModel->update($id, $name, $description)) {
            $this->setFlash('success', 'Category updated successfully.');
            $this->redirect('index.php?action=categories');
        } else {
            $this->setFlash('error', 'Failed to update category. Please try again.');
            $this->redirect('index.php?action=category-edit&id=' . $id);
        }
    }

    /**
     * Delete a category from the database.
     */
    public function delete()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $this->setFlash('error', 'Invalid category ID.');
            $this->redirect('index.php?action=categories');
            return;
        }

        if ($this->categoryModel->hasBooks($id)) {
            $this->setFlash('error', 'Cannot delete category: Books are currently assigned to it.');
            $this->redirect('index.php?action=categories');
            return;
        }

        if ($this->categoryModel->delete($id)) {
            $this->setFlash('success', 'Category deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete category.');
        }
        $this->redirect('index.php?action=categories');
    }
}
