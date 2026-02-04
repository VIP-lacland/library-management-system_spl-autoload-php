<?php

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // =========================
    // READ ALL + COUNT BOOKS
    // =========================
    public function getAllCategories()
    {
        $sql = "
            SELECT 
                c.category_id,
                c.name,
                COUNT(b.book_id) AS total_books
            FROM Categories c
            LEFT JOIN Books b ON c.category_id = b.category_id
            GROUP BY c.category_id, c.name
            ORDER BY c.name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function addCategory($name) {
        $sql = "INSERT INTO Categories (name) VALUES (:name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        return $stmt->execute();
    }


    function deleteCategory($category_id) {
        // First, check if any books are associated with this category
        $checkSql = "SELECT COUNT(*) as book_count FROM Books WHERE category_id = :category_id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // If books are found, prevent deletion
        if ($result && $result['book_count'] > 0) {
            return false;
        }

        // If no books are associated, proceed with deletion
        $sql = "DELETE FROM Categories WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        return $stmt->execute();
    }


    function getCategoryById($category_id) {
        $sql = "SELECT * FROM Categories WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function updateCategory($category_id, $name) {
        $sql = "UPDATE Categories SET name = :name WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        return $stmt->execute();
    }



    // =========================
    // GET BY ID
    // =========================
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM Categories WHERE category_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // =========================
    // CHECK NAME EXISTS (CREATE + UPDATE)
    // =========================
    public function isNameExists($name, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) FROM Categories WHERE name = :name AND category_id != :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['name' => $name, 'id' => $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) FROM Categories WHERE name = :name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['name' => $name]);
        }
        return $stmt->fetchColumn() > 0;
    }

    // =========================
    // CREATE
    // =========================
    public function create($name)
    {
        $sql = "INSERT INTO Categories (name) VALUES (:name)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'name' => $name
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update($id, $name)
    {
        $sql = "
            UPDATE Categories 
            SET name = :name
            WHERE category_id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'name' => $name
        ]);
    }

    // =========================
    // CHECK HAS BOOKS
    // =========================
    public function hasBooks($categoryId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Books WHERE category_id = :id");
        $stmt->execute(['id' => $categoryId]);
        return $stmt->fetchColumn() > 0;
    }

    // =========================
    // DELETE
    // =========================
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM Categories WHERE category_id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
