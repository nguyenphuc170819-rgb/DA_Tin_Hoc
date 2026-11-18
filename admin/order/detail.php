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

// Xử lý khi Admin bấm nút lưu cập nhật trạng thái
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_order_status = $_POST['order_status'] ?? '';
    $new_payment_status = $_POST['payment_status'] ?? '';

    $res1 = $orderObj->updateOrderStatus($order_id, $new_order_status);
    $res2 = $orderObj->updatePaymentStatus($order_id, $new_payment_status);

    if ($res1 || $res2) {
        $message = "<div style='background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 4px;'>Cập nhật trạng thái đơn hàng thành công!</div>";
    } else {
        $message = "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px;'>Có lỗi xảy ra khi cập nhật!</div>";
    }
}

// Lấy thông tin chung của đơn hàng thông qua Class Order
$order = $orderObj->getOrderById($order_id);

if (!$order) {
    echo "<h3 style='color:red; text-align:center; margin-top:50px;'>Đơn hàng không tồn tại! <a href='index.php'>Quay lại danh sách</a></h3>";
    exit;
}

// Lấy danh sách sản phẩm thuộc đơn hàng thông qua Class Order
$items = $orderObj->getOrderItems($order_id);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết Đơn hàng #<?php echo $order['order_id']; ?> - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif; max-width: 1000px; margin: auto;">
        <h1>Chi tiết Đơn hàng #<?php echo $order['order_id']; ?></h1>
        <hr>
        <p><a href="index.php">⬅ Quay lại danh sách đơn hàng</a></p>

        <?php echo $message; ?>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
            <!-- Thông tin khách hàng & Giao hàng -->
            <div style="flex: 1; background: #f9f9f9; padding: 20px; border-radius: 5px; border: 1px solid #ddd; min-width: 300px;">
                <h3>Thông tin khách hàng & Giao hàng</h3>
                <p><b>Tài khoản:</b> <?php echo htmlspecialchars($order['user_name'] ?? 'Khách vãng lai'); ?> (<?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?>)</p>
                <p><b>Họ tên người nhận:</b> <?php echo htmlspecialchars($order['shipping_full_name']); ?></p>
                <p><b>Số điện thoại:</b> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                <p><b>Địa chỉ nhận hàng:</b> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                <p><b>Ghi chú:</b> <span style="color: #555;"><?php echo htmlspecialchars($order['note'] ?? 'Không có'); ?></span></p>
                <p><b>Thời gian đặt hàng:</b> <?php echo $order['created_at']; ?></p>
            </div>

            <!-- Form cập nhật trạng thái đơn hàng & thanh toán -->
            <div style="flex: 1; background: #fff3cd; padding: 20px; border-radius: 5px; border: 1px solid #ffeeba; min-width: 300px;">
                <h3><i class="fa fa-edit"></i> Cập nhật trạng thái</h3>
                <form method="POST" action="">
                    <div style="margin-bottom: 15px;">
                        <label><b>Trạng thái đơn hàng:</b></label>
                        <select name="order_status" style="width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc;">
                            <option value="pending" <?= ($order['order_status'] == 'pending') ? 'selected' : ''; ?>>Chờ xác nhận (pending)</option>
                            <option value="confirmed" <?= ($order['order_status'] == 'confirmed') ? 'selected' : ''; ?>>Đã xác nhận (confirmed)</option>
                            <option value="shipping" <?= ($order['order_status'] == 'shipping') ? 'selected' : ''; ?>>Đang giao hàng (shipping)</option>
                            <option value="completed" <?= ($order['order_status'] == 'completed') ? 'selected' : ''; ?>>Đã hoàn thành (completed)</option>
                            <option value="cancelled" <?= ($order['order_status'] == 'cancelled') ? 'selected' : ''; ?>>Đã hủy (cancelled)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label><b>Trạng thái thanh toán:</b></label>
                        <select name="payment_status" style="width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc;">
                            <option value="unpaid" <?= ($order['payment_status'] == 'unpaid') ? 'selected' : ''; ?>>Chưa thanh toán (unpaid)</option>
                            <option value="paid" <?= ($order['payment_status'] == 'paid') ? 'selected' : ''; ?>>Đã thanh toán (paid)</option>
                            <option value="refunded" <?= ($order['payment_status'] == 'refunded') ? 'selected' : ''; ?>>Đã hoàn tiền (refunded)</option>
                        </select>
                    </div>

                    <button type="submit" style="background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%;">
                        <i class="fa fa-save"></i> Lưu thay đổi
                    </button>
                </form>
            </div>
        </div>

        <!-- Bảng sản phẩm trong đơn -->
        <h3>Danh sách giày trong đơn hàng</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center;">
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
                        <td style="text-align: left;"><b><?php echo htmlspecialchars($item['product_name']); ?></b></td>
                        <td>Size: <b><?php echo $item['size']; ?></b> | Màu: <b><?php echo htmlspecialchars($item['color']); ?></b></td>
                        <td><?php echo htmlspecialchars($item['sku']); ?></td>
                        <td><?php echo number_format($item['unit_price'], 0, ',', '.'); ?> đ</td>
                        <td><?php echo $item['quantity']; ?></td>
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
        <div style="text-align: right; margin-top: 20px; font-size: 16px; background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
            <p>Tiền hàng: <b><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> đ</b></p>
            <p>Giảm giá (Mã coupon): <b style="color: green;">-<?php echo number_format($order['discount_amount'], 0, ',', '.'); ?> đ</b></p>
            <p>Phí vận chuyển: <b><?php echo number_format($order['shipping_fee'] ?? 0, 0, ',', '.'); ?> đ</b></p>
            <hr style="width: 300px; margin-left: auto;">
            <h2>Tổng thanh toán: <span style="color: #d9534f;"><?php echo number_format($order['final_amount'], 0, ',', '.'); ?> đ</span></h2>
        </div>
    </div>
</body>
</html>