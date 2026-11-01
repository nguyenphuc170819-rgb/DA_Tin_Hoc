<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xóa toàn bộ các biến trong session
$_SESSION = array();

// Nếu muốn xóa cookie session hoàn toàn
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session
session_destroy();

// Chuyển hướng về trang chủ của website sau khi đăng xuất
header("Location: " . BASE_URL . "/index.php");
exit();
?>