<?php
class Review {
    private $conn;
    private $table = "reviews";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Thêm đánh giá mới (số sao từ 1-5 và nội dung bình luận) cho sản phẩm
    public function addReview($product_id, $user_id, $rating, $comment) {
        $query = "INSERT INTO " . $this->table . " (product_id, user_id, rating, comment) 
                  VALUES (:product_id, :user_id, :rating, :comment)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lấy toàn bộ danh sách bình luận/đánh giá của một đôi giày (kèm tên khách hàng)
    public function getReviewsByProductId($product_id) {
        $query = "SELECT r.*, u.full_name 
                  FROM " . $this->table . " r
                  LEFT JOIN users u ON r.user_id = u.user_id
                  WHERE r.product_id = :product_id
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tính điểm đánh giá trung bình (ví dụ: 4.5/5 sao) và tổng số lượng đánh giá
    public function getAverageRating($product_id) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                  FROM " . $this->table . " 
                  WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>