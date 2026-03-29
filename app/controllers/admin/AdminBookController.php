<?php

class AdminBookController extends Controller
{
    private $bookModel;
    private $categoryModel;

    public function __construct()
    {
        $this->bookModel = $this->model('Book');
        $this->categoryModel = $this->model('Category');
    }

    public function adminBookList()
    {
        if ($this->isPost()) {
            $this->redirect(url('admin.php?url=book/adminBookList'));
            return;
        }

        $limit = 10;
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $totalBooks = $this->bookModel->countTotalBooks($keyword);
        $totalPages = $totalBooks > 0 ? ceil($totalBooks / $limit) : 1;

        if ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $limit;
        $books = $this->bookModel->getBooksPaginated($limit, $offset, $keyword);

        $data = [
            'books' => $books,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalBooks' => $totalBooks,
            'keyword' => $keyword,
            'message' => $this->getFlash('message'),
            'message_type' => $this->getFlash('message_type'),
            'title' => 'Book Management'
        ];

        $this->view('admin/books/book-management', $data);
    }

    public function addBook()
    {
        if (!$this->isPost()) {
            $this->view('admin/books/add-book', [
                'title' => 'Add New Book',
                'categories' => $this->categoryModel->getAllCategories(),
                'message' => $this->getFlash('message'),
                'message_type' => $this->getFlash('message_type')
            ]);
            return;
        }

        $bookData = $this->getBookDataFromPost();
        if (empty($bookData['title']) || empty($bookData['author'])) {
            $this->redirectWithError('Please fill in all required information', 'admin.php?url=book/addBook'); // ✅
            return;
        }

        if ($this->bookModel->addBook($bookData)) {
            $this->redirectWithSuccess('Book "' . htmlspecialchars($bookData['title']) . '" added successfully');
        } else {
            $this->redirectWithError('Unable to add book', 'admin.php?url=book/addBook');
        }
    }

    public function editBook()
    {
        $bookId = (int)($this->input('id') ?? $_GET['id'] ?? 0);
        if ($bookId <= 0) {
            $this->redirectWithError('Invalid book ID');
            return;
        }

        $book = $this->bookModel->getById($bookId);
        if (!$book) {
            $this->redirectWithError('Book does not exist');
            return;
        }

        if (!$this->isPost()) {
            $this->view('admin/books/edit-book', [
                'book' => $book,
                'book_id' => $bookId,
                'bookData' => $book,
                'categories' => $this->categoryModel->getAllCategories(),
                'title' => 'Edit Book: ' . $book['title'],
                'message' => $this->getFlash('message'),
                'message_type' => $this->getFlash('message_type')
            ]);
            return;
        }

        $bookData = $this->getBookDataFromPost();
        if (empty($bookData['title']) || empty($bookData['author'])) {
            $this->redirectWithError('Please fill in all required information', 'admin.php?url=book/editBook&id=' . $bookId);
            return;
        }

        if ($this->bookModel->updateBook($bookId, $bookData)) {
            $this->redirectWithSuccess('Book updated successfully');
        } else {
            $this->redirectWithError('Unable to update book', 'admin.php?url=book/editBook&id=' . $bookId);
        }
    }

    public function deleteBook()
    {
        if (!$this->isPost()) {
            $this->redirect(url('admin.php?url=book/adminBookList'));
            return;
        }

        $bookId = (int)($this->input('book_id') ?? 0);
        if ($bookId <= 0) {
            $this->redirectWithError('Invalid book ID');
            return;
        }

        $book = $this->bookModel->getById($bookId);
        if (!$book) {
            $this->redirectWithError('Book does not exist');
            return;
        }

        if ($this->bookModel->deleteBook($bookId)) {
            $this->redirectWithSuccess('Book "' . htmlspecialchars($book['title']) . '" deleted successfully');
        } else {
            $this->redirectWithError('Unable to delete book. Book still has copies');
        }
    }

    private function getBookDataFromPost()
    {
        return [
            'title' => trim($this->input('title') ?? ''),
            'author' => trim($this->input('author') ?? ''),
            'category_id' => !empty($this->input('category_id')) ? (int)$this->input('category_id') : null,
            'publisher' => trim($this->input('publisher') ?? ''),
            'publish_year' => trim($this->input('publish_year') ?? ''),
            'description' => trim($this->input('description') ?? ''),
            'url' => trim($this->input('url') ?? '')
        ];
    }

    private function redirectWithError($message, $url = 'admin.php?url=book/adminBookList')
    {
        $this->setFlash('message', $message);
        $this->setFlash('message_type', 'error');
        $this->redirect(url($url));
    }

    private function redirectWithSuccess($message, $url = 'admin.php?url=book/adminBookList')
    {
        $this->setFlash('message', $message);
        $this->setFlash('message_type', 'success');
        $this->redirect(url($url));
    }
}
