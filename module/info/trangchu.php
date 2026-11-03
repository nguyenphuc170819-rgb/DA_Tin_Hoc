<?php
if (!defined("ROOT")) { exit; }

require_once ROOT . '/classes/Database.php';
require_once ROOT . '/classes/Product.php';

$db = (new Database())->connect();
$productModel = new Product($db);
$latestProducts = $productModel->getLatestProducts(8);
?>
<div class="container my-3">
    <!-- Banner / Hero Section phong cách Bootstrap 5 -->
    <div class="p-5 mb-4 bg-light rounded-3 shadow-sm border">
        <div class="container-fluid py-3">
            <h1 class="display-5 fw-bold text-dark">Chào mừng đến với <?= SITE_NAME ?>!</h1>
            <p class="col-md-8 fs-5 text-muted">Hệ thống cửa hàng giày thể thao chính hãng hàng đầu. Cung cấp các mẫu Sneaker mới nhất, chất lượng cao cấp với mức giá tốt nhất thị trường.</p>
            <a href="<?= BASE_URL ?>/index.php?mod=product&ac=list" class="btn btn-dark btn-lg" type="button">
                <i class="fa fa-shopping-bag me-2"></i> Khám phá sản phẩm ngay
            </a>
        </div>
    </div>

    <!-- Tiêu đề phần sản phẩm mới -->
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h3 class="fw-bold m-0 text-dark"><i class="fa fa-fire text-danger"></i> Sản phẩm mới nổi bật</h3>
        <a href="<?= BASE_URL ?>/index.php?mod=product&ac=spmoi" class="text-decoration-none text-primary fw-bold">Xem tất cả &raquo;</a>
    </div>

    <?php 
    // Tái sử dụng lưới hiển thị sản phẩm đã tạo ở module product
    $products = $latestProducts;
    if (file_exists(ROOT . '/module/product/_product_grid.php')) {
        include ROOT . '/module/product/_product_grid.php';
    }
    ?>
</div>