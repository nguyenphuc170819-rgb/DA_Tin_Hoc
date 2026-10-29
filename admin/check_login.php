<?php
// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kiểm tra xem người dùng đã đăng nhập chưa (có session user_id chưa)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Chưa đăng nhập -> Đá văng về trang đăng nhập ở thư mục gốc
    header("Location: __DIR__ .../../login.php");
    exit;
}

// 2. Kiểm tra xem role có phải là nội bộ (admin hoặc staff) hay không
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    // Nếu là customer cố tình gõ URL vào admin -> Chặn lại hoặc đá về trang chủ
    echo "<h3 style='color:red; text-align:center; margin-top:50px;'>Bạn không có quyền truy cập vào khu vực quản trị!</h3>";
    echo "<p style='text-align:center;'><a href='../../index.php'>Quay về trang chủ</a></p>";
    exit;
}
?>