<?php
if (!defined("ROOT")) { exit; }

// 1. Lấy ID sản phẩm từ URL và kiểm tra hợp lệ
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    echo '<div class="container my-5"><div class="alert alert-danger">Mã sản phẩm không hợp lệ!</div></div>';
    return;
}

// 2. Nạp Database và Product Model
require_once ROOT_PATH . '/classes/Database.php';
require_once ROOT_PATH . '/classes/Product.php';

$db = (new Database())->connect();
$productModel = new Product($db);

// 3. Lấy toàn bộ thông tin thông qua Model (Không có câu lệnh SQL ở đây)
$product = $productModel->getProductById($product_id);

if (!$product) {
    echo '<div class="container my-5"><div class="alert alert-warning">Sản phẩm không tồn tại hoặc đã bị ngừng kinh doanh!</div></div>';
    return;
}

// Lấy biến thể và hình ảnh thông qua các phương thức của Model
$variants = $productModel->getVariantsByProductId($product_id);
$images = $productModel->getImagesByProductId($product_id);
$related_products = !empty($product['category_id']) ? $productModel->getRelatedProducts($product['category_id'], $product_id, 4) : [];
?>

<div class="container my-4">
    <!-- Breadcrumb điều hướng -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?mod=product&action=list" class="text-decoration-none">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['product_name']) ?></li>
        </ol>
    </nav>

    <!-- Khung chi tiết sản phẩm -->
    <div class="card shadow-sm border-0 p-4 mb-5 bg-white">
        <div class="row">
            <!-- Cột hình ảnh sản phẩm -->
            <div class="col-md-5 mb-4 mb-md-0">
                <div class="border rounded overflow-hidden text-center bg-light p-3 mb-3">
                    <?php 
                    $main_img = !empty($product['image']) ? BASE_URL . '/image/' . $product['image'] : 'https://via.placeholder.com/400x400?text=No+Image';
                    ?>
                    <img id="main-product-img" src="<?= $main_img ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['product_name']) ?>" style="max-height: 400px; object-fit: cover;">
                </div>

                <!-- Thư viện ảnh nhỏ (Thumbnails) -->
                <?php if (!empty($images)): ?>
                    <div class="d-flex gap-2 overflow-auto pb-2">
                        <?php foreach ($images as $img): ?>
                            <div class="border rounded overflow-hidden" style="width: 70px; height: 70px; cursor: pointer;" onclick="document.getElementById('main-product-img').src='<?= BASE_URL . '/image/' . $img['image_url'] ?>'">
                                <img src="<?= BASE_URL . '/image/' . $img['image_url'] ?>" class="w-100 h-100" style="object-fit: cover;" alt="Thumbnail">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cột thông tin sản phẩm & Chọn biến thể -->
            <div class="col-md-7">
                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($product['brand_name'] ?? 'Thương hiệu khác') ?></span>
                <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($product['product_name']) ?></h2>
                
                <!-- Giá tiền -->
                <div class="mb-3">
                    <span class="fs-3 fw-bold text-danger">
                        <?= number_format($product['base_price'], 0, ',', '.') ?> đ
                    </span>
                </div>

                <div class="text-muted mb-4">
                    <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Đang cập nhật mô tả chi tiết cho sản phẩm này...')) ?></p>
                </div>

                <hr class="text-muted">

                <!-- Form Thêm vào giỏ hàng -->
                <form action="<?= BASE_URL ?>/index.php?mod=cart&action=add" method="POST" class="mt-4">
                    
                    <!-- Chọn Biến thể (Size / Màu sắc) -->
                    <?php if (!empty($variants)): ?>
                        <div class="mb-3">
                            <label class="fw-bold d-block mb-2"><i class="fa fa-ruler-combined me-1"></i> Chọn phiên bản (Size / Màu):</label>
                            <select name="variant_id" class="form-select" required>
                                <option value="">-- Vui lòng chọn Size & Màu --</option>
                                <?php foreach ($variants as $var): ?>
                                    <option value="<?= $var['variant_id'] ?>">
                                        Size: <?= htmlspecialchars($var['size']) ?> - Màu: <?= htmlspecialchars($var['color']) ?> 
                                        (<?= $var['stock_quantity'] > 0 ? 'Còn hàng: ' . $var['stock_quantity'] : 'Hết hàng' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning py-2">Sản phẩm này tạm thời hết hàng.</div>
                    <?php endif; ?>

                    <!-- Chọn số lượng -->
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <label class="fw-bold">Số lượng:</label>
                        </div>
                        <div class="col-auto">
                            <input type="number" name="quantity" value="1" min="1" max="99" class="form-control text-center" style="width: 80px;" required>
                        </div>
                    </div>

                    <!-- Nút bấm hành động -->
                    <?php if (!empty($variants)): ?>
                        <button type="submit" class="btn btn-dark btn-lg px-4 me-2">
                            <i class="fa fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-lg px-4 me-2" disabled>
                            Tạm hết hàng
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Phần sản phẩm liên quan -->
    <?php if (!empty($related_products)): ?>
        <div class="mt-5">
            <h4 class="fw-bold mb-3"><i class="fa fa-fire text-danger me-2"></i> Sản phẩm liên quan</h4>
            <div class="row">
                <?php foreach ($related_products as $item): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <?php 
                            $rel_img = !empty($item['image']) ? BASE_URL . '/image/' . $item['image'] : 'https://via.placeholder.com/300x300?text=No+Image';
                            ?>
                            <img src="<?= $rel_img ?>" class="card-img-top" alt="..." style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fs-6 fw-bold text-truncate"><?= htmlspecialchars($item['product_name']) ?></h5>
                                <p class="text-danger fw-bold mb-3"><?= number_format($item['base_price'], 0, ',', '.') ?> đ</p>
                                <a href="<?= BASE_URL ?>/index.php?mod=product&action=detail&id=<?= $item['product_id'] ?>" class="btn btn-outline-dark mt-auto btn-sm">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>