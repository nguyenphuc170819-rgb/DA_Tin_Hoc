<?php
if (!defined("ROOT")) { exit; }
require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Product.php';
require_once ROOT . '/classes/Category.php';

$db = (new Database())->connect();
$productModel = new Product($db);
$categoryModel = new Category($db);

$category_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$category = $categoryModel->getCategoryById($category_id);
$products = $productModel->getProductsByCategory($category_id);
?>
<div class="container my-3">
    <h3 class="fw-bold mb-4 pb-2 border-bottom">
        <i class="fa fa-list-alt"></i> Danh mục: <?= htmlspecialchars($category['category_name'] ?? 'Tất cả') ?>
    </h3>
    <?php include __DIR__ . '/_product_grid.php'; ?>
</div>