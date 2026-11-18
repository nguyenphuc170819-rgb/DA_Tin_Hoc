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

    //Thống kê doanh thu nhóm theo tháng trong năm hiện tại (hoặc toàn bộ)
    public function getRevenueByMonth($year = null) {
        if ($year === null) {
            $year = date('Y');
        }
    
        $query = "SELECT MONTH(created_at) AS thong_ke_thang, 
                        SUM(final_amount) AS doanh_thu, 
                        COUNT(order_id) AS tong_don 
                FROM orders 
                WHERE order_status = 'completed' AND YEAR(created_at) = :year
                GROUP BY MONTH(created_at)
                ORDER BY thong_ke_thang ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    //Lấy thông tin chi tiết một đơn hàng kèm theo thanh toán
    public function getOrderById2($order_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT o.*, p.payment_method, p.status as payment_method_status 
                                      FROM orders o 
                                      LEFT JOIN payments p ON o.order_id = p.order_id 
                                      WHERE o.order_id = ? AND o.user_id = ?");
        $stmt->execute([$order_id, $user_id]);
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

    // Cập nhật trạng thái xử lý đơn hàng (pending, confirmed, shipping, completed, cancelled)
    public function updateOrderStatus($order_id, $order_status) {
        try {
            $query = "UPDATE orders SET order_status = :status WHERE order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':status' => $order_status,
                ':order_id' => $order_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Cập nhật trạng thái thanh toán (unpaid, paid, refunded)
    public function updatePaymentStatus($order_id, $payment_status) {
        try {
            $query = "UPDATE orders SET payment_status = :status WHERE order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':status' => $payment_status,
                ':order_id' => $order_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Lấy danh sách đơn hàng đang chờ xử lý (pending)
    public function getPendingOrders($limit = 5) {
        $query = "SELECT * FROM orders WHERE order_status = 'pending' ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách đơn hàng theo trạng thái (Dùng cho bộ lọc)
    public function getOrdersByStatus($status) {
        $query = "SELECT o.*, u.full_name as user_name 
                  FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.user_id 
                  WHERE o.order_status = :status 
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //checkout
    public function checkCoupon($code, $subtotal) {
        $stmt = $this->conn->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (start_date IS NULL OR start_date <= NOW()) AND (end_date IS NULL OR end_date >= NOW())");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) {
            return ['status' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn!'];
        }

        if ($subtotal < $coupon['min_order_value']) {
            return ['status' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu (' . number_format($coupon['min_order_value'], 0, ',', '.') . ' đ) để dùng mã này!'];
        }

        if ($coupon['used_count'] >= $coupon['usage_limit']) {
            return ['status' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!'];
        }

        // Tính số tiền được giảm
        $discount_amount = 0;
        if ($coupon['discount_type'] == 'percentage') {
            $discount_amount = ($subtotal * $coupon['discount_value']) / 100;
        } else {
            $discount_amount = $coupon['discount_value'];
        }

        // Đảm bảo tiền giảm không lớn hơn tổng tiền hàng
        if ($discount_amount > $subtotal) {
            $discount_amount = $subtotal;
        }

        return [
            'status' => true,
            'coupon_id' => $coupon['coupon_id'],
            'discount_amount' => $discount_amount,
            'message' => 'Áp dụng mã giảm giá thành công!'
        ];
    }

    // 2. Cập nhật quy trình checkout tích hợp mã giảm giá
    public function processCheckout($user_id, $data, $cart_items, $coupon_code = null) {
        try {
            $this->conn->beginTransaction();

            $total_money = 0;
            foreach ($cart_items as $item) {
                $total_money += (float)$item['price'] * (int)$item['quantity'];
            }

            $shipping_fee = 30000;
            $discount_amount = 0;
            $coupon_id = null;

            // Xử lý nếu có mã giảm giá gửi lên
            if (!empty($coupon_code)) {
                $check = $this->checkCoupon($coupon_code, $total_money);
                if ($check['status']) {
                    $discount_amount = $check['discount_amount'];
                    $coupon_id = $check['coupon_id'];

                    // Tăng số lần sử dụng của coupon lên 1
                    $stmt_up_coupon = $this->conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE coupon_id = ?");
                    $stmt_up_coupon->execute([$coupon_id]);
                }
            }

            $final_amount = $total_money + $shipping_fee - $discount_amount;
            if ($final_amount < 0) $final_amount = 0;

            // Lưu vào bảng orders (nếu bảng orders của bạn có cột coupon_id hoặc lưu ghi chú, tùy biến câu lệnh SQL dưới đây)
            $sql_order = "INSERT INTO orders (user_id, shipping_full_name, shipping_phone, shipping_address, total_amount, discount_amount, shipping_fee, final_amount, order_status, payment_status, note, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid', ?, NOW())";
            $stmt_order = $this->conn->prepare($sql_order);
            $stmt_order->execute([
                $user_id, 
                $data['shipping_full_name'], 
                $data['shipping_phone'], 
                $data['shipping_address'], 
                $total_money, 
                $discount_amount, 
                $shipping_fee, 
                $final_amount, 
                $data['note']
            ]);
            $order_id = $this->conn->lastInsertId();

            // Lưu vào order_items và trừ kho
            foreach ($cart_items as $item) {
                $sql_item = "INSERT INTO order_items (order_id, variant_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
                $this->conn->prepare($sql_item)->execute([$order_id, $item['variant_id'], $item['quantity'], $item['price']]);

                $sql_stock = "UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE variant_id = ?";
                $this->conn->prepare($sql_stock)->execute([$item['quantity'], $item['variant_id']]);
            }

            // Lưu bảng payments
            $sql_payment = "INSERT INTO payments (order_id, payment_method, amount, status, paid_at) VALUES (?, ?, ?, 'pending', NULL)";
            $this->conn->prepare($sql_payment)->execute([$order_id, $data['payment_method'], $final_amount]);

            // Xóa giỏ hàng của user
            $sql_cart = "SELECT cart_id FROM carts WHERE user_id = ?";
            $stmt_c = $this->conn->prepare($sql_cart);
            $stmt_c->execute([$user_id]);
            $cart = $stmt_c->fetch(PDO::FETCH_ASSOC);

            if ($cart) {
                $this->conn->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cart['cart_id']]);
            }

            $this->conn->commit();
            return $order_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // 1. Lấy danh sách đơn hàng của một user
    public function getOrdersByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>