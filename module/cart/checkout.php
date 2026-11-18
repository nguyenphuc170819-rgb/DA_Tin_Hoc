<?php
if (!defined("ROOT")) { exit; }

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?mod=user&action=login");
    exit();
}

require_once ROOT . '/config/config.php';
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Cart.php';
require_once ROOT . '/classes/Order.php';

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];
$cartObj = new Cart($conn);
$orderObj = new Order($conn);

$cart_items = $cartObj->getCartItems($user_id);

if (empty($cart_items)) {
    header("Location: index.php?mod=cart&action=index");
    exit();
}

$total_money = 0;
foreach ($cart_items as $item) {
    $total_money += (float)$item['price'] * (int)$item['quantity'];
}

$shipping_fee = 30000;
$discount_amount = 0;
$coupon_message = '';
$applied_coupon = $_SESSION['applied_coupon'] ?? '';

// Giữ lại dữ liệu người dùng đã nhập khi submit form (để không bị mất khi bấm Áp dụng hoặc bị lỗi)
$shipping_full_name = $_POST['shipping_full_name'] ?? ($_SESSION['full_name'] ?? '');
$shipping_phone     = $_POST['shipping_phone'] ?? ($_SESSION['phone_number'] ?? '');
$shipping_address   = $_POST['shipping_address'] ?? '';
$note               = $_POST['note'] ?? '';

// Xử lý khi người dùng bấm nút Áp dụng mã giảm giá
if (isset($_POST['apply_coupon'])) {
    $coupon_code = trim($_POST['coupon_code']);
    $check = $orderObj->checkCoupon($coupon_code, $total_money);
    if ($check['status']) {
        $_SESSION['applied_coupon'] = $coupon_code;
        $_SESSION['discount_amount'] = $check['discount_amount'];
        $discount_amount = $check['discount_amount'];
        $coupon_message = "<div class='text-success small mt-1'>{$check['message']}</div>";
    } else {
        unset($_SESSION['applied_coupon']);
        unset($_SESSION['discount_amount']);
        $coupon_message = "<div class='text-danger small mt-1'>{$check['message']}</div>";
    }
} elseif (isset($_SESSION['applied_coupon'])) {
    // Tự động tính lại nếu đã lưu sẵn trong session
    $check = $orderObj->checkCoupon($_SESSION['applied_coupon'], $total_money);
    if ($check['status']) {
        $discount_amount = $check['discount_amount'];
    } else {
        unset($_SESSION['applied_coupon']);
        unset($_SESSION['discount_amount']);
    }
}

$final_amount = $total_money + $shipping_fee - $discount_amount;
if ($final_amount < 0) $final_amount = 0;

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $data = [
        'shipping_full_name' => trim($shipping_full_name),
        'shipping_phone'     => trim($shipping_phone),
        'shipping_address'   => trim($shipping_address),
        'payment_method'     => $_POST['payment_method'] ?? 'COD',
        'note'               => trim($note)
    ];

    if (empty($data['shipping_full_name']) || empty($data['shipping_phone']) || empty($data['shipping_address'])) {
        $error = "Vui lòng điền đầy đủ thông tin giao hàng!";
    } else {
        $coupon_code_to_save = $_SESSION['applied_coupon'] ?? null;
        $order_id = $orderObj->processCheckout($user_id, $data, $cart_items, $coupon_code_to_save);

        if ($order_id) {
            // Xóa session mã giảm giá sau khi đặt thành công
            unset($_SESSION['applied_coupon']);
            unset($_SESSION['discount_amount']);

            echo "<script>alert('Đặt hàng thành công!'); window.location.href='index.php?mod=product&action=list';</script>";
            exit();
        } else {
            $error = "Đã xảy ra lỗi trong quá trình đặt hàng. Vui lòng thử lại!";
        }
    }
}
?>

<div class="container my-4">
    <h2 class="mb-4"><i class="fa fa-credit-card me-2"></i> Xác nhận thanh toán</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="row">
        <!-- Thông tin giao hàng -->
        <div class="col-md-7">
            <div class="card p-4 shadow-sm bg-white mb-4">
                <h4 class="mb-3">1. Thông tin giao hàng</h4>
                <div class="mb-3">
                    <label class="form-label">Họ tên người nhận</label>
                    <input type="text" class="form-control" name="shipping_full_name" value="<?= htmlspecialchars($shipping_full_name) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" name="shipping_phone" value="<?= htmlspecialchars($shipping_phone) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ nhận hàng cụ thể</label>
                    <textarea class="form-control" name="shipping_address" rows="3" required placeholder="Số nhà, đường, Phường/Xã, Quận/Huyện, Tỉnh/TP"><?= htmlspecialchars($shipping_address) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ghi chú (Tùy chọn)</label>
                    <textarea class="form-control" name="note" rows="2"><?= htmlspecialchars($note) ?></textarea>
                </div>
            </div>

            <div class="card p-4 shadow-sm bg-white">
                <h4 class="mb-3">2. Phương thức thanh toán</h4>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'COD') || !isset($_POST['payment_method']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="VNPAY" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'VNPAY') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="vnpay">Thanh toán trực tuyến VNPAY</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="momo" value="MOMO" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'MOMO') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="momo">Ví điện tử MOMO</label>
                </div>
            </div>
        </div>

        <!-- Tóm tắt đơn hàng & Mã giảm giá -->
        <div class="col-md-5">
            <div class="card p-4 shadow-sm bg-white">
                <h4 class="mb-3">3. Tóm tắt đơn hàng</h4>
                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($cart_items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($item['product_name']) ?></h6>
                                <small class="text-muted">Size: <?= $item['size'] ?> | Màu: <?= $item['color'] ?> | SL: <?= $item['quantity'] ?></small>
                            </div>
                            <span class="fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Ô nhập Mã giảm giá -->
                <div class="mb-3">
                    <label class="form-label">Mã giảm giá (Coupon)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="coupon_code" placeholder="Nhập mã (VD: WELCOME2026)" value="<?= htmlspecialchars($applied_coupon) ?>">
                        <button class="btn btn-outline-secondary" type="submit" name="apply_coupon">Áp dụng</button>
                    </div>
                    <?= $coupon_message ?>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Tạm tính:</span>
                    <strong><?= number_format($total_money, 0, ',', '.') ?> đ</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Phí vận chuyển:</span>
                    <strong><?= number_format($shipping_fee, 0, ',', '.') ?> đ</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Giảm giá:</span>
                    <strong class="text-success">-<?= number_format($discount_amount, 0, ',', '.') ?> đ</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                    <strong class="fs-5 text-danger"><?= number_format($final_amount, 0, ',', '.') ?> đ</strong>
                </div>

                <button type="submit" name="place_order" class="btn btn-success w-100 py-2 fw-bold text-uppercase">Đặt hàng ngay</button>
                <a href="index.php?mod=cart&action=index" class="btn btn-outline-secondary w-100 mt-2">Quay lại giỏ hàng</a>
            </div>
        </div>
    </form>
</div>