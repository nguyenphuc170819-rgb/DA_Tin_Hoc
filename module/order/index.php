<?php
if (!defined("ROOT")) { exit; }

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?mod=user&action=login");
    exit();
}

require_once ROOT . '/config/config.php';
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Order.php';

$db = new Database();
$conn = $db->connect();
$orderObj = new Order($conn);
$user_id = $_SESSION['user_id'];

$orders = $orderObj->getOrdersByUserId($user_id);
?>

<div class="container my-4">
    <h2 class="mb-4"><i class="fa fa-history me-2"></i> Lịch sử đơn hàng của bạn</h2>

    <?php if (!empty($orders)): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Người nhận</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái đơn</th>
                        <th>Thanh toán</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="fw-bold">#<?= $order['order_id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td class="text-start">
                            <div><?= htmlspecialchars($order['shipping_full_name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($order['shipping_phone']) ?></small>
                        </td>
                        <td class="text-danger fw-bold"><?= number_format($order['final_amount'], 0, ',', '.') ?> đ</td>
                        <td>
                            <?php 
                                $status = $order['order_status'];
                                $badgeClass = 'bg-secondary';
                                $statusText = 'Chờ xử lý';
                                if($status == 'confirmed') { $badgeClass = 'bg-info text-dark'; $statusText = 'Đã xác nhận'; }
                                elseif($status == 'shipping') { $badgeClass = 'bg-primary'; $statusText = 'Đang giao hàng'; }
                                elseif($status == 'completed') { $badgeClass = 'bg-success'; $statusText = 'Hoàn thành'; }
                                elseif($status == 'cancelled') { $badgeClass = 'bg-danger'; $statusText = 'Đã hủy'; }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                        </td>
                        <td>
                            <?php if ($order['payment_status'] == 'paid'): ?>
                                <span class="badge bg-success">Đã thanh toán</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?mod=order&action=detail&id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-eye"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center p-5 bg-white rounded shadow-sm">
            <p class="fs-5 text-muted">Bạn chưa có đơn hàng nào!</p>
            <a href="index.php?mod=product&action=list" class="btn btn-primary mt-2">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>