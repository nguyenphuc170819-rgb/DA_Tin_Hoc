<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$userObj = new User($conn);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // 'admin' hoặc 'staff'

    if (empty($full_name) || empty($email) || empty($password) || empty($role)) {
        $error = "Vui lòng nhập đầy đủ thông tin bắt buộc!";
    } else {
        $result = $userObj->addStaffOrAdmin($full_name, $email, $phone_number, $password, $role);
        if ($result === true) {
            header("Location: index.php");
            exit;
        } else {
            $error = $result; // Thông báo lỗi từ hàm addStaffOrAdmin (ví dụ: email trùng)
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Tài khoản Nhân sự - Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
    <div class="admin-container" style="padding: 20px; font-family: Arial, sans-serif; max-width: 500px;">
        <h1>Thêm Tài khoản Quản trị / Nhân viên</h1>
        <hr>
        <p><a href="index.php">⬅ Quay lại danh sách nhân sự</a></p>

        <?php if (!empty($error)): ?>
            <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label>Họ và tên:</label><br>
                <input type="text" name="full_name" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Email đăng nhập:</label><br>
                <input type="email" name="email" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Số điện thoại:</label><br>
                <input type="text" name="phone_number" style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Mật khẩu tạm tính:</label><br>
                <input type="password" name="password" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Phân quyền (Role):</label><br>
                <select name="role" style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="staff">Nhân viên (Staff)</option>
                    <option value="admin">Quản trị viên (Admin)</option>
                </select>
            </div>
            <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 3px; font-weight: bold;">Tạo tài khoản</button>
        </form>
    </div>
</body>
</html>