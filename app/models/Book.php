<?php

class Book
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ==================== CÁC HÀM CŨ (GIỮ NGUYÊN) ====================

    // Lấy tất cả sách
    public function getAllBooks()
    {
        $sql = "SELECT book_id, title, author, publisher, publish_year, description, url FROM Books";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Lấy sách theo phân trang
    public function getBooksPaginated($limit, $offset)
    {
        $sql = "SELECT book_id, title, author, publisher, publish_year, description, url 
                FROM Books 
                ORDER BY book_id DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số sách
    public function countTotalBooks()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM Books");
        $row = $stmt->fetch();
        return $row ? $row['total'] : 0;
    }

    // Tìm kiếm sách
    public function searchBooks($keyword)
    {
        $keyword = "%$keyword%";
        $sql = "SELECT book_id, title, author, publisher, publish_year, description, url 
                FROM Books 
                WHERE title LIKE :keyword OR author LIKE :keyword OR publisher LIKE :keyword";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => $keyword]);
        return $stmt->fetchAll();
    }

    // Lấy thông tin chi tiết sách
    public function getBookDetail($bookId)
    {
        $bookId = (int)$bookId;

        $sql = "
            SELECT 
                b.book_id,
                b.title,
                b.author,
                b.publisher,
                b.publish_year,
                b.description,
                b.url,
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

    // Lấy sách theo ID
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM Books WHERE book_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Thống kê trạng thái sách
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
                SET title = :title, 
                    author = :author, 
                    category_id = :category_id,
                    publisher = :publisher, 
                    publish_year = :publish_year, 
                    description = :description, 
                    url = :url
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
        // Kiểm tra trước nếu cần
        $checkSql = "SELECT COUNT(*) as total FROM Book_Items WHERE book_id = :id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([':id' => $id]);
        $result = $checkStmt->fetch();
        
        if ($result && $result['total'] > 0) {
            return false; // Không thể xóa vì còn bản sao
        }
        
        $sql = "DELETE FROM Books WHERE book_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Lấy tất cả categories để hiển thị trong form
     */
    public function getAllCategories()
    {
        $sql = "SELECT category_id, name FROM Categories ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ==================== CÁC HÀM MỚI CHO IMPORT CSV ====================

    /**
     * Kiểm tra sách đã tồn tại trong database chưa
     */
    public function bookExists($title, $author)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM Books 
                WHERE LOWER(TRIM(title)) = LOWER(TRIM(:title)) 
                AND LOWER(TRIM(author)) = LOWER(TRIM(:author))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':author' => $author
        ]);
        
        $result = $stmt->fetch();
        return $result && $result['total'] > 0;
    }

    /**
     * Lấy category_id từ tên category. Nếu không tồn tại, tạo mới.
     */
    public function getCategoryIdByName($categoryName)
    {
        if (empty($categoryName) || !is_string($categoryName)) {
            return null;
        }
        
        $trimmedCategoryName = trim($categoryName);
        if ($trimmedCategoryName === '') {
            return null;
        }

        // Search for existing category (case-insensitive and trim spaces)
        $sql = "SELECT category_id FROM Categories WHERE LOWER(TRIM(name)) = LOWER(TRIM(:name)) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $trimmedCategoryName]);
        $result = $stmt->fetch();

        if ($result) {
            // Return existing category ID
            return $result['category_id'];
        } else {
            // Category not found, create it
            try {
                $categoryModel = new Category();
                // Use the trimmed name for insertion
                if ($categoryModel->addCategory($trimmedCategoryName)) {
                    // Return the new ID
                    return $this->db->lastInsertId();
                }
            } catch (Exception $e) {
                // Log error if needed, for now return null to avoid breaking the whole import
                error_log('Failed to find or create category "' . $trimmedCategoryName . '": ' . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Validate dữ liệu sách từ CSV
     */
    private function validateBookData($bookData)
    {
        $errors = [];

        // Kiểm tra trường bắt buộc
        if (empty(trim($bookData['title']))) {
            $errors[] = "Tên sách không được để trống";
        }

        if (empty(trim($bookData['author']))) {
            $errors[] = "Tác giả không được để trống";
        }

        // Kiểm tra độ dài
        if (!empty($bookData['title']) && mb_strlen($bookData['title']) > 255) {
            $errors[] = "Tên sách quá dài (tối đa 255 ký tự)";
        }

        if (!empty($bookData['author']) && mb_strlen($bookData['author']) > 255) {
            $errors[] = "Tên tác giả quá dài (tối đa 255 ký tự)";
        }

        // Kiểm tra năm xuất bản
        if (!empty($bookData['publish_year'])) {
            $year = (int)$bookData['publish_year'];
            if ($year < 1000 || $year > date('Y') + 1) {
                $errors[] = "Năm xuất bản không hợp lệ (phải từ 1000 đến " . (date('Y') + 1) . ")";
            }
        }

        // Kiểm tra URL
        if (!empty($bookData['url'])) {
            if (!filter_var($bookData['url'], FILTER_VALIDATE_URL)) {
                $errors[] = "URL hình ảnh không hợp lệ";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Import nhiều sách từ mảng dữ liệu CSV
     */
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
            $this->db->beginTransaction();

            foreach ($booksData as $index => $bookData) {
                $rowNumber = $index + 2; // +2 vì row 1 là header, bắt đầu từ row 2

                // Validate dữ liệu
                $validation = $this->validateBookData($bookData);
                if (!$validation['valid']) {
                    $result['failed']++;
                    $result['errors'][] = "Dòng {$rowNumber}: " . implode(', ', $validation['errors']);
                    continue;
                }

                // Lấy category_id từ tên category nếu có
                if (!empty($bookData['category_name'])) {
                    $categoryId = $this->getCategoryIdByName($bookData['category_name']);
                    $bookData['category_id'] = $categoryId;
                } else {
                    $bookData['category_id'] = null;
                }

                // Thêm sách
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