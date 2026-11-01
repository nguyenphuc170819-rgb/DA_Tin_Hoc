<?php
// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập chưa và có phải là admin hoặc staff không
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    
    // Nếu không thỏa mãn điều kiện -> Chặn lại và đá về trang đăng nhập của module/user
    // (Điều chỉnh lại đường dẫn ../ tùy theo vị trí thực tế file của bạn so với thư mục module/user/login.php)
    header("Location:". BASE_URL . " /module/user/login.php?error=unauthorized");
    exit();
}
?>