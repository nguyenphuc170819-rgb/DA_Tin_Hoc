<?php
class Category {
    private $conn;
    private $table_categories = "categories";
    private $table_brands = "brands";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả danh mục giày
    public function getAllCategories() {
        $query = "SELECT * FROM " . $this->table_categories . " ORDER BY category_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả thương hiệu giày (Nike, Adidas, Puma...)
    public function getAllBrands() {
        $query = "SELECT * FROM " . $this->table_brands . " ORDER BY brand_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function countCategories() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_categories;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
    // Lấy thông tin 1 danh mục theo ID (dùng cho trang edit.php)
    public function getCategoryById($category_id) {
        $query = "SELECT * FROM " . $this->table_categories . " WHERE category_id = :category_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm danh mục mới
    public function addCategory($category_name, $slug, $parent_id = null) {
        $query = "INSERT INTO " . $this->table_categories . " (category_name, slug, parent_id) VALUES (:category_name, :slug, :parent_id)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':category_name' => $category_name,
            ':slug' => $slug,
            ':parent_id' => empty($parent_id) ? null : $parent_id
        ]);
    }

    // Cập nhật danh mục
    public function updateCategory($category_id, $category_name, $slug, $parent_id = null) {
        $query = "UPDATE " . $this->table_categories . " SET category_name = :category_name, slug = :slug, parent_id = :parent_id WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':category_name' => $category_name,
            ':slug' => $slug,
            ':parent_id' => empty($parent_id) ? null : $parent_id,
            ':category_id' => $category_id
        ]);
    }

    // Xóa danh mục
    public function deleteCategory($category_id) {
        $query = "DELETE FROM " . $this->table_categories . " WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>