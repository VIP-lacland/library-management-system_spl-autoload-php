<?php

class Book
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ==================== CÁC HÀM CŨ (GIỮ NGUYÊN) ====================

    public function getAllBooks()
    {
        $sql = "SELECT b.book_id, b.title, b.author, b.publisher, b.publish_year, b.description, b.url,
                (SELECT COUNT(*) FROM Book_Items bi WHERE bi.book_id = b.book_id AND bi.status NOT IN ('borrowed', 'lost', 'damaged', 'maintenance', 'Borrowed', 'Lost', 'Damaged')) as available_count
                FROM Books b";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy sách theo phân trang
    public function getBooksPaginated($limit, $offset, $keyword = '')
    {
        $sql = "SELECT b.book_id, b.title, b.author, b.publisher, b.publish_year, b.description, b.url, c.name as category_name
                FROM Books b
                LEFT JOIN Categories c ON b.category_id = c.category_id";
        
        if (!empty($keyword)) {
            $sql .= " WHERE b.title LIKE :kw1 OR b.author LIKE :kw2 OR b.publisher LIKE :kw3 OR c.name LIKE :kw4";
        }

        $sql .= " ORDER BY b.book_id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        if (!empty($keyword)) {
            $searchParam = "%$keyword%";
            $stmt->bindValue(':kw1', $searchParam);
            $stmt->bindValue(':kw2', $searchParam);
            $stmt->bindValue(':kw3', $searchParam);
            $stmt->bindValue(':kw4', $searchParam);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số sách (có thể tìm kiếm)
    public function countTotalBooks($keyword = '')
    {
        $sql = "SELECT COUNT(b.book_id) as total FROM Books b LEFT JOIN Categories c ON b.category_id = c.category_id";
        if (!empty($keyword)) {
            $sql .= " WHERE b.title LIKE :kw1 OR b.author LIKE :kw2 OR b.publisher LIKE :kw3 OR c.name LIKE :kw4";
        }
        $stmt = $this->db->prepare($sql);
        if (!empty($keyword)) {
            $searchParam = "%$keyword%";
            $stmt->bindValue(':kw1', $searchParam);
            $stmt->bindValue(':kw2', $searchParam);
            $stmt->bindValue(':kw3', $searchParam);
            $stmt->bindValue(':kw4', $searchParam);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? $row['total'] : 0;
    }

    // Lấy thông tin chi tiết sách
    public function searchBooks($keyword)
    {
        $keyword = "%$keyword%";
        $sql = "SELECT b.book_id, b.title, b.author, b.publisher, b.publish_year, b.description, b.url,
                (SELECT COUNT(*) FROM Book_Items bi WHERE bi.book_id = b.book_id AND bi.status NOT IN ('borrowed', 'lost', 'damaged', 'maintenance', 'Borrowed', 'Lost', 'Damaged')) as available_count
                FROM Books b 
                WHERE b.title LIKE :keyword OR b.author LIKE :keyword OR b.publisher LIKE :keyword";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => $keyword]);
        return $stmt->fetchAll();
    }

    public function getBookDetail($bookId)
    {
        $bookId = (int)$bookId;
        $sql = "
            SELECT 
                b.book_id, b.title, b.author, b.publisher,
                b.publish_year, b.description, b.url,
                c.name AS category_name
            FROM Books b
            LEFT JOIN Categories c ON b.category_id = c.category_id
            WHERE b.book_id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch();
        return $book ? $book : null;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM Books WHERE book_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getBookItemsStatus($bookId)
    {
        $bookId = (int)$bookId;
        $sql = "
            SELECT status, COUNT(*) as total
            FROM Book_Items
            WHERE book_id = :id
            GROUP BY status
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $bookId]);
        return $stmt->fetchAll();
    }

    public function addBook($data)
    {
        $sql = "INSERT INTO Books (title, author, category_id, publisher, publish_year, description, url) 
                VALUES (:title, :author, :category_id, :publisher, :publish_year, :description, :url)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':author' => $data['author'],
            ':category_id' => $data['category_id'] ?? null,
            ':publisher' => $data['publisher'] ?? null,
            ':publish_year' => $data['publish_year'] ?? null,
            ':description' => $data['description'] ?? null,
            ':url' => $data['url'] ?? null
        ]);
    }

    public function updateBook($id, $data)
    {
        $sql = "UPDATE Books 
                SET title = :title, author = :author, category_id = :category_id,
                    publisher = :publisher, publish_year = :publish_year, 
                    description = :description, url = :url
                WHERE book_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':author' => $data['author'],
            ':category_id' => $data['category_id'] ?? null,
            ':publisher' => $data['publisher'] ?? null,
            ':publish_year' => $data['publish_year'] ?? null,
            ':description' => $data['description'] ?? null,
            ':url' => $data['url'] ?? null,
            ':id' => $id
        ]);
    }

    public function deleteBook($id)
    {
        $checkSql = "SELECT COUNT(*) as total FROM Book_Items WHERE book_id = :id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([':id' => $id]);
        $result = $checkStmt->fetch();
        
        if ($result && $result['total'] > 0) {
            return false;
        }
        
        $sql = "DELETE FROM Books WHERE book_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getAllCategories()
    {
        $sql = "SELECT category_id, name FROM Categories ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ==================== CÁC HÀM MỚI CHO IMPORT CSV ====================

    private function resetAutoIncrement()
    {
        $stmt = $this->db->query("SELECT COALESCE(MAX(book_id), 0) + 1 AS next_id FROM Books");
        $row = $stmt->fetch();
        $nextId = (int)$row['next_id'];
        $this->db->exec("ALTER TABLE Books AUTO_INCREMENT = " . $nextId);
    }

    public function bookExists($title, $author)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM Books 
                WHERE LOWER(TRIM(title)) = LOWER(TRIM(:title)) 
                AND LOWER(TRIM(author)) = LOWER(TRIM(:author))";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':title' => $title, ':author' => $author]);
        $result = $stmt->fetch();
        return $result && $result['total'] > 0;
    }

    public function getCategoryIdByName($categoryName)
    {
        if (empty($categoryName) || !is_string($categoryName)) {
            return null;
        }
        
        $trimmedCategoryName = trim($categoryName);
        if ($trimmedCategoryName === '') {
            return null;
        }

        $sql = "SELECT category_id FROM Categories WHERE LOWER(TRIM(name)) = LOWER(TRIM(:name)) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $trimmedCategoryName]);
        $result = $stmt->fetch();

        if ($result) {
            return $result['category_id'];
        } else {
            try {
                $categoryModel = new Category();
                if ($categoryModel->addCategory($trimmedCategoryName)) {
                    return $this->db->lastInsertId();
                }
            } catch (Exception $e) {
                error_log('Failed to find or create category "' . $trimmedCategoryName . '": ' . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Validate dữ liệu sách từ CSV
     * URL chấp nhận: http/https URL hoặc relative path (../../public/images/...)
     */
    private function validateBookData($bookData)
    {
        $errors = [];

        if (empty(trim($bookData['title']))) {
            $errors[] = "Tên sách không được để trống";
        }

        if (empty(trim($bookData['author']))) {
            $errors[] = "Tác giả không được để trống";
        }

        if (!empty($bookData['title']) && mb_strlen($bookData['title']) > 255) {
            $errors[] = "Tên sách quá dài (tối đa 255 ký tự)";
        }

        if (!empty($bookData['author']) && mb_strlen($bookData['author']) > 255) {
            $errors[] = "Tên tác giả quá dài (tối đa 255 ký tự)";
        }

        if (!empty($bookData['publish_year'])) {
            $year = (int)$bookData['publish_year'];
            if ($year < 1000 || $year > date('Y') + 1) {
                $errors[] = "Năm xuất bản không hợp lệ (phải từ 1000 đến " . (date('Y') + 1) . ")";
            }
        }

        // Validate URL: chấp nhận http/https URL hoặc relative path
        if (!empty($bookData['url'])) {
            $url = trim($bookData['url']);
            $isValidUrl = filter_var($url, FILTER_VALIDATE_URL);           // http(s)://...
            $isRelativePath = preg_match('/^\.\.\//', $url)                 // ../...
                           || preg_match('/^\//', $url)                     // /...
                           || preg_match('/^[a-zA-Z0-9_\-\.\/]+\.(jpg|jpeg|png|gif|webp|svg)$/i', $url); // filename.jpg

            if (!$isValidUrl && !$isRelativePath) {
                $errors[] = "URL hình ảnh không hợp lệ";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function importBooks($booksData)
    {
        $result = [
            'total' => count($booksData),
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        try {
            $this->resetAutoIncrement();
            $this->db->beginTransaction();

            foreach ($booksData as $index => $bookData) {
                $rowNumber = $index + 2;

                $validation = $this->validateBookData($bookData);
                if (!$validation['valid']) {
                    $result['failed']++;
                    $result['errors'][] = "Dòng {$rowNumber}: " . implode(', ', $validation['errors']);
                    continue;
                }

                if (!empty($bookData['category_name'])) {
                    $categoryId = $this->getCategoryIdByName($bookData['category_name']);
                    $bookData['category_id'] = $categoryId;
                } else {
                    $bookData['category_id'] = null;
                }

                try {
                    if ($this->addBook($bookData)) {
                        $result['success']++;
                    } else {
                        $result['failed']++;
                        $result['errors'][] = "Dòng {$rowNumber}: Lỗi khi thêm sách '{$bookData['title']}'";
                    }
                } catch (Exception $e) {
                    $result['failed']++;
                    $result['errors'][] = "Dòng {$rowNumber}: Lỗi database - " . $e->getMessage();
                }
            }

            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollBack();
            $result['errors'][] = 'Lỗi hệ thống: ' . $e->getMessage();
        }

        return $result;
    }
}
?>