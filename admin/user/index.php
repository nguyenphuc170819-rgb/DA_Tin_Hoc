<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$userObj = new User($conn);

// Xử lý khi Admin bấm nút đổi quyền nhanh
if (isset($_GET['action']) && $_GET['action'] == 'changerole') {
    $target_id = (int)$_GET['id'];
    $current_role = $_GET['current'];
    
    // Đảo ngược quyền: Nếu đang là admin thì chuyển thành staff và ngược lại
    $new_role = ($current_role == 'admin') ? 'staff' : 'admin';
    
    $userObj->updateRole($target_id, $new_role);
    header("Location: index.php");
    exit;
}

$staff_list = $userObj->getStaffAndAdmins();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Nhân sự (Admin & Staff) - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif;">
        <h1>Quản lý Tài khoản Nội bộ (Admin & Staff)</h1>
        <hr>
        <p>
            <a href="../index.php">⬅ Quay lại Dashboard</a> | 
            <a href="add.php" style="background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">+ Thêm tài khoản nhân sự</a>
        </p>

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 20px; text-align: center;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò (Role)</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($staff_list)): ?>
                    <?php foreach ($staff_list as $staff): ?>
                    <tr>
                        <td>#<?php echo $staff['user_id']; ?></td>
                        <td style="text-align: left;"><b><?php echo htmlspecialchars($staff['full_name']); ?></b></td>
                        <td style="text-align: left;"><?php echo htmlspecialchars($staff['email']); ?></td>
                        <td><?php echo htmlspecialchars($staff['phone_number']); ?></td>
                        <td>
                            <span style="padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 12px;
                                <?php echo ($staff['role'] == 'admin') ? 'background: #f8d7da; color: #721c24;' : 'background: #d1ecf1; color: #0c5460;'; ?>">
                                <?php echo strtoupper($staff['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($staff['is_active'] == 1): ?>
                                <span style="color: green; font-weight: bold;">Hoạt động</span>
                            <?php else: ?>
                                <span style="color: red; font-weight: bold;">Đã khóa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            
                            <!-- Nút bấm đổi quyền nhanh -->
                            <a href="index.php?action=changerole&id=<?php echo $staff['user_id']; ?>&current=<?php echo $staff['role']; ?>" 
                               onclick="return confirm('Bạn có chắc muốn đổi quyền của tài khoản này không?');" 
                               style="color: #000; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 13px; font-weight: bold;">
                               Đổi role <?php echo ($staff['role'] == 'admin') ? 'Staff' : 'Admin'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Chưa có tài khoản nhân sự nào khác.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>