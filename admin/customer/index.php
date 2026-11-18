<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();

$userObj = new User($conn);
$customers = $userObj->getAllCustomers();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Khách hàng - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif;">
        <h1>Quản lý Khách hàng</h1>
        <hr>
        <p><a href="../index.php">⬅ Quay lại Dashboard</a></p>

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Trạng thái</th>
                    <th>Ngày đăng ký</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customers)): ?>
                    <?php foreach ($customers as $cus): ?>
                    <tr>
                        <td>#<?php echo $cus['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($cus['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($cus['email']); ?></td>
                        <td><?php echo htmlspecialchars($cus['phone_number']); ?></td>
                        <td>
                            <?php if ($cus['is_active'] == 1): ?>
                                <span style="color: green; font-weight: bold;">Hoạt động</span>
                            <?php else: ?>
                                <span style="color: red; font-weight: bold;">Đã khóa</span>
                            <?php endif; ?>
                        </td>
                        <!-- <td><a href="change_password.php?id=<?php echo $cus['user_id']; ?>" style="background: #17a2b8; color: white; padding: 4px 8px; text-decoration: none; border-radius: 3px; font-size: 12px; font-weight: bold;">🔑 Đổi mật khẩu</a></td> -->
                        <td><?php echo $cus['created_at']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Chưa có khách hàng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>