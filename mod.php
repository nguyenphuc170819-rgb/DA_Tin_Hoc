<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!"; exit;    
}

// Lấy module chính từ URL, mặc định có thể để trống hoặc về info/trang chủ
$mod = isset($_GET["mod"]) ? $_GET["mod"] : "";

// Xác định file điều phối của từng module
switch ($mod) {
    case "info":
        $path = ROOT . "/module/info/index.php";
        break;
    case "product":
        $path = ROOT . "/module/product/index.php"; // Chuyển giao sang file index.php trong thư mục product
        break;
    case "news":
        $path = ROOT . "/module/news/index.php";
        break;
    case "cart":
        $path = ROOT . "/module/cart/index.php";
        break;
    case "search":
        $path = ROOT . "/module/product/search.php";
        break;
    default:
        $path = ROOT . "/module/info/trangchu.php"; // Mặc định về trang chủ
        break;
}

// Include file module tương ứng
if (file_exists($path)) {
    include $path;
} else {
    echo '<div class="alert alert-warning p-3">Module không tồn tại!</div>';
}
?>