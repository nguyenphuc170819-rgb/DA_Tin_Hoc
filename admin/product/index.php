<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$productObj = new Product($conn);
$products = $productObj->getAllProductsAdmin();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản phẩm - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif;">
        <h1>Quản lý Sản phẩm Giày</h1>
        <hr>
        <p><a href="../index.php">⬅ Quay lại Dashboard</a> | <a href="add.php" style="background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">+ Thêm sản phẩm mới</a></p>

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th>ID</th>
                    <th>Tên giày</th>
                    <th>Danh mục</th>
                    <th>Thương hiệu</th>
                    <th>Giá gốc</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $pro): ?>
                    <tr>
                        <td>#<?php echo $pro['product_id']; ?></td>
                        <td><b><?php echo htmlspecialchars($pro['product_name']); ?></b></td>
                        <td><?php echo htmlspecialchars($pro['category_name'] ?? 'Chưa phân loại'); ?></td>
                        <td><?php echo htmlspecialchars($pro['brand_name'] ?? 'Không rõ'); ?></td>
                        <td><b style="color: red;"><?php echo number_format($pro['base_price'], 0, ',', '.'); ?> đ</b></td>
                        <td>
                            <?php if ($pro['is_active']): ?>
                                <span style="color: green;">Đang bán</span>
                            <?php else: ?>
                                <span style="color: gray;">Ngừng bán</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $pro['product_id']; ?>" style="color: #007bff;">Sửa</a> | 
                            <a href="delete.php?id=<?php echo $pro['product_id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" style="color: red;">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Chưa có sản phẩm giày nào trong hệ thống.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>