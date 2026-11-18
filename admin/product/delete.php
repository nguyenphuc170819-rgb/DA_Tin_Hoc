<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$productObj = new Product($conn);

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id > 0) {
    $productObj->deleteProduct($product_id);
}

header("Location: index.php");
exit;
?>