<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/Category.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$productObj = new Product($conn);
$catObj = new Category($conn);

// Lấy ID sản phẩm cần sửa từ URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productObj->getProductById($product_id);

if (!$product) {
    echo "<h3 style='color:red; text-align:center; margin-top:50px;'>Sản phẩm không tồn tại! <a href='index.php'>Quay lại danh sách</a></h3>";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'product_name' => trim($_POST['product_name']),
        'slug' => trim($_POST['slug']),
        'brand_id' => $_POST['brand_id'],
        'category_id' => $_POST['category_id'],
        'description' => trim($_POST['description']),
        'base_price' => $_POST['base_price'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    if (empty($data['product_name']) || empty($data['base_price'])) {
        $error = "Vui lòng nhập tên giày và giá cơ bản!";
    } else {
        // Thực hiện cập nhật trong database (Bạn có thể bổ sung phương thức updateProduct vào Product.php nếu chưa có)
        $query = "UPDATE products SET product_name = :product_name, slug = :slug, brand_id = :brand_id, category_id = :category_id, description = :description, base_price = :base_price, is_active = :is_active WHERE product_id = :product_id";
        $stmt = $conn->prepare($query);
        $check = $stmt->execute([
            ':product_name' => $data['product_name'],
            ':slug' => $data['slug'],
            ':brand_id' => $data['brand_id'],
            ':category_id' => $data['category_id'],
            ':description' => $data['description'],
            ':base_price' => $data['base_price'],
            ':is_active' => $data['is_active'],
            ':product_id' => $product_id
        ]);

        if ($check) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Cập nhật sản phẩm thất bại!";
        }
    }
}

$categories = $catObj->getAllCategories();
$brands = $catObj->getAllBrands();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản phẩm - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif; max-width: 600px;">
        <h1>Sửa Sản phẩm #<?php echo $product['product_id']; ?></h1>
        <hr>
        <p><a href="index.php">⬅ Quay lại danh sách</a></p>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label>Tên giày:</label><br>
                <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Slug:</label><br>
                <input type="text" name="slug" value="<?php echo htmlspecialchars($product['slug']); ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px; display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Danh mục:</label><br>
                    <select name="category_id" style="width: 100%; padding: 8px; margin-top: 5px;">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo ($cat['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                <?php echo $cat['category_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>Thương hiệu:</label><br>
                    <select name="brand_id" style="width: 100%; padding: 8px; margin-top: 5px;">
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand['brand_id']; ?>" <?php echo ($brand['brand_id'] == $product['brand_id']) ? 'selected' : ''; ?>>
                                <?php echo $brand['brand_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Giá cơ bản (VNĐ):</label><br>
                <input type="number" step="0.01" name="base_price" value="<?php echo $product['base_price']; ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Mô tả giày:</label><br>
                <textarea name="description" rows="4" style="width: 100%; padding: 8px; margin-top: 5px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div style="margin-bottom: 15px;">
                <label><input type="checkbox" name="is_active" value="1" <?php echo ($product['is_active'] == 1) ? 'checked' : ''; ?>> Đang kinh doanh (Hiển thị lên web)</label>
            </div>
            <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 3px; font-weight: bold;">Cập nhật sản phẩm</button>
        </form>
    </div>
</body>
</html>