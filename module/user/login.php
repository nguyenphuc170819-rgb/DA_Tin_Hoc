<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nhúng file kết nối CSDL và Class User (Điều chỉnh đường dẫn theo cấu trúc thực tế của bạn)
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/User.php';

$database = new Database();
$db = $database->connect();
$userObj = new User($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ email và mật khẩu!";
    } else {
        $user = $userObj->login($email, $password);
        
        if ($user) {
            // Lưu thông tin chung vào Session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role']; // 'customer', 'admin', 'staff'

            // Phân quyền điều hướng dựa vào role trong CSDL
            if ($user['role'] == 'admin' || $user['role'] == 'staff') {
                // Nếu là Admin hoặc Nhân viên -> Chuyển thẳng vào khu vực quản trị
                header("Location: " . BASE_URL. "/admin/index.php");
                exit();
            } else {
                // Nếu là Khách hàng thông thường -> Ở lại website mua sắm
                header("Location:" . BASE_URL. "/index.php");
                exit();
            }
        } else {
            $error = "Email hoặc mật khẩu không chính xác, hoặc tài khoản đã bị khóa!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Wed_Shop_Giay</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .login-container { max-width: 400px; margin: 80px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .btn-submit { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn-submit:hover { background: #218838; }
        .error-msg { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; border: 1px solid #f5c6cb; }
        .links { text-align: center; margin-top: 20px; }
        .links a { color: #007bff; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Đăng Nhập Hệ Thống</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="email">Email đăng nhập:</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn-submit">Đăng Nhập</button>
    </form>
    
    <div class="links">
        <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
        <p><a href="../../index.php">← Quay lại trang chủ</a></p>
    </div>
</div>

</body>
</html>