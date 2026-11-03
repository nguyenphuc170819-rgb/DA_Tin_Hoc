<?php
if (!defined("ROOT")) { exit; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= defined('SITE_NAME') ? SITE_NAME : 'SHOE STORE' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS (nếu có) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body class="bg-light">

    <!-- Top bar nhỏ (Thông tin liên hệ / Tài khoản) -->
    <div class="bg-dark text-white py-1 px-3 small d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <i class="fa fa-phone me-1"></i> Hotline:| <i class="fa fa-envelope me-1"></i> 
            </div>
            <div>
                <?php if (isset($_SESSION['user_name'])): ?>
                    <span class="me-3"><i class="fa fa-user me-1"></i> Xin chào, <strong><?= $_SESSION['user_name'] ?></strong></span>
                    <a href="<?= BASE_URL ?>/index.php?mod=user&action=logout" class="text-white text-decoration-none">Đăng xuất</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/index.php?mod=user&action=login" class="text-white text-decoration-none me-3">Đăng nhập</a>
                    <a href="<?= BASE_URL ?>/index.php?mod=user&action=register" class="text-white text-decoration-none">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <!-- Logo thương hiệu -->
            <a class="navbar-brand fw-bold fs-3 text-uppercase" href="<?= BASE_URL ?>/index.php">
                <i class="fa fa-shoe-prints text-warning me-2"></i><?= defined('SITE_NAME') ? SITE_NAME : 'SHOE STORE' ?>
            </a>

            <!-- Nút mobile toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Các mục menu chính -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= BASE_URL ?>/index.php"><i class="fa fa-home me-1"></i> Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php?mod=product&action=spmoi">Sản phẩm mới</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php?mod=product&action=spbc">Bán chạy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php?mod=info&action=gioithieu">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php?mod=info&action=lienhe">Liên hệ</a>
                    </li>
                </ul>

                <!-- Form tìm kiếm nhanh sản phẩm -->
                <form class="d-flex me-3" action="<?= BASE_URL ?>/index.php" method="GET">
                    <input type="hidden" name="mod" value="search">
                    <input class="form-control me-2" type="search" name="keyword" placeholder="Nhập tên giày cần tìm..." required>
                    <button class="btn btn-outline-warning" type="submit"><i class="fa fa-search"></i></button>
                </form>

                <!-- Biểu tượng giỏ hàng -->
                <div class="d-flex align-items-center">
                    <a href="<?= BASE_URL ?>/index.php?mod=cart&action=list" class="btn btn-outline-light position-relative">
                        <i class="fa fa-shopping-cart"></i> Giỏ hàng
                        <!-- Số lượng sản phẩm trong giỏ (Có thể gắn biến động sau) -->
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Khung bọc nội dung tổng của trang (Sẽ chứa cả left.php và nội dung chính bên phải) -->
    <div class="container my-4">
        <div class="row">