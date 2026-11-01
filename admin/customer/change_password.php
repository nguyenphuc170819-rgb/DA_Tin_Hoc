<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$userObj = new User($conn);

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin nhân viên cần đổi mật khẩu
$stmt = $conn->prepare("SELECT user_id, full_name, email FROM users WHERE user_id = :id LIMIT 1");
$stmt->execute([':id' => $user_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    echo "<h3 style='color:red; text-align:center;'>Tài khoản không tồn tại! <a href='index.php'>Quay lại</a></h3>";
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Mật khẩu nhập lại không khớp!";
    } else {
        // Gọi hàm đổi mật khẩu trong Class
        $result = $userObj->updatePassword($user_id, $new_password);
        if ($result) {
            $success = "Đổi mật khẩu thành công cho tài khoản " . htmlspecialchars($staff['email']) . "!";
        } else {
            $error = "Đổi mật khẩu thất bại, vui lòng thử lại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi Mật khẩu Nhân sự - Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body style="background: #f9f9f9; padding: 20px; font-family: Arial, sans-serif;">
    <div class="admin-container" style="max-width: 450px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Đổi mật khẩu cho: <?php echo htmlspecialchars($staff['full_name']); ?></h2>
        <p style="color: #666; font-size: 14px;">Email: <?php echo htmlspecialchars($staff['email']); ?></p>
        <hr style="margin-bottom: 20px;">
        
        <p><a href="index.php" style="color: #007bff; text-decoration: none;">⬅ Quay lại danh sách nhân sự</a></p>

        <?php if (!empty($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Mật khẩu mới:</label><br>
                <input type="password" name="new_password" required placeholder="Nhập mật khẩu mới..." style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold;">Xác nhận mật khẩu mới:</label><br>
                <input type="password" name="confirm_password" required placeholder="Nhập lại mật khẩu mới..." style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; background: #28a745; color: white; padding: 10px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 15px;">Cập nhật mật khẩu</button>
        </form>
    </div>
</body>
</html>