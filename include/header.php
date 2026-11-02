<?php
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer') {
    echo '<span>Xin chào, ' . htmlspecialchars($_SESSION['full_name']) . '</span>';
    echo '<a href="module/user/logout.php">Đăng xuất</a>';
} else {
    echo '<a href="module/user/login.php">Đăng nhập</a>';
    echo '<a href="module/user/register.php">Đăng ký</a>';
}
?>