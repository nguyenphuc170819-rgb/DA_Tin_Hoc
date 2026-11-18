<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!"; 
    exit;    
}

// 1. Bắt biến từ URL bằng chữ 'action'
$action = isset($_GET["action"]) ? $_GET["action"] : "";

// 2. Gom tất cả vào biến đường dẫn $path dựa theo action
switch ($action) {
    case "spbc":
        $path = ROOT . "/module/product/spbc.php";
        break;
    case "spmoi":
        $path = ROOT . "/module/product/spmoi.php";
        break;
    case "catalog":
        $path = ROOT . "/module/product/product_catalog.php";
        break;
    case "press":
        $path = ROOT . "/module/product/product_press.php";
        break;
    case "detail":
        $path = ROOT . "/module/product/product_detail.php";
        break;
    case "list":
    default:
        // Mặc định nếu không có action hoặc không khớp thì gọi file danh sách chung
        $path = ROOT . "/module/product/list.php"; 
        break;
}

// 3. Kiểm tra tồn tại rồi include một lần duy nhất
if (file_exists($path)) {
    include $path;
} else {
    echo '<div class="alert alert-warning">Tính năng đang được cập nhật!</div>';
}
?>