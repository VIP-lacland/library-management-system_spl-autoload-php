﻿<?php

class Book
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Lấy tất cả sách
    public function getAllBooks()
    {
        $sql = "SELECT book_id, title, author, publisher, publish_year, description, url FROM Books";
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

}
