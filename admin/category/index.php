<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Category.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$catObj = new Category($conn);
$categories = $catObj->getAllCategories();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif;">
        <h1>Quản lý Danh mục Giày</h1>
        <hr>
        <p><a href="../index.php">⬅ Quay lại Dashboard</a> | <a href="add.php" style="background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">+ Thêm danh mục mới</a></p>

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Danh mục cha (Parent ID)</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>#<?php echo $cat['category_id']; ?></td>
                        <td><b><?php echo htmlspecialchars($cat['category_name']); ?></b></td>
                        <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                        <td><?php echo $cat['parent_id'] ? $cat['parent_id'] : '--- (Danh mục gốc)'; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $cat['category_id']; ?>" style="color: #007bff;">Sửa</a> | 
                            <a href="delete.php?id=<?php echo $cat['category_id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');" style="color: red;">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Chưa có danh mục nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>