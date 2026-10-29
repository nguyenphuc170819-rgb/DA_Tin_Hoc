<?php
// Sửa lại đường dẫn lùi 1 cấp chính xác từ thư mục admin/ ra thư mục gốc
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Category.php'; 
require_once __DIR__ . '/check_login.php';
// Khởi tạo kết nối CSDL
$db = new Database();
$conn = $db->connect();

// Khởi tạo các đối tượng nghiệp vụ
$productObj = new Product($conn);
$orderObj = new Order($conn);
$userObj = new User($conn);
$categoryObj = new Category($conn); // Khởi tạo danh mục

// Lấy số liệu thông qua phương thức từ class
$total_products = $productObj->countProducts();
$total_orders = $orderObj->countOrders();
$total_users = $userObj->countCustomers();
$total_revenue = $orderObj->getTotalRevenue();
$total_categories = $categoryObj->countCategories(); // Thêm hàm đếm danh mục trong class Category
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif;">
        <h1>Bảng điều khiển Quản Trị (Dashboard)</h1>
        <hr>
        <p>Xin chào, <b><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></b> | <a href="../logout.php">Đăng xuất</a></p>
        
        <!-- Menu điều hướng -->
        <div class="nav-menu" style="margin: 20px 0; background: #eee; padding: 10px; border-radius: 5px;">
            <b>Menu quản lý:</b> 
            <a href="index.php">Trang chủ</a> | 
            <a href="product/index.php">Sản phẩm</a> | 
            <a href="category/index.php">Danh mục</a> | 
            <a href="order/index.php">Đơn hàng</a> | 
            <a href="customer/index.php">Khách hàng</a> |
            <a href="user/index.php">Nhân viên</a> |
            <a href=" ../index.php" target="_blank">Xem Website 🌐</a>
        </div>

        <!-- Các hộp thống kê KPI -->
        <h2>Thống kê tổng quan</h2>
        <div class="stats-grid" style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
            <div style="background: #e3f2fd; padding: 20px; flex: 1; min-width: 200px; border-radius: 5px; border-left: 5px solid #2196f3;">
                <h3>Tổng sản phẩm</h3>
                <p style="font-size: 24px; font-weight: bold; color: #0d47a1;"><?php echo $total_products; ?></p>
            </div>
            <div style="background: #e8f5e9; padding: 20px; flex: 1; min-width: 200px; border-radius: 5px; border-left: 5px solid #4caf50;">
                <h3>Tổng đơn hàng</h3>
                <p style="font-size: 24px; font-weight: bold; color: #1b5e20;"><?php echo $total_orders; ?></p>
            </div>
            <div style="background: #fff3e0; padding: 20px; flex: 1; min-width: 200px; border-radius: 5px; border-left: 5px solid #ff9800;">
                <h3>Khách hàng</h3>
                <p style="font-size: 24px; font-weight: bold; color: #e65100;"><?php echo $total_users; ?></p>
            </div>
            <div style="background: #ede7f6; padding: 20px; flex: 1; min-width: 200px; border-radius: 5px; border-left: 5px solid #673ab7;">
                <h3>Danh mục</h3>
                <p style="font-size: 24px; font-weight: bold; color: #311b92;"><?php echo $total_categories ?? 0; ?></p>
            </div>
            <div style="background: #fce4ec; padding: 20px; flex: 1.5; min-width: 250px; border-radius: 5px; border-left: 5px solid #e91e63;">
                <h3>Doanh thu (Đã hoàn thành)</h3>
                <p style="font-size: 24px; font-weight: bold; color: #880e4f;"><?php echo number_format($total_revenue, 0, ',', '.'); ?> đ</p>
            </div>
        </div>

        <!-- Khu vực hiển thị bảng dữ liệu nhanh (Đơn hàng mới / Cảnh báo kho) -->
        <!-- <div style="margin-top: 30px;">
            <h3>Đơn hàng mới cần xử lý gần đây</h3> -->
            <!-- Bạn có thể gọi phương thức lấy danh sách đơn hàng mới từ $orderObj->getRecentOrders() ở đây và hiện bảng HTML -->
            <!-- <p style="color: #666; font-style: italic;">(Gợi ý: Hiển thị bảng danh sách 5-10 đơn hàng gần nhất tại đây)</p>
        </div> -->
    </div>
</body>
</html>