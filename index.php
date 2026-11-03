<?php
require_once __DIR__ . '/config/config.php';
require_once ROOT_PATH . '/include/function.php';

$page_title = "Trang chủ - Cửa hàng giày chính hãng";
require_once ROOT_PATH . '/include/header.php';
?>

<!-- BỐ CỤC 3 CỘT SỬ DỤNG BOOTSTRAP GRID -->
<div class="row">
    
    <!-- CỘT TRÁI: Sidebar danh mục & bộ lọc (Chiếm 3 cột trên màn hình vừa/lớn) -->
    <div class="col-lg-3 col-md-4 mb-3">
        <?php 
        if (file_exists(ROOT_PATH . '/include/left.php')) {
            require_once ROOT_PATH . '/include/left.php';
        }
        ?>
    </div>

    <!-- CỘT GIỮA: Nội dung sản phẩm chính (Chiếm 7 cột) -->
    <div class="col-lg-7 col-md-8 mb-3">
        <div class="card shadow-sm border-0 p-3 bg-white">
            <?php 
            if (file_exists(ROOT_PATH . '/mod.php')) {
                require_once ROOT_PATH . '/mod.php';
            } else {
                echo "<h2 class='text-center text-muted my-4'>Chào mừng đến với Shoe Store</h2>";
            }
            ?>
        </div>
    </div>

    <!-- CỘT PHẢI: Tiện ích / Tìm kiếm nhanh (Chiếm 2 cột) -->
    <div class="col-lg-2 d-none d-lg-block">
        <div class="card shadow-sm border-0 p-3 bg-white">
            <h5 class="card-title fs-6 fw-bold border-bottom pb-2 mb-3">
                <i class="fa fa-search text-primary"></i> Tìm kiếm
            </h5>
            <form action="<?= BASE_URL ?>/mod.php" method="GET">
                <input type="hidden" name="module" value="product" />
                <input type="hidden" name="action" value="search" />
                <div class="mb-2">
                    <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tên giày..." required />
                </div>
                <button type="submit" class="btn btn-dark btn-sm w-100">Tìm</button>
            </form>
        </div>
    </div>

</div>

</div> <!-- Đóng #main-container từ header.php -->
</div> <!-- Đóng #wrapper từ header.php -->

<!-- Nhúng Bootstrap 5 JS Bundle (Phục vụ cho các hiệu ứng dropdown, modal nếu cần sau này) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>