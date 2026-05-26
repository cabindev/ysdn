<?php
namespace App\Model;

use App\Database\Db;

class CategoryActivity extends Db {
    
    public function getAllCategories() {
        $sql = "SELECT * FROM category_activity";
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll();
        return $data;
    }

    public function getCategoryById($categoryId) {
        $sql = "SELECT * FROM category_activity WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $categoryId);
        $stmt->execute();
        $data = $stmt->fetch();
        return $data;
    }

    public function createCategory($name) {
        $sql = "INSERT INTO category_activity (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    public function updateCategory($categoryId, $name) {
        $sql = "UPDATE category_activity SET name = :name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $categoryId);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    public function deleteCategory($categoryId) {
        $sql = "DELETE FROM category_activity WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $categoryId);
        return $stmt->execute();
    }
}

?>
