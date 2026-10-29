<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

$db = new Database();
$conn = $db->connect();
$userObj = new User($conn);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ email và mật khẩu!";
    } else {
        // Gọi phương thức đăng nhập từ trong Class User
        $user = $userObj->login($email, $password);

        if ($user) {
            if ($user['is_active'] == 0) {
                $error = "Tài khoản của bạn đã bị khóa!";
            } else {
                // Lưu thông tin vào Session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; // 'admin', 'staff', hoặc 'customer'

                // Phân quyền điều hướng
                if ($user['role'] == 'admin' || $user['role'] == 'staff') {
                    header("Location: index.php");
                    exit;
                } else {
                    header("Location: index.php");
                    exit;
                }
            }
        } else {
            $error = "Email hoặc mật khẩu không chính xác!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Shop Giày</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; font-family: Arial, sans-serif;">
        <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Đăng nhập hệ thống</h2>

        <?php if (!empty($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; font-size: 14px;">Email:</label><br>
                <input type="email" name="email" required placeholder="Nhập email của bạn..." style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold; font-size: 14px;">Mật khẩu:</label><br>
                <input type="password" name="password" required placeholder="Nhập mật khẩu..." style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; background: #007bff; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px;">Đăng nhập</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 14px;">
            Chưa có tài khoản? <a href="register.php" style="color: #007bff; text-decoration: none;">Đăng ký ngay</a>
        </p>
    </div>
</body>
</html>