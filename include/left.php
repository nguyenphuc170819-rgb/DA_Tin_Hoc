<?php
if (!defined("ROOT")) { exit; }

// Đảm bảo đã có config và các class cần thiết
require_once ROOT_PATH . '/classes/Database.php';
require_once ROOT_PATH . '/classes/Category.php';

// 1. Tạo kết nối CSDL (Chú ý: nếu class Database của bạn dùng getConnection() thì thay lại cho khớp)
$database = new Database();
$db = $database->connect(); 

// 2. Khởi tạo Category và truyền $db vào
$categoryModel = new Category($db);

// 3. Lấy danh sách danh mục
$categories = $categoryModel->getAllCategories();
?>
<aside id="sidebar">
    <div class="widget card shadow-sm mb-4 border-0 p-3">
        <h5 class="fw-bold mb-3 text-dark"><i class="fa fa-list text-primary me-2"></i> Danh mục sản phẩm</h5>
        <ul class="list-group list-group-flush">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <li class="list-group-item px-0 border-0">
                        <!-- Đã sửa: Trỏ qua index.php, dùng mod=product và action=catalog -->
                        <a href="<?= BASE_URL ?>/index.php?mod=product&action=catalog&id=<?= $cat['category_id'] ?>" class="text-decoration-none text-dark d-block py-1">
                            <i class="fa fa-angle-right me-2 text-muted"></i> <?= htmlspecialchars($cat['category_name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item px-0 text-muted border-0">Chưa có danh mục nào</li>
            <?php endif; ?>
        </ul>
    </div>
    <!-- Các widget khác giữ nguyên -->
</aside>