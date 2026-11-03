<?php
if (!defined("ROOT")) { exit; }
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Product.php';

$db = (new Database())->connect();
$productModel = new Product($db);

$keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : '';
$products = !empty($keyword) ? $productModel->searchProducts($keyword) : [];
?>
<div class="container my-3">
    <h3 class="fw-bold mb-4 pb-2 border-bottom">
        <i class="fa fa-search"></i> Kết quả tìm kiếm cho: "<?= htmlspecialchars($keyword) ?>"
    </h3>
    <?php include __DIR__ . '/_product_grid.php'; ?>
</div>