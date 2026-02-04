<?php

class BorrowingController extends Controller
{
    private $borrowModel;

    public function __construct()
    {
        // A robust role-based access control system is recommended for a real application.
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->setFlash('error', 'Access Denied: You do not have permission to access this page.');
            if (!isset($_SESSION['user'])) {
                $this->redirect('index.php?action=login');
            } else {
                $this->redirect('index.php');
            }
            exit;
        }
        $this->borrowModel = $this->model('Borrow');
    }

    public function listBorrowings()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $loans = $this->borrowModel->getLoansPaginated($limit, $offset, $keyword);
        $totalLoans = $this->borrowModel->countLoans($keyword);
        $totalPages = $totalLoans > 0 ? ceil($totalLoans / $limit) : 0;

        $this->view('admin/borrowing/borrowing-list', [
            'loans' => $loans,
            'title' => 'All Borrowing History',
            'keyword' => $keyword,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function requests()
    {
        $requests = $this->borrowModel->getAllLoans('pending');
        $this->view('admin/borrowing/pending', [
            'requests' => $requests,
            'title' => 'Borrow Requests',
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function overdue()
    {
        $overdueLoans = $this->borrowModel->getOverdueLoans();
        $this->view('admin/borrowing/overdue', [
            'overdueLoans' => $overdueLoans,
            'title' => 'Overdue Books',
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function approve()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($this->isGet() && $id > 0) {
            $dueDate = date('Y-m-d', strtotime('+14 days'));
            if ($this->borrowModel->approveRequest($id, $dueDate)) {
                $this->setFlash('success', 'Request approved successfully.');
            } else {
                $this->setFlash('error', 'Failed to approve request. It may have been processed already.');
            }
        }
        $this->redirect('admin.php?action=borrow-requests');
    }

    public function reject()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($this->isGet() && $id > 0) {
            if ($this->borrowModel->rejectRequest($id)) {
                $this->setFlash('success', 'Request rejected successfully.');
            } else {
                $this->setFlash('error', 'Failed to reject request. It may have been processed already.');
            }
        }
        $this->redirect('admin.php?action=borrow-requests');
    }

    public function returnBook()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($this->isGet() && $id > 0) {
            if ($this->borrowModel->returnBook($id)) {
                $this->setFlash('success', 'Book marked as returned successfully.');
            } else {
                $this->setFlash('error', 'Failed to return book. It might have been returned already.');
            }
        }

        // Redirect back to the page the admin came from
        $from = isset($_GET['from']) ? $_GET['from'] : 'list';
        $redirectAction = ($from === 'overdue') ? 'overdue-list' : 'borrow-list';
        $this->redirect('admin.php?action=' . $redirectAction);
    }
}