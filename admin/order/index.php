<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Order.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();

$orderObj = new Order($conn);
$orders = $orderObj->getAllOrders();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif;">
        <h1>Quản lý Đơn hàng</h1>
        <hr>
        <p><a href="../index.php">⬅ Quay lại Dashboard</a></p>

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
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
                        <td>#<?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['shipping_full_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['shipping_phone']); ?></td>
                        <td><b><?php echo number_format($order['final_amount'], 0, ',', '.'); ?> đ</b></td>
                        <td>
                            <span style="padding: 3px 8px; border-radius: 3px; font-size: 12px; 
                                <?php 
                                    if($order['order_status'] == 'completed') echo 'background: #d4edda; color: #155724;';
                                    elseif($order['order_status'] == 'cancelled') echo 'background: #f8d7da; color: #721c24;';
                                    else echo 'background: #fff3cd; color: #856404;';
                                ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                        <td><?php echo ucfirst($order['payment_status']); ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                        <td>
                            <a href="detail.php?id=<?php echo $order['order_id']; ?>">Xem chi tiết</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">Chưa có đơn hàng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>