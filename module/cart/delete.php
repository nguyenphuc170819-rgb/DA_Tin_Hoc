<?php
if (!defined("ROOT")) { exit; }

require_once ROOT . '/config/config.php';
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Cart.php';

$db = new Database();
$conn = $db->connect();
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $cart_item_id = intval($_GET['id']);

    $cartObj = new Cart($conn);
    $cartObj->removeCartItem($cart_item_id);
}

echo "<script>window.location.href = 'index.php?mod=cart&action=index';</script>";
exit();
