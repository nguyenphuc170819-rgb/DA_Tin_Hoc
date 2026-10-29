<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Category.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$catObj = new Category($conn);

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = trim($_POST['category_name']);
    $slug = trim($_POST['slug']);
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

    if (empty($category_name) || empty($slug)) {
        $error = "Vui lòng nhập đầy đủ tên và slug danh mục!";
    } else {
        if ($catObj->addCategory($category_name, $slug, $parent_id)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Thêm danh mục thất bại!";
        }
    }
}
$all_cats = $catObj->getAllCategories();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Danh mục - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif; max-width: 500px;">
        <h1>Thêm Danh mục mới</h1>
        <hr>
        <p><a href="index.php">⬅ Quay lại</a></p>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label>Tên danh mục:</label><br>
                <input type="text" name="category_name" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Slug (đường dẫn URL):</label><br>
                <input type="text" name="slug" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Thuộc danh mục cha:</label><br>
                <select name="parent_id" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="">-- Chọn làm danh mục gốc --</option>
                    <?php foreach ($all_cats as $c): ?>
                        <option value="<?php echo $c['category_id']; ?>"><?php echo $c['category_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" style="background: #28a745; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px;">Thêm danh mục</button>
        </form>
    </div>
</body>
</html>