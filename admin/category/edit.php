<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Category.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$catObj = new Category($conn);

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = $catObj->getCategoryById($category_id);

if (!$category) {
    echo "<h3 style='color:red; text-align:center;'>Danh mục không tồn tại! <a href='index.php'>Quay lại</a></h3>";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = trim($_POST['category_name']);
    $slug = trim($_POST['slug']);
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

    if (empty($category_name) || empty($slug)) {
        $error = "Vui lòng nhập đầy đủ tên và slug!";
    } else {
        if ($catObj->updateCategory($category_id, $category_name, $slug, $parent_id)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Cập nhật danh mục thất bại!";
        }
    }
}
$all_cats = $catObj->getAllCategories();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Danh mục - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif; max-width: 500px;">
        <h1>Sửa Danh mục #<?php echo $category['category_id']; ?></h1>
        <hr>
        <p><a href="index.php">⬅ Quay lại</a></p>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label>Tên danh mục:</label><br>
                <input type="text" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Slug:</label><br>
                <input type="text" name="slug" value="<?php echo htmlspecialchars($category['slug']); ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Thuộc danh mục cha:</label><br>
                <select name="parent_id" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="">-- Làm danh mục gốc --</option>
                    <?php foreach ($all_cats as $c): ?>
                        <?php if ($c['category_id'] == $category['category_id']) continue; // Tránh chọn chính nó làm cha ?>
                        <option value="<?php echo $c['category_id']; ?>" <?php echo ($c['category_id'] == $category['parent_id']) ? 'selected' : ''; ?>>
                            <?php echo $c['category_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" style="background: #007bff; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px;">Cập nhật</button>
        </form>
    </div>
</body>
</html>