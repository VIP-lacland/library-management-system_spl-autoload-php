<?php

class AdminCategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = $this->model('Category');
    }

    public function index() {
    $this->view('admin/dashboard');
    }

    // LIST
    public function categoryList()
    {
        $data['categories'] = $this->categoryModel->getAllCategories();
        $data['message']      = $this->getFlash('message');
        $data['message_type'] = $this->getFlash('message_type');
        $this->view('admin/category/category-list', $data);
    }

    // FORM CREATE + STORE (gộp)
    public function addCategory()
    {
        if ($this->isPost()) {
            $name        = trim($this->input('name'));
            $description = trim($this->input('description') ?? '');

            if (empty($name)) {
                $this->setFlash('message', 'Please enter a category name');
                $this->setFlash('message_type', 'error');
                $this->redirect(url('admin.php?url=category/addCategory'));
                return;
            }

            // Kiểm tra trùng tên (từ CategoryController cũ)
            if ($this->categoryModel->isNameExists($name)) {
                $this->setFlash('message', 'Category name already exists');
                $this->setFlash('message_type', 'error');
                $this->redirect(url('admin.php?url=category/addCategory'));
                return;
            }

            if ($this->categoryModel->addCategory($name, $description)) {
                $this->setFlash('message', 'Category added successfully');
                $this->setFlash('message_type', 'success');
                $this->redirect(url('admin.php?url=category/categoryList'));
                return;
            }

            $this->setFlash('message', 'Failed to add category');
            $this->setFlash('message_type', 'error');
            $this->redirect(url('admin.php?url=category/addCategory'));
            return;
        }

        // GET — hiển thị form
        $this->view('admin/category/add-category', [
            'message'      => $this->getFlash('message'),
            'message_type' => $this->getFlash('message_type')
        ]);
    }

    // FORM EDIT + UPDATE (gộp)
    public function editCategory()
    {
        $category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($category_id <= 0) {
            $this->setFlash('message', 'Invalid category ID');
            $this->setFlash('message_type', 'error');
            $this->redirect(url('admin.php?url=category/categoryList'));
            return;
        }

        $category = $this->categoryModel->getCategoryById($category_id);

        if (!$category) {
            $this->setFlash('message', 'Category not found');
            $this->setFlash('message_type', 'error');
            $this->redirect(url('admin.php?url=category/categoryList'));
            return;
        }

        if ($this->isPost()) {
            $name        = trim($this->input('name'));
            $description = trim($this->input('description') ?? '');

            if (empty($name)) {
                $this->setFlash('message', 'Category name is required');
                $this->setFlash('message_type', 'error');
                $this->redirect(url('admin.php?url=category/editCategory&id=' . $category_id));
                return;
            }

            // Kiểm tra trùng tên, bỏ qua chính nó (từ CategoryController cũ)
            if ($this->categoryModel->isNameExists($name, $category_id)) {
                $this->setFlash('message', 'Category name already exists');
                $this->setFlash('message_type', 'error');
                $this->redirect(url('admin.php?url=category/editCategory&id=' . $category_id));
                return;
            }

            if ($this->categoryModel->updateCategory($category_id, $name, $description)) {
                $this->setFlash('message', 'Category updated successfully');
                $this->setFlash('message_type', 'success');
                $this->redirect(url('admin.php?url=category/categoryList'));
                return;
            }

            $this->setFlash('message', 'Failed to update category');
            $this->setFlash('message_type', 'error');
            $this->redirect(url('admin.php?url=category/editCategory&id=' . $category_id));
            return;
        }

        // GET — hiển thị form edit
        $this->view('admin/category/edit-category', [
            'category'     => $category,
            'message'      => $this->getFlash('message'),
            'message_type' => $this->getFlash('message_type')
        ]);
    }

    // DELETE
    public function deleteCategory()
    {
        if (!$this->isPost()) {
            $this->redirect(url('admin.php?url=category/categoryList'));
            return;
        }

        $category_id = intval($this->input('category_id') ?? 0);

        if ($category_id <= 0) {
            $this->setFlash('message', 'Invalid category ID');
            $this->setFlash('message_type', 'error');
            $this->redirect(url('admin.php?url=category/categoryList'));
            return;
        }

        // Kiểm tra còn sách không (từ CategoryController cũ)
        if ($this->categoryModel->hasBooks($category_id)) {
            $this->setFlash('message', 'Cannot delete category that still has books');
            $this->setFlash('message_type', 'error');
            $this->redirect(url('admin.php?url=category/categoryList'));
            return;
        }

        if ($this->categoryModel->deleteCategory($category_id)) {
            $this->setFlash('message', 'Category deleted successfully');
            $this->setFlash('message_type', 'success');
        } else {
            $this->setFlash('message', 'Failed to delete category');
            $this->setFlash('message_type', 'error');
        }

        $this->redirect(url('admin.php?url=category/categoryList'));
    }
}