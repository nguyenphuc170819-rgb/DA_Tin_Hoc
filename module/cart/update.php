<?php
if (!defined("ROOT")) { exit; }

require_once ROOT . '/config/config.php';
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Cart.php';

$db = new Database();
$conn = $db->connect();
if (isset($_POST['cart_item_id'], $_POST['quantity']) && isset($_SESSION['user_id'])) {
    $cart_item_id = intval($_POST['cart_item_id']);
    $quantity = intval($_POST['quantity']);

    $cartObj = new Cart($conn);
    $cartObj->updateQuantity($cart_item_id, $quantity);
}

echo "<script>window.location.href = 'index.php?mod=cart&action=index';</script>";
exit();
