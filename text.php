<?php
// Mật khẩu mẫu bạn muốn dùng để đăng nhập (Ví dụ: 123456 hoặc matkhau123)
$raw_password = "123456"; 

// Tạo mã băm chuẩn bằng BCRYPT (giống như hệ thống đang dùng)
$hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);

echo "<h3>Công cụ tạo mã băm mật khẩu (Password Hash Generator)</h3>";
echo "<p><b>Mật khẩu gốc:</b> " . htmlspecialchars($raw_password) . "</p>";
echo "<p><b>Mã Hash để đưa vào CSDL:</b> <input type='text' value='" . $hashed_password . "' style='width: 500px; padding: 5px;' readonly></p>";
echo "<hr>";
echo "<p><i>Bạn chỉ cần copy chuỗi mã hash dài bên trên và dán trực tiếp vào cột <b>password_hash</b> trong bảng <b>users</b> tại phpMyAdmin là có thể dùng mật khẩu <b>123456</b> để đăng nhập bình thường!</i></p>";
echo $hashed_password
?>