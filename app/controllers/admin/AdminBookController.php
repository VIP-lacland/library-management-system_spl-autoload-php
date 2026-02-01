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
            $this->redirect(url('admin.php?action=book-management'));
            return;
        }

        $limit = 10;
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($currentPage - 1) * $limit;

        if (!empty($keyword)) {
            $books = $this->bookModel->searchBooks($keyword);
            $totalBooks = count($books);
            $totalPages = ceil($totalBooks / $limit);
            if ($totalBooks > 0) {
                $books = array_slice($books, $offset, $limit);
            }
        } else {
            $books = $this->bookModel->getBooksPaginated($limit, $offset);
            $totalBooks = $this->bookModel->countTotalBooks();
            $totalPages = ceil($totalBooks / $limit);
        }

        if ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
            $redirectUrl = !empty($keyword)
                ? 'admin.php?action=book-management&page=' . $currentPage . '&keyword=' . urlencode($keyword)
                : 'admin.php?action=book-management&page=' . $currentPage;
            $this->redirect(url($redirectUrl));
            return;
        }

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
                'categories' => $this->categoryModel->getAllCategory(),
                'message' => $this->getFlash('message'),
                'message_type' => $this->getFlash('message_type')
            ]);
            return;
        }

        $bookData = $this->getBookDataFromPost();
        if (empty($bookData['title']) || empty($bookData['author'])) {
            $this->redirectWithError('Please fill in all required information', 'admin.php?action=add-book');
            return;
        }

        if ($this->bookModel->addBook($bookData)) {
            $this->redirectWithSuccess('Book "' . htmlspecialchars($bookData['title']) . '" added successfully');
        } else {
            $this->redirectWithError('Unable to add book', 'admin.php?action=add-book');
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
                'categories' => $this->categoryModel->getAllCategory(),
                'title' => 'Edit Book: ' . $book['title'],
                'message' => $this->getFlash('message'),
                'message_type' => $this->getFlash('message_type')
            ]);
            return;
        }

        $bookData = $this->getBookDataFromPost();
        if (empty($bookData['title']) || empty($bookData['author'])) {
            $this->redirectWithError('Please fill in all required information', 'admin.php?action=edit-book&id=' . $bookId);
            return;
        }

        if ($this->bookModel->updateBook($bookId, $bookData)) {
            $this->redirectWithSuccess('Book "' . htmlspecialchars($bookData['title']) . '" updated successfully');
        } else {
            $this->redirectWithError('Unable to update book', 'admin.php?action=edit-book&id=' . $bookId);
        }
    }

    public function deleteBook()
    {
        if (!$this->isPost()) {
            $this->redirect(url('admin.php?action=book-management'));
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

    private function redirectWithError($message, $url = 'admin.php?action=book-management')
    {
        $this->setFlash('message', $message);
        $this->setFlash('message_type', 'error');
        $this->redirect(url($url));
    }

    private function redirectWithSuccess($message, $url = 'admin.php?action=book-management')
    {
        $this->setFlash('message', $message);
        $this->setFlash('message_type', 'success');
        $this->redirect(url($url));
    }
}