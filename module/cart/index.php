<?php
if (!defined("ROOT")) { exit; }

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-warning text-center p-5 my-5'>
            <h4>Vui lòng đăng nhập để xem giỏ hàng của bạn!</h4>
            <a href='" . BASE_URL . "/module/user/login.php' class='btn btn-primary mt-3'>Đăng nhập ngay</a>
          </div>";
    return;
}

// Nhúng kết nối và Class Cart qua hằng số ROOT
require_once ROOT . '/config/config.php';
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Cart.php';

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];
$cartObj = new Cart($conn);
$cart_items = $cartObj->getCartItems($user_id);
$total_money = 0;
?>

<div class="container my-4">
    <h2 class="mb-4"><i class="fa fa-shopping-cart me-2"></i> Giỏ hàng của bạn</h2>

    <?php if (!empty($cart_items)): ?>
        <div class="table-responsive">
            <form action="index.php?mod=cart&action=update" method="POST">
                <table class="table table-bordered align-middle text-center bg-white shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Size / Màu (Tùy chọn đổi)</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $index => $item): 
                            $price = isset($item['price']) ? $item['price'] : 0;
                            $subtotal = (float)$price * (int)$item['quantity'];
                            $total_money += $subtotal;
                        ?>
                        <tr>
                            <!-- Cột 1: Thông tin sản phẩm -->
                            <td class="text-start d-flex align-items-center gap-3">
                                <img src="<?= htmlspecialchars(!empty($item['variant_image']) ? $item['variant_image'] : BASE_URL . '/image/default.png') ?>" alt="" style="width: 60px; height: 60px; object-fit: cover;" class="rounded border">
                                <div>
                                    <h6 class="mb-1"><b><?= htmlspecialchars($item['product_name']) ?></b></h6>
                                    <!-- <small class="text-muted">SKU: <?= htmlspecialchars($item['sku']) ?></small> -->
                                </div>
                            </td>

                            <!-- Cột 2: Chọn Size / Màu trực tiếp bằng dropdown -->
                            <td>
                                <input type="hidden" name="items[<?= $index ?>][cart_item_id]" value="<?= $item['cart_item_id'] ?>">
                                
                                <?php 
                                $variants = $cartObj->getVariantsByProductId($item['product_id']);
                                $sizes = array_unique(array_column($variants, 'size'));
                                $colors = array_unique(array_column($variants, 'color'));
                                ?>
                                <div class="d-flex gap-1 justify-content-center">
                                    <!-- Dropdown chọn Size -->
                                    <select name="items[<?= $index ?>][size]" class="form-select form-select-sm" style="width: 85px;" title="Đổi Size">
                                        <?php foreach ($sizes as $s): ?>
                                            <option value="<?= $s ?>" <?= ($s == $item['size']) ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                    <!-- Dropdown chọn Màu -->
                                    <select name="items[<?= $index ?>][color]" class="form-select form-select-sm" style="width: 105px;" title="Đổi Màu">
                                        <?php foreach ($colors as $c): ?>
                                            <option value="<?= $c ?>" <?= ($c == $item['color']) ? 'selected' : '' ?>><?= $c ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </td>

                            <!-- Cột 3: Đơn giá -->
                            <td class="text-danger fw-bold"><?= number_format((float)$price, 0, ',', '.') ?> đ</td>

                            <!-- Cột 4: Số lượng -->
                            <td>
                                <input type="number" name="items[<?= $index ?>][quantity]" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>" class="form-control form-control-sm text-center mx-auto" style="width: 65px;" title="Số lượng">
                            </td>

                            <!-- Cột 5: Thành tiền -->
                            <td class="text-danger fw-bold"><?= number_format((float)$subtotal, 0, ',', '.') ?> đ</td>

                            <!-- Cột 6: Xóa -->
                            <td>
                                <a href="index.php?mod=cart&action=delete&id=<?= $item['cart_item_id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Thanh công cụ tổng kết & nút Cập nhật chung cho toàn giỏ hàng -->
                <div class="card bg-white shadow-sm p-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex gap-2 align-items-center">
                            <a href="index.php?mod=product&action=list" class="text-decoration-none"><i class="fa fa-arrow-left me-1"></i> Tiếp tục mua sắm</a>
                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fa fa-sync"></i> Cập nhật giỏ hàng</button>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-2">Tổng cộng: <span class="text-danger fw-bold"><?= number_format((float)$total_money, 0, ',', '.') ?> đ</span></h4>
                            <a href="index.php?mod=cart&action=checkout" class="btn btn-success px-4 py-2 fw-bold">Tiến hành đặt hàng &rarr;</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    <?php else: ?>
        <div class="text-center p-5 bg-white rounded shadow-sm">
            <p class="fs-5 text-muted">Giỏ hàng của bạn đang trống!</p>
            <a href="index.php?mod=product&action=list" class="btn btn-primary mt-2">Khám phá sản phẩm ngay</a>
        </div>
    <?php endif; ?>
</div>