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
$categoryObj = new Category($conn);

// Lấy số liệu thông qua phương thức từ class
$total_products = $productObj->countProducts();
$total_orders = $orderObj->countOrders();
$total_users = $userObj->countCustomers();
$total_revenue = $orderObj->getTotalRevenue();
$total_categories = $categoryObj->countCategories();

// Lấy danh sách đơn hàng chờ xử lý thông qua phương thức của class Order
$pending_orders = $orderObj->getPendingOrders(5);

// Lấy dữ liệu doanh thu theo tháng trong năm hiện tại
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$revenue_by_month = $orderObj->getRevenueByMonth($selected_year);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Thêm FontAwesome cho đẹp icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif;">
        <h1><i class="fa fa-tachometer-alt"></i> Bảng điều khiển Quản Trị (Dashboard)</h1>
        <hr>
        <p>Xin chào, <b><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></b> | <a href="<?php echo BASE_URL; ?>/module/user/logout.php">Đăng xuất</a></p>
        
        <!-- Menu điều hướng -->
        <div class="nav-menu" style="margin: 20px 0; background: #eee; padding: 10px; border-radius: 5px;">
            <b>Menu quản lý:</b> 
            <a href="index.php">Trang chủ</a> | 
            <a href="product/index.php">Sản phẩm</a> | 
            <a href="category/index.php">Danh mục</a> | 
            <a href="order/index.php">Đơn hàng</a> | 
            <a href="customer/index.php">Khách hàng</a> |
            <a href="user/index.php">Nhân viên</a> |
            <a href="<?php echo BASE_URL; ?>/index.php" target="_blank">Xem Website 🌐</a>
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

        <!-- ================= PHẦN ĐƠN HÀNG MỚI CẦN XỬ LÝ ================= -->
        <div style="background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #d9534f;"><i class="fa fa-clock"></i> Đơn hàng mới cần xử lý gấp (Chờ xác nhận)</h3>
                <a href="order/index.php" style="text-decoration: none; color: #007bff; font-weight: bold;">Xem tất cả đơn hàng &rarr;</a>
            </div>

            <div style="overflow-x: auto;">
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center; border-color: #ddd;">
                    <thead>
                        <tr style="background: #fff3cd; color: #856404;">
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Thời gian đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pending_orders)): ?>
                            <?php foreach ($pending_orders as $p_order): ?>
                            <tr>
                                <td><b>#<?php echo $p_order['order_id']; ?></b></td>
                                <td><?php echo htmlspecialchars($p_order['shipping_full_name']); ?></td>
                                <td><?php echo htmlspecialchars($p_order['shipping_phone']); ?></td>
                                <td style="color: #d9534f; font-weight: bold;"><?php echo number_format($p_order['final_amount'], 0, ',', '.'); ?> đ</td>
                                <td>
                                    <span style="padding: 2px 6px; border-radius: 3px; font-size: 11px; background: <?php echo ($p_order['payment_status'] == 'paid') ? '#d4edda; color: #155724;' : '#fff3cd; color: #856404;'; ?>">
                                        <?php echo ucfirst($p_order['payment_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $p_order['created_at']; ?></td>
                                <td>
                                    <a href="order/detail.php?id=<?php echo $p_order['order_id']; ?>" style="background: #007bff; color: white; padding: 5px 10px; border-radius: 3px; text-decoration: none; font-size: 12px;">Xử lý ngay</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 15px; color: #6c757d;">Tuyệt vời! Không có đơn hàng nào đang chờ xử lý.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- ================= KẾT THÚC PHẦN ĐƠN HÀNG MỚI ================= -->

        <!-- ================= PHẦN QUẢN LÝ DOANH THU THEO THỜI GIAN ================= -->
        <div style="background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;"><i class="fa fa-chart-line text-primary"></i> Thống kê doanh thu theo tháng (Năm <?= $selected_year ?>)</h3>
                
                <!-- Form chọn năm thống kê -->
                <form method="GET" action="" style="display: flex; gap: 10px; align-items: center;">
                    <label for="year">Chọn năm:</label>
                    <select name="year" id="year" onchange="this.form.submit()" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc;">
                        <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                            <option value="<?= $y ?>" <?= ($selected_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>

            <div style="overflow-x: auto;">
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center; border-color: #ddd;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th>Tháng</th>
                            <th>Số lượng đơn hoàn thành</th>
                            <th>Tổng doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Tạo mảng đủ 12 tháng để hiển thị trực quan
                        $months_data = array_fill(1, 12, ['doanh_thu' => 0, 'tong_don' => 0]);
                        foreach ($revenue_by_month as $item) {
                            $m = intval($item['thong_ke_thang']);
                            $months_data[$m] = [
                                'doanh_thu' => $item['doanh_thu'],
                                'tong_don' => $item['tong_don']
                            ];
                        }
                        
                        $total_year_revenue = 0;
                        foreach ($months_data as $month => $data): 
                            $total_year_revenue += $data['doanh_thu'];
                            if($data['tong_don']>0){
                        ?>
                        <tr>
                            <td><b>Tháng <?= $month ?></b></td>
                            <td><?= $data['tong_don'] ?> đơn</td>
                            <td style="color: #d9534f; font-weight: bold;"><?= number_format($data['doanh_thu'], 0, ',', '.') ?> đ</td>
                        </tr>
                        <?php }endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: #f1f1f1; font-weight: bold;">
                            <td colspan="2" style="text-align: right;">Tổng doanh thu năm <?= $selected_year ?>:</td>
                            <td style="color: #28a745; font-size: 18px;"><?= number_format($total_year_revenue, 0, ',', '.') ?> đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- ================= KẾT THÚC PHẦN THỐNG KÊ ================= -->

    </div>
</body>
</html>