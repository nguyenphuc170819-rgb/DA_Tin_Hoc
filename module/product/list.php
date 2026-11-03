<?php
if (!defined("ROOT")) { exit; }
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Product.php';

$db = (new Database())->connect();
$productModel = new Product($db);
$products = $productModel->getAllProductsAdmin();
?>
<div class="container my-3">
    <h3 class="fw-bold mb-4 pb-2 border-bottom"><i class="fa fa-th-list"></i> Tất cả sản phẩm</h3>
    <?php include __DIR__ . '/_product_grid.php'; ?>
</div>