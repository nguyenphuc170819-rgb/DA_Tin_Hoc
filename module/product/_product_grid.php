<?php
if (!defined("ROOT")) { exit; }

// Đảm bảo file function.php luôn được gọi để dùng format_currency
if (!function_exists('format_currency')) {
    require_once ROOT . '/include/function.php';
}

if (!empty($products)): ?>
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($products as $row): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <div class="position-relative overflow-hidden" style="height: 190px; background: #f8f9fa;">
                        <?php 
                        $img_url = !empty($row['primary_image']) ? BASE_URL . '/image/' . $row['primary_image'] : 'https://via.placeholder.com/300x300?text=No+Image';
                        ?>
                        <img src="<?= $img_url ?>" class="card-img-top h-100 object-fit-cover" alt="<?= htmlspecialchars($row['product_name']) ?>">
                        <?php if (!empty($row['brand_name'])): ?>
                            <span class="badge bg-dark position-absolute top-0 start-0 m-2"><?= htmlspecialchars($row['brand_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-truncate mb-2">
                            <a href="<?= BASE_URL ?>/index.php?mod=product&ac=detail&id=<?= $row['product_id'] ?>" class="text-decoration-none text-dark fw-bold" title="<?= htmlspecialchars($row['product_name']) ?>">
                                <?= htmlspecialchars($row['product_name']) ?>
                            </a>
                        </h6>
                        <p class="card-text text-danger fw-bold mb-3">
                            <?= format_currency($row['base_price']) ?>
                        </p>
                        <div class="mt-auto d-flex gap-2">
                            <a href="<?= BASE_URL ?>/index.php?mod=product&action=detail&id=<?= $row['product_id'] ?>" class="btn btn-outline-dark btn-sm flex-grow-1">Xem</a>
                            <a href="<?= BASE_URL ?>/index.php?mod=cart&action=add&id=<?= $row['product_id'] ?>" class="btn btn-dark btn-sm"><i class="fa fa-cart-plus"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center py-4" role="alert">
        <i class="fa fa-info-circle fa-2x mb-2"></i>
        <p class="mb-0">Không tìm thấy sản phẩm nào phù hợp.</p>
    </div>
<?php endif; ?>