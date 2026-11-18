<?php
if (!defined("ROOT"))
{
	echo "You don't have permission to access this page!"; exit;	
}
?>
lien he<?php
if (!defined("ROOT")) { exit; }

$success_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $message = clean_input($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($message)) {
        $success_msg = "Cảm ơn bạn đã liên hệ! Chúng tôi đã nhận được thông tin và sẽ phản hồi sớm nhất.";
    }
}
?>
<div class="container my-4">
    <div class="row">
        <!-- Form liên hệ -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h3 class="fw-bold text-dark mb-3"><i class="fa fa-envelope text-primary"></i> Gửi tin nhắn cho chúng tôi</h3>
                <p class="text-muted">Bạn có thắc mắc về size giày, đơn hàng hay cần tư vấn? Hãy điền thông tin vào form dưới đây.</p>
                
                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success"><?= $success_msg ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ tên của bạn</label>
                        <input type="text" name="name" class="form-control" required placeholder="Nhập họ tên...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="Nhập email...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung tin nhắn</label>
                        <textarea name="message" class="form-control" rows="4" required placeholder="Nhập nội dung cần tư vấn..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Gửi thông điệp</button>
                </form>
            </div>
        </div>

        <!-- Thông tin cửa hàng -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100 bg-light">
                <h3 class="fw-bold text-dark mb-3"><i class="fa fa-map-marker-alt text-danger"></i> Thông tin cửa hàng</h3>
                <p class="text-muted">Hãy ghé thăm cửa hàng trực tiếp của chúng tôi để trải nghiệm sản phẩm chân thực nhất.</p>
                <ul class="list-unstyled text-muted">
                    <li class="mb-3"><i class="fa fa-building text-primary me-2"></i> <strong>Địa chỉ:</strong> 123 Đường Thể Thao, Quận 1, TP. Hồ Chí Minh</li>
                    <li class="mb-3"><i class="fa fa-phone text-success me-2"></i> <strong>Hotline:</strong> 0909 123 456</li>
                    <li class="mb-3"><i class="fa fa-envelope text-warning me-2"></i> <strong>Email:</strong> support@<?= strtolower(str_replace(' ', '', SITE_NAME)) ?>.com</li>
                    <li class="mb-3"><i class="fa fa-clock text-info me-2"></i> <strong>Giờ mở cửa:</strong> 8:00 - 21:00 (Thứ Hai - Chủ Nhật)</li>
                </ul>
            </div>
        </div>
    </div>
</div>