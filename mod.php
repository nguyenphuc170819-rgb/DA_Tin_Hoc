<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!"; 
    exit;    
}

// 1. Lấy module chính từ URL (mặc định để trống hoặc về info)
$mod = isset($_GET["mod"]) ? trim($_GET["mod"]) : "";

// 2. Xác định file điều phối của từng module
switch ($mod) {
    case "info":
        $path = ROOT . "/module/info/index.php";
        break;
    case "product":
        $path = ROOT . "/module/index.php"; 
        break;
    case "cart":
        $path = ROOT . "/module/index.php"; 
        break;
    case "news":
        $path = ROOT . "/module/index.php"; 
        break;
    case "user":
        $path = ROOT . "/module/index.php"; 
        break;
    case "order":
        $path = ROOT . "/module/index.php"; 
        break;
    case "search":
        $path = ROOT . "/module/product/search.php";
        break;
    default:
        $path = ROOT . "/module/info/trangchu.php"; // Mặc định về trang chủ
        break;
}

// 3. Include file điều phối tương ứng
if (file_exists($path)) {
    include $path;
} else {
    echo '<div class="alert alert-warning p-3">Module không tồn tại!</div>';
}
?>