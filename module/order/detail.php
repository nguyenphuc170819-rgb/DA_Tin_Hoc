<?php
if (!defined("ROOT")) { exit; }

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?mod=user&action=login");
    exit();
}

require_once ROOT . '/classes/Order.php';
require_once ROOT . '/config/config.php';
require_once ROOT . '/classes/Database.php';

$db = new Database();
$conn = $db->connect();

$orderObj = new Order($conn);
$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$order = $orderObj->getOrderById2($order_id, $user_id);

if (!$order) {
    echo "<div class='container my-5 text-center'><h3>Đơn hàng không tồn tại hoặc không thuộc về bạn!</h3><a href='index.php?mod=order&action=index' class='btn btn-primary mt-3'>Quay lại lịch sử đơn hàng</a></div>";
    return;
}

$order_items = $orderObj->getOrderItems($order_id);
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết đơn hàng #<?= $order['order_id'] ?></h2>
        <a href="index.php?mod=order&action=index" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i> Quay lại lịch sử đơn</a>
    </div>

    <div class="row">
        <!-- Thông tin giao hàng & thanh toán -->
        <div class="col-md-5 mb-4">
            <div class="card p-4 shadow-sm bg-white h-100">
                <h4 class="mb-3">Thông tin nhận hàng</h4>
                <p class="mb-2"><strong>Họ tên:</strong> <?= htmlspecialchars($order['shipping_full_name']) ?></p>
                <p class="mb-2"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['shipping_phone']) ?></p>
                <p class="mb-2"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                <p class="mb-2"><strong>Ghi chú:</strong> <?= !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có' ?></p>
                <hr>
                <p class="mb-2"><strong>Phương thức thanh toán:</strong> <span class="text-uppercase"><?= $order['payment_method'] ?? 'COD' ?></span></p>
                <p class="mb-2"><strong>Trạng thái thanh toán:</strong> 
                    <?= $order['payment_status'] == 'paid' ? '<span class="text-success fw-bold">Đã thanh toán</span>' : '<span class="text-warning fw-bold">Chưa thanh toán</span>' ?>
                </p>
                <p class="mb-0"><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
            </div>
        </div>

        <!-- Danh sách sản phẩm trong đơn -->
        <div class="col-md-7 mb-4">
            <div class="card p-4 shadow-sm bg-white">
                <h4 class="mb-3">Sản phẩm đã mua</h4>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td style="width: 60px;">
                                    <img src="<?= htmlspecialchars(!empty($item['variant_image']) ? $item['variant_image'] : BASE_URL . '/image/default.png') ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
                                </td>
                                <td>
                                    <h6 class="mb-1"><b><?= htmlspecialchars($item['product_name']) ?></b></h6>
                                    <small class="text-muted">Size: <?= $item['size'] ?> | Màu: <?= $item['color'] ?></small>
                                </td>
                                <td class="text-center">x<?= $item['quantity'] ?></td>
                                <td class="text-end fw-bold text-danger"><?= number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') ?> đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tạm tính:</span>
                    <strong><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Phí vận chuyển:</span>
                    <strong><?= number_format($order['shipping_fee'], 0, ',', '.') ?> đ</strong>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span>Giảm giá (Coupon):</span>
                    <strong class="text-success">-<?= number_format($order['discount_amount'], 0, ',', '.') ?> đ</strong>
                </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                    <strong class="fs-5 text-danger"><?= number_format($order['final_amount'], 0, ',', '.') ?> đ</strong>
                </div>
            </div>
        </div>
    </div>
</div>