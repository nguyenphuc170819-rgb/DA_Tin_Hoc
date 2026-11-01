<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo đơn hàng mới từ giỏ hàng
    public function createOrder($user_id, $shipping_info, $cart_items, $total_amount, $discount_amount, $final_amount) {
        try {
            // Bắt đầu Transaction (Đảm bảo an toàn dữ liệu: lỗi bước nào rollback bước đó)
            $this->conn->beginTransaction();

            // 1. Lưu thông tin chung vào bảng orders
            $query_order = "INSERT INTO orders (user_id, shipping_full_name, shipping_phone, shipping_address, total_amount, discount_amount, final_amount, order_status, payment_status) 
                            VALUES (:user_id, :full_name, :phone, :address, :total, :discount, :final, 'pending', 'unpaid')";
            
            $stmt_order = $this->conn->prepare($query_order);
            $stmt_order->execute([
                ':user_id' => $user_id,
                ':full_name' => $shipping_info['full_name'],
                ':phone' => $shipping_info['phone'],
                ':address' => $shipping_info['address'],
                ':total' => $total_amount,
                ':discount' => $discount_amount,
                ':final' => $final_amount
            ]);

            $order_id = $this->conn->lastInsertId(); // Lấy ID đơn hàng vừa tạo

            // 2. Lưu từng sản phẩm vào bảng order_items và trừ tồn kho biến thể (size/màu)
            foreach($cart_items as $item) {
                // Thêm chi tiết đơn hàng
                $query_item = "INSERT INTO order_items (order_id, variant_id, quantity, unit_price) 
                               VALUES (:order_id, :variant_id, :quantity, :unit_price)";
                $stmt_item = $this->conn->prepare($query_item);
                $stmt_item->execute([
                    ':order_id' => $order_id,
                    ':variant_id' => $item['variant_id'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['price']
                ]);

                // Trừ số lượng tồn kho trong bảng product_variants
                $query_stock = "UPDATE product_variants 
                                SET stock_quantity = stock_quantity - :quantity 
                                WHERE variant_id = :variant_id AND stock_quantity >= :quantity";
                $stmt_stock = $this->conn->prepare($query_stock);
                $stmt_stock->execute([
                    ':quantity' => $item['quantity'],
                    ':variant_id' => $item['variant_id']
                ]);

                // Nếu kho không đủ để trừ, báo lỗi
                if($stmt_stock->rowCount() == 0) {
                    throw new Exception("Sản phẩm có biến thể (Size/Màu) đã hết hàng hoặc không đủ số lượng!");
                }
            }

            // Hoàn tất transaction
            $this->conn->commit();
            return $order_id;

        } catch (Exception $e) {
            // Rollback nếu có lỗi xảy ra
            $this->conn->rollBack();
            return "Lỗi đặt hàng: " . $e->getMessage();
        }
    }

    // Lấy lịch sử đơn hàng của một khách hàng
    public function getOrdersByUser($user_id) {
        $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function countOrders() {
        return $this->conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    // Tính tổng doanh thu đơn hàng hoàn thành
    public function getTotalRevenue() {
        $stmt = $this->conn->query("SELECT SUM(final_amount) FROM orders WHERE order_status = 'completed'");
        return $stmt->fetchColumn() ?: 0;
    }
    // Lấy thông tin chung của một đơn hàng theo ID
    public function getOrderById($order_id) {
        $query = "SELECT o.*, u.full_name as user_name, u.email 
                  FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.user_id 
                  WHERE o.order_id = :order_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách sản phẩm (biến thể size/màu) thuộc đơn hàng đó
    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, p.product_name, pv.size, pv.color, pv.sku 
                  FROM order_items oi
                  JOIN product_variants pv ON oi.variant_id = pv.variant_id
                  JOIN products p ON pv.product_id = p.product_id
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //lấy thông tin đơn hàng
    public function getAllOrders() {
        $query = "SELECT o.*, u.full_name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.user_id ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>