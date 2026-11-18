<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!"; 
    exit;    
}

// Bắt biến từ URL bằng chữ 'action', mặc định là trang chủ (trangchu)
$action = isset($_GET["action"]) ? $_GET["action"] : "trangchu";

switch ($action) {
    case "gioithieu":
        $sub_path = ROOT . "/module/info/gioithieu.php";
        break;
    case "lienhe":
        $sub_path = ROOT . "/module/info/lienhe.php";
        break;
    case "trangchu":
    default:
        $sub_path = ROOT . "/module/info/trangchu.php";
        break;
}

if (file_exists($sub_path)) {
    include $sub_path;
} else {
    echo '<div class="alert alert-warning">Trang thông tin không tồn tại!</div>';
}
?>