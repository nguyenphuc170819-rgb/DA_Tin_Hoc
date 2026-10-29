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
    //Đếm sl sản phẩm
    public function countProducts() {
        return $this->conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }  
    // Lấy toàn bộ sản phẩm cho trang quản trị Admin (admin/product/index.php)
    public function getAllProductsAdmin() {
        $query = "SELECT p.*, c.category_name, b.brand_name 
                  FROM " . $this->table_products . " p
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  LEFT JOIN brands b ON p.brand_id = b.brand_id
                  ORDER BY p.product_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm kiếm sản phẩm theo tên (cho trang module/product/search.php)
    public function searchProducts($keyword) {
        $query = "SELECT p.*, b.brand_name, c.category_name 
                  FROM " . $this->table_products . " p
                  LEFT JOIN brands b ON p.brand_id = b.brand_id
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.product_name LIKE :keyword AND p.is_active = 1
                  ORDER BY p.product_id DESC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%" . $keyword . "%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm sản phẩm mới (cho admin/product/add.php)
    public function addProduct($data) {
        $query = "INSERT INTO " . $this->table_products . " (product_name, slug, brand_id, category_id, description, base_price, is_active) 
                  VALUES (:product_name, :slug, :brand_id, :category_id, :description, :base_price, :is_active)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':product_name' => $data['product_name'],
            ':slug' => $data['slug'],
            ':brand_id' => $data['brand_id'],
            ':category_id' => $data['category_id'],
            ':description' => $data['description'],
            ':base_price' => $data['base_price'],
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    // Xóa sản phẩm (cho admin/product/delete.php)
    public function deleteProduct($product_id) {
        $query = "DELETE FROM " . $this->table_products . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        return $stmt->execute();
    } 
}
?>