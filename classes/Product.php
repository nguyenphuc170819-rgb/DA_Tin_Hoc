<?php
class Product {
    private $conn;
    private $table_products = "products";
    private $table_variants = "product_variants";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách sản phẩm mới nhất ra trang chủ
    public function getLatestProducts($limit = 8) {
        $query = "SELECT p.*, b.brand_name, c.category_name 
                  FROM " . $this->table_products . " p
                  LEFT JOIN brands b ON p.brand_id = b.brand_id
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.is_active = 1 
                  ORDER BY p.product_id DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết một đôi giày theo ID
    public function getProductById($product_id) {
        $query = "SELECT p.*, b.brand_name, c.category_name 
                  FROM " . $this->table_products . " p
                  LEFT JOIN brands b ON p.brand_id = b.brand_id
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.product_id = :product_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách các biến thể (Size, Màu sắc, Tồn kho) của đôi giày đó
    public function getVariantsByProductId($product_id) {
        $query = "SELECT * FROM " . $this->table_variants . " WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lấy danh sách sản phẩm theo Danh mục (Category)
    public function getProductsByCategory($category_id) {
        $query = "SELECT p.*, b.brand_name, c.category_name 
                  FROM " . $this->table_products . " p
                  LEFT JOIN brands b ON p.brand_id = b.brand_id
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.category_id = :category_id AND p.is_active = 1
                  ORDER BY p.product_id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách sản phẩm theo Thương hiệu (Brand)
    public function getProductsByBrand($brand_id) {
        $query = "SELECT p.*, b.brand_name, c.category_name 
                  FROM " . $this->table_products . " p
                  LEFT JOIN brands b ON p.brand_id = b.brand_id
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.brand_id = :brand_id AND p.is_active = 1
                  ORDER BY p.product_id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>