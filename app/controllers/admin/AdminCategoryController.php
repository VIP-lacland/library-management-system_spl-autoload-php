<?php

class AdminCategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = $this->model('Category');
    }

    public function categoryList()
    {
        if ($this->isPost()) {
            $this->redirect(url('admin.php?action=add-category'));
            return;
        }

        $data['categories'] = $this->categoryModel->getAllCategories();
        $data['message'] = $this->getFlash('message');
        $data['message_type'] = $this->getFlash('message_type');

        $this->view('admin/category/category-list', $data);
    }

    public function addCategory()
    {
        $data = [];
        if ($this->isPost()) {
            $name = $this->input('name');
            $data['name'] = $name;

            if (empty($name)) {
                $this->setFlash('message', 'Please enter a category name');
                $this->setFlash('message_type', 'error');
            } else {
                if ($this->categoryModel->addCategory($name)) {
                    $this->setFlash('message', 'Added category successfully');
                    $this->setFlash('message_type', 'success');
                    $this->redirect(url('admin.php?action=categoryList'));
                    return;
                } else {
                    $this->setFlash('message', 'Add failure category');
                    $this->setFlash('message_type', 'error');
                }
            }
        }
        $data['message'] = $this->getFlash('message');
        $data['message_type'] = $this->getFlash('message_type');

        $this->view('admin/category/add-category', $data);
    }


    public function deleteCategory() {
        if (!$this->isPost()) {
            $this->redirect(url('admin.php?action=categoryList'));
            return;
        }

        $category_id = $this->input('category_id');

        if (empty($category_id) || !is_numeric($category_id)) {
            $this->setFlash('message', 'Invalid category ID.');
            $this->setFlash('message_type', 'error');
            $this->redirect(url('admin.php?action=categoryList'));
            return;
        }

        if ($this->categoryModel->deleteCategory($category_id)) {
            $this->setFlash('message', 'Category deleted successfully.');
            $this->setFlash('message_type', 'success');
        } else {
            $this->setFlash('message', 'Cannot delete category. It may still contain books.');
            $this->setFlash('message_type', 'error');
        }

        $this->redirect(url('admin.php?action=categoryList'));
    }

    public function editCategory() {
        // Handle the GET request to show the edit form
        $category_id = isset($_GET['id']) ? $_GET['id'] : null;
        
        $category = $this->categoryModel->getCategoryById($category_id);
        
        if (!$category) {
            $this->setFlash('message', 'Category not found.');
            $this->setFlash('message_type', 'error');
            $this->redirect(url('admin.php?action=categoryList'));
            return;
        }
        
        // Xử lý khi submit form (POST request)
        if ($this->isPost()) {
            $name = trim($this->input('name'));
            
            // Validate
            if (empty($name)) {
                $this->setFlash('message', 'Category name is required.');
                $this->setFlash('message_type', 'error');
                // Redirect back to the edit form with the ID
                $this->redirect(url('admin.php?action=edit-category&id=' . $category_id));
                return;
            }
            
            // Update category
            if ($this->categoryModel->updateCategory($category_id, $name)) {
                $this->setFlash('message', 'Category updated successfully.');
                $this->setFlash('message_type', 'success');
                $this->redirect(url('admin.php?action=category-list'));
            } else {
                $this->setFlash('message', 'Failed to update category.');
                $this->setFlash('message_type', 'error');
                $this->redirect(url('admin.php?action=edit-category&id=' . $category_id));
            }
            return;
        }
        
        // Hiển thị form edit (GET request)
        $this->view('admin/category/edit-category', [
            'category' => $category,
            'title' => 'Edit Category',
            'message' => $this->getFlash('message'),
            'message_type' => $this->getFlash('message_type')
        ]);
    }
}



