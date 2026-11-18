<?php
class Cart {
    
    // Khởi động session nếu chưa có
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Thêm sản phẩm (theo biến thể variant_id: size & màu) vào giỏ hàng
    public function addToCart($variant_id, $quantity, $product_data) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu sản phẩm với size/màu này đã có trong giỏ thì tăng số lượng
        if (isset($_SESSION['cart'][$variant_id])) {
            $_SESSION['cart'][$variant_id]['quantity'] += $quantity;
        } else {
            // Nếu chưa có thì thêm mới vào giỏ
            $_SESSION['cart'][$variant_id] = [
                'variant_id' => $variant_id,
                'product_name' => $product_data['product_name'],
                // Ưu tiên lấy giá riêng của biến thể, nếu không có lấy giá gốc sản phẩm
                'price' => !empty($product_data['price']) ? $product_data['price'] : $product_data['base_price'],
                'size' => $product_data['size'],
                'color' => $product_data['color'],
                'image_url' => $product_data['image_url'],
                'quantity' => $quantity
            ];
        }
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateQuantity($variant_id, $quantity) {
        if (isset($_SESSION['cart'][$variant_id])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$variant_id]); // Xóa luôn nếu số lượng <= 0
            } else {
                $_SESSION['cart'][$variant_id]['quantity'] = $quantity;
            }
        }
    }

    // Xóa một sản phẩm khỏi giỏ hàng
    public function removeItem($variant_id) {
        if (isset($_SESSION['cart'][$variant_id])) {
            unset($_SESSION['cart'][$variant_id]);
        }
    }

    // Lấy toàn bộ sản phẩm trong giỏ hàng
    public function getCartItems() {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    // Tính tổng tiền giỏ hàng
    public function getTotalPrice() {
        $total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }
        }
        return $total;
    }

    // Xóa sạch giỏ hàng sau khi đặt hàng thành công
    public function clearCart() {
        unset($_SESSION['cart']);
    }
}
?>