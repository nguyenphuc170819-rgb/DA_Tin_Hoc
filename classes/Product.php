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
        $query = "SELECT p.*, b.brand_name, c.category_name,
                  (SELECT image_url FROM product_images WHERE product_images.product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image 
                  FROM " . $this->table_products . " p
                  LEFT JOIN brands b ON p.brand_id = b.brand_id
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.is_active = 1 
                  ORDER BY p.product_id DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // HÀM LẤY SẢN PHẨM BÁN CHẠY (Phục vụ cho trang spbc.php) ---
    public function getBestSellerProducts($limit = 8) {
        $query = "SELECT p.*, b.brand_name, c.category_name,
                  (SELECT image_url FROM product_images WHERE product_images.product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image,
                  COALESCE(SUM(oi.quantity), 0) as total_sold
                  FROM " . $this->table_products . " p
                  LEFT JOIN brands b ON p.brand_id = b.brand_id
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  LEFT JOIN product_variants pv ON p.product_id = pv.product_id
                  LEFT JOIN order_items oi ON pv.variant_id = oi.variant_id
                  WHERE p.is_active = 1 
                  GROUP BY p.product_id
                  ORDER BY total_sold DESC, p.product_id DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
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

    // Lấy danh sách hình ảnh theo product_id
    public function getImagesByProductId($product_id) {
        $query = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRelatedProducts($category_id, $current_product_id, $limit = 4) {
    $query = "SELECT p.*, b.brand_name, c.category_name, 
              (SELECT image_url FROM product_images WHERE product_images.product_id = p.product_id AND is_primary = 1 LIMIT 1) as image
              FROM " . $this->table_products . " p
              LEFT JOIN brands b ON p.brand_id = b.brand_id
              LEFT JOIN categories c ON p.category_id = c.category_id
              WHERE p.category_id = :category_id 
                AND p.product_id != :current_id 
                AND p.is_active = 1
              ORDER BY RAND() 
              LIMIT :limit";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindParam(':current_id', $current_product_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Đồng bộ trường base_price sang price nếu các view cũ của bạn đang gọi $item['price']
    foreach ($products as &$pro) {
        $pro['price'] = $pro['base_price'];
    }
    
    return $products;
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