<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Order.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$orderObj = new Order($conn);

// Lấy ID đơn hàng từ URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin chung của đơn hàng
$query_order = "SELECT o.*, u.full_name as user_name, u.email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.user_id 
                WHERE o.order_id = :order_id LIMIT 1";
$stmt_order = $conn->prepare($query_order);
$stmt_order->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt_order->execute();
$order = $stmt_order->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<h3 style='color:red; text-align:center; margin-top:50px;'>Đơn hàng không tồn tại! <a href='index.php'>Quay lại danh sách</a></h3>";
    exit;
}

// Lấy danh sách sản phẩm (biến thể size/màu) thuộc đơn hàng này
$query_items = "SELECT oi.*, p.product_name, pv.size, pv.color, pv.sku 
                FROM order_items oi
                JOIN product_variants pv ON oi.variant_id = pv.variant_id
                JOIN products p ON pv.product_id = p.product_id
                WHERE oi.order_id = :order_id";
$stmt_items = $conn->prepare($query_items);
$stmt_items->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt_items->execute();
$items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết Đơn hàng #<?php echo $order['order_id']; ?> - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif; max-width: 900px; margin: auto;">
        <h1>Chi tiết Đơn hàng #<?php echo $order['order_id']; ?></h1>
        <hr>
        <p><a href="index.php">⬅ Quay lại danh sách đơn hàng</a></p>

        <!-- Thông tin chung & Giao hàng -->
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ddd;">
            <h3>Thông tin khách hàng & Giao hàng</h3>
            <p><b>Khách hàng tài khoản:</b> <?php echo htmlspecialchars($order['user_name'] ?? 'Khách vãng lai'); ?> (Email: <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?>)</p>
            <p><b>Họ tên người nhận:</b> <?php echo htmlspecialchars($order['shipping_full_name']); ?></p>
            <p><b>Số điện thoại:</b> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
            <p><b>Địa chỉ nhận hàng:</b> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
            <p><b>Ghi chú của khách:</b> <span style="color: #555;"><?php echo htmlspecialchars($order['note'] ?? 'Không có'); ?></span></p>
            <p><b>Trạng thái đơn hàng:</b> 
                <span style="padding: 4px 8px; border-radius: 3px; font-weight: bold; 
                    <?php 
                        if($order['order_status'] == 'completed') echo 'background: #d4edda; color: #155724;';
                        elseif($order['order_status'] == 'cancelled') echo 'background: #f8d7da; color: #721c24;';
                        elseif($order['order_status'] == 'shipping') echo 'background: #cce5ff; color: #004085;';
                        else echo 'background: #fff3cd; color: #856404;';
                    ?>">
                    <?php echo ucfirst($order['order_status']); ?>
                </span>
            </p>
            <p><b>Trạng thái thanh toán:</b> <span style="font-weight: bold; color: <?php echo ($order['payment_status'] == 'paid') ? 'green' : 'orange'; ?>;"><?php echo ucfirst($order['payment_status']); ?></span></p>
            <p><b>Thời gian đặt hàng:</b> <?php echo $order['created_at']; ?></p>
        </div>

        <!-- Bảng sản phẩm trong đơn -->
        <h3>Danh sách giày trong đơn hàng</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th>Tên sản phẩm</th>
                    <th>Size / Màu</th>
                    <th>Mã SKU</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($item['product_name']); ?></b></td>
                        <td>Size: <b><?php echo $item['size']; ?></b> | Màu: <b><?php echo htmlspecialchars($item['color']); ?></b></td>
                        <td><?php echo htmlspecialchars($item['sku']); ?></td>
                        <td><?php echo number_format($item['unit_price'], 0, ',', '.'); ?> đ</td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td><b style="color: red;"><?php echo number_format($item['unit_price'] * $item['quantity'], 0, ',', '.'); ?> đ</b></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Không có sản phẩm nào trong đơn hàng này.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tổng tiền thanh toán -->
        <div style="text-align: right; margin-top: 20px; font-size: 16px; background: #fff; padding: 10px;">
            <p>Tiền hàng: <b><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</b></p>
            <p>Giảm giá (Mã coupon): <b style="color: green;">-<?php echo number_format($order['discount_amount'], 0, ',', '.'); ?> đ</b></p>
            <p>Phí vận chuyển: <b><?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?> đ</b></p>
            <hr style="width: 300px; margin-left: auto;">
            <h2>Tổng thanh toán: <span style="color: #d9534f;"><?php echo number_format($order['final_amount'], 0, ',', '.'); ?> đ</span></h2>
        </div>
    </div>
</body>
</html>