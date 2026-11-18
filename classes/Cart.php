<?php
class Cart {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Lấy hoặc tạo cart_id của user dựa vào user_id
    public function getCartIdByUserId($user_id) {
        $query = "SELECT cart_id FROM carts WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) {
            return $cart['cart_id'];
        } else {
            // Nếu chưa có giỏ hàng, tạo mới cho user này
            $insert = "INSERT INTO carts (user_id) VALUES (:user_id)";
            $stmt_insert = $this->conn->prepare($insert);
            $stmt_insert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_insert->execute();
            return $this->conn->lastInsertId();
        }
    }

    // Thêm sản phẩm (biến thể) vào giỏ hàng trong CSDL
    public function addToCart($user_id, $variant_id, $quantity = 1) {
        $cart_id = $this->getCartIdByUserId($user_id);

        // Kiểm tra xem biến thể giày này đã có trong giỏ hàng chưa
        $check = "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = :cart_id AND variant_id = :variant_id LIMIT 1";
        $stmt_check = $this->conn->prepare($check);
        $stmt_check->execute([
            ':cart_id' => $cart_id,
            ':variant_id' => $variant_id
        ]);
        $item = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Nếu có rồi thì cộng dồn số lượng
            $new_quantity = $item['quantity'] + $quantity;
            $update = "UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id";
            $stmt_update = $this->conn->prepare($update);
            return $stmt_update->execute([
                ':quantity' => $new_quantity,
                ':cart_item_id' => $item['cart_item_id']
            ]);
        } else {
            // Chưa có thì thêm mới dòng vào cart_items
            $insert = "INSERT INTO cart_items (cart_id, variant_id, quantity) VALUES (:cart_id, :variant_id, :quantity)";
            $stmt_insert = $this->conn->prepare($insert);
            return $stmt_insert->execute([
                ':cart_id' => $cart_id,
                ':variant_id' => $variant_id,
                ':quantity' => $quantity
            ]);
        }
    }

    // Lấy toàn bộ sản phẩm trong giỏ hàng của user kèm thông tin chi tiết biến thể và sản phẩm
    public function getCartItems($user_id) {
        $cart_id = $this->getCartIdByUserId($user_id);

        $query = "SELECT ci.cart_item_id, ci.quantity, pv.variant_id, pv.sku, pv.size, pv.color, 
                         COALESCE(pv.price, p.base_price) as price, pv.stock_quantity, 
                         p.product_name, pv.image_url as variant_image, pv.product_id
                  FROM cart_items ci
                  JOIN product_variants pv ON ci.variant_id = pv.variant_id
                  JOIN products p ON pv.product_id = p.product_id
                  WHERE ci.cart_id = :cart_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật số lượng sản phẩm trong giỏ
    public function updateQuantity($cart_item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeCartItem($cart_item_id);
        }
        $query = "UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':quantity' => $quantity,
            ':cart_item_id' => $cart_item_id
        ]);
    }

    // Xóa một sản phẩm khỏi giỏ hàng
    public function removeCartItem($cart_item_id) {
        $query = "DELETE FROM cart_items WHERE cart_item_id = :cart_item_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':cart_item_id' => $cart_item_id]);
    }

    // Xóa sạch giỏ hàng của user sau khi đặt hàng thành công
    public function clearCart($user_id) {
        $cart_id = $this->getCartIdByUserId($user_id);
        $query = "DELETE FROM cart_items WHERE cart_id = :cart_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':cart_id' => $cart_id]);
    }

    // Lấy variant_id đầu tiên còn hàng của một sản phẩm
    public function getDefaultVariantId($product_id) {
        $query = "SELECT variant_id FROM product_variants WHERE product_id = :product_id AND stock_quantity > 0 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':product_id' => $product_id]);
        $variant = $stmt->fetch(PDO::FETCH_ASSOC);
        return $variant ? $variant['variant_id'] : null;
    }

    // Lấy tất cả biến thể của cùng một sản phẩm (phục vụ việc đổi size/màu trong giỏ hàng)
    public function getVariantsByProductId($product_id) {
        $query = "SELECT variant_id, size, color, stock_quantity FROM product_variants WHERE product_id = :product_id AND stock_quantity > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>