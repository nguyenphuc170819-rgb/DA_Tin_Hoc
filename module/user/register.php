<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nhúng file cấu hình, kết nối CSDL và Class User
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/User.php';

$database = new Database();
$db = $database->connect();
$userObj = new User($db);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name    = trim($_POST['full_name']);
    $email        = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password     = trim($_POST['password']);
    $re_password  = trim($_POST['re_password']);

    // Kiểm tra dữ liệu trống
    if (empty($full_name) || empty($email) || empty($phone_number) || empty($password) || empty($re_password)) {
        $error = "Vui lòng nhập đầy đủ tất cả các trường!";
    } elseif ($password !== $re_password) {
        $error = "Mật khẩu nhập lại không khớp!";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự!";
    } else {
        // Gọi hàm register trong Class User đã có sẵn của bạn
        // Hàm này tự kiểm tra email tồn tại và mã hóa password_hash (BCRYPT)
        $result = $userObj->register($full_name, $email, $phone_number, $password);

        if ($result === true) {
            $success = "Đăng ký tài khoản thành công! Bạn có thể <a href='login.php'>đăng nhập tại đây</a>.";
        } else {
            // Nếu hàm trả về chuỗi thông báo lỗi (ví dụ: "Email này đã được sử dụng!")
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản - SHOE STORE</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .register-container { max-width: 450px; margin: 50px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .btn-submit { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .btn-submit:hover { background: #0056b3; }
        .error-msg { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; border: 1px solid #f5c6cb; }
        .success-msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; border: 1px solid #c3e6cb; }
        .links { text-align: center; margin-top: 20px; }
        .links a { color: #007bff; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Đăng Ký Tài Khảo Khách Hàng</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success-msg"><?php echo $success; // Cho phép hiển thị thẻ a link chuyển trang ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="full_name">Họ và tên:</label>
            <input type="text" id="full_name" name="full_name" required value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="email">Địa chỉ Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="phone_number">Số điện thoại:</label>
            <input type="text" id="phone_number" name="phone_number" required value="<?php echo isset($phone_number) ? htmlspecialchars($phone_number) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="re_password">Nhập lại mật khẩu:</label>
            <input type="password" id="re_password" name="re_password" required>
        </div>

        <button type="submit" class="btn-submit">Đăng Ký</button>
    </form>
    
    <div class="links">
        <p>Đã có tài khoản? <a href="<?php echo BASE_URL; ?>/module/user/login.php">Đăng nhập ngay</a></p>
        <p><a href="<?php echo BASE_URL; ?>/index.php">← Quay lại trang chủ</a></p>
    </div>
</div>

</body>
</html>