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
                c.description,
                COUNT(b.book_id) AS total_books
            FROM Categories c
            LEFT JOIN Books b ON c.category_id = b.category_id
            GROUP BY c.category_id, c.name, c.description
            ORDER BY c.name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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
    public function create($name, $description)
    {
        $sql = "INSERT INTO Categories (name, description) VALUES (:name, :description)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'name' => $name,
            'description' => $description
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update($id, $name, $description)
    {
        $sql = "
            UPDATE Categories 
            SET name = :name,
                description = :description
            WHERE category_id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description
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
