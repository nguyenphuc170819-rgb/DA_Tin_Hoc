<?php
if (!defined("ROOT")) { exit; }

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'index.php?mod=user&action=login';</script>";
    exit;
}
require_once ROOT . '/config/config.php';
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Cart.php';

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];
$cartObj = new Cart($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Thêm từ form chi tiết sản phẩm (có chọn size/màu cụ thể)
    $variant_id = isset($_POST['variant_id']) ? intval($_POST['variant_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($variant_id > 0) {
        $cartObj->addToCart($user_id, $variant_id, $quantity);
    }
} elseif (isset($_GET['id'])) {
    // Thêm nhanh từ nút giỏ hàng ở danh sách sản phẩm (chỉ có product_id)
    $product_id = intval($_GET['id']);
    
    // Gọi phương thức thông qua OOP
    $variant_id = $cartObj->getDefaultVariantId($product_id);

    if ($variant_id) {
        $cartObj->addToCart($user_id, $variant_id, 1);
    }
}

// Chuyển hướng an toàn về trang giỏ hàng
echo "<script>window.location.href = 'index.php?mod=cart&action=index';</script>";
exit;
?>