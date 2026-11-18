<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Order.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$orderObj = new Order($conn);

// Lọc theo trạng thái thông qua phương thức của class Order
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

if ($status_filter != '') {
    $orders = $orderObj->getOrdersByStatus($status_filter);
} else {
    $orders = $orderObj->getAllOrders();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif;">
        <h1><i class="fa fa-shopping-cart"></i> Quản lý Đơn hàng</h1>
        <hr>
        <p><a href="../index.php">⬅ Quay lại Dashboard</a></p>

        <!-- Bộ lọc trạng thái đơn hàng -->
        <div style="margin: 20px 0; background: #f8f9fa; padding: 10px; border-radius: 5px; display: flex; gap: 10px; align-items: center;">
            <b>Lọc trạng thái:</b>
            <a href="index.php" style="padding: 5px 10px; background: <?= ($status_filter == '') ? '#007bff' : '#eee'; ?>; color: <?= ($status_filter == '') ? '#fff' : '#333'; ?>; text-decoration: none; border-radius: 3px;">Tất cả</a>
            <a href="index.php?status=pending" style="padding: 5px 10px; background: <?= ($status_filter == 'pending') ? '#007bff' : '#eee'; ?>; color: <?= ($status_filter == 'pending') ? '#fff' : '#333'; ?>; text-decoration: none; border-radius: 3px;">Chờ xác nhận</a>
            <a href="index.php?status=confirmed" style="padding: 5px 10px; background: <?= ($status_filter == 'confirmed') ? '#007bff' : '#eee'; ?>; color: <?= ($status_filter == 'confirmed') ? '#fff' : '#333'; ?>; text-decoration: none; border-radius: 3px;">Đã xác nhận</a>
            <a href="index.php?status=shipping" style="padding: 5px 10px; background: <?= ($status_filter == 'shipping') ? '#007bff' : '#eee'; ?>; color: <?= ($status_filter == 'shipping') ? '#fff' : '#333'; ?>; text-decoration: none; border-radius: 3px;">Đang giao</a>
            <a href="index.php?status=completed" style="padding: 5px 10px; background: <?= ($status_filter == 'completed') ? '#007bff' : '#eee'; ?>; color: <?= ($status_filter == 'completed') ? '#fff' : '#333'; ?>; text-decoration: none; border-radius: 3px;">Hoàn thành</a>
            <a href="index.php?status=cancelled" style="padding: 5px 10px; background: <?= ($status_filter == 'cancelled') ? '#007bff' : '#eee'; ?>; color: <?= ($status_filter == 'cancelled') ? '#fff' : '#333'; ?>; text-decoration: none; border-radius: 3px;">Đã hủy</a>
        </div>

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 20px; text-align: center;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th>Mã ĐH</th>
                    <th>Người nhận</th>
                    <th>Số điện thoại</th>
                    <th>Tổng thanh toán</th>
                    <th>Trạng thái đơn</th>
                    <th>Thanh toán</th>
                    <th>Ngày đặt</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><b>#<?php echo $order['order_id']; ?></b></td>
                        <td><?php echo htmlspecialchars($order['shipping_full_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['shipping_phone']); ?></td>
                        <td><b style="color: #d9534f;"><?php echo number_format($order['final_amount'], 0, ',', '.'); ?> đ</b></td>
                        <td>
                            <span style="padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; 
                                <?php 
                                    if($order['order_status'] == 'completed') echo 'background: #d4edda; color: #155724;';
                                    elseif($order['order_status'] == 'cancelled') echo 'background: #f8d7da; color: #721c24;';
                                    elseif($order['order_status'] == 'shipping') echo 'background: #cce5ff; color: #004085;';
                                    else echo 'background: #fff3cd; color: #856404;';
                                ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                        <td>
                            <span style="padding: 3px 6px; border-radius: 3px; font-size: 11px; font-weight: bold; background: <?php echo ($order['payment_status'] == 'paid') ? '#d4edda; color: #155724;' : '#fff3cd; color: #856404;'; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </td>
                        <td><?php echo $order['created_at']; ?></td>
                        <td>
                            <a href="detail.php?id=<?php echo $order['order_id']; ?>" style="background: #007bff; color: white; padding: 5px 10px; border-radius: 3px; text-decoration: none; font-size: 12px;"><i class="fa fa-eye"></i> Xem & Xử lý</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #666;">Không tìm thấy đơn hàng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>