<?php
if (!defined("ROOT")) {
    echo "You don't have permission to access this page!"; 
    exit;    
}

// 1. Bắt module và action từ URL (mặc định vào module product, action là list)
$mod = isset($_GET["mod"]) ? $_GET["mod"] : "product";
$action = isset($_GET["action"]) ? $_GET["action"] : "list";

// 2. Định tuyến động dựa theo module và action
$path = "";

switch ($mod) {
    case "product":
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
                $path = ROOT . "/module/product/list.php"; 
                break;
        }
        break;
    case "order":
        switch ($action) {
            case 'detail':
                $path = ROOT . "/module/order/detail.php";
                break;
            case 'index':
            default:
                $path = ROOT . "/module/order/index.php";
                break;
        }
        break;
    case "cart":
        // Điều phối cho module Giỏ hàng của bạn
        switch ($action) {
            case "add":
                $path = ROOT . "/module/cart/add.php";
                break;
            case "update":
                $path = ROOT . "/module/cart/update.php";
                break;
            case "delete":
                $path = ROOT . "/module/cart/delete.php";
                break;
            case "checkout":
                $path = ROOT . "/module/cart/checkout.php";
                break;
            case "index":
            default:
                $path = ROOT . "/module/cart/index.php";
                break;
        }
        break;

    case "user":
        // Điều phối cho module Người dùng (Đăng nhập, Đăng ký...)
        switch ($action) {
            case "login":
                $path = ROOT . "/module/user/login.php";
                break;
            case "register":
                $path = ROOT . "/module/user/register.php";
                break;
            case "logout":
                $path = ROOT . "/module/user/logout.php";
                break;
            default:
                $path = ROOT . "/module/user/login.php";
                break;
        }
        break;

    case "info":
        // Điều phối cho các trang thông tin (Giới thiệu, Liên hệ...)
        $path = ROOT . "/module/info/{$action}.php";
        break;

    default:
        $path = ROOT . "/module/info/trangchu.php";
        break;
}

// 3. Kiểm tra tồn tại rồi include một lần duy nhất
if (file_exists($path)) {
    include $path;
} else {
    echo '<div class="alert alert-warning">Tính năng đang được cập nhật!</div>';
}
?>