<?php
// Kiểm tra chống truy cập trực tiếp
if (!defined("ROOT") && !defined("ROOT_PATH")) {
    // Nếu chưa định nghĩa ROOT hoặc ROOT_PATH, tự động xác định
    define('ROOT', dirname(__DIR__, 2)); // Điều chỉnh tùy cấu trúc gốc
}

/**
 * Định dạng tiền tệ VND (Ví dụ: 1500000 -> 1.500.000 đ)
 */
if (!function_exists('format_currency')) {
    function format_currency($amount) {
        if (!is_numeric($amount)) {
            $amount = 0;
        }
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}

/**
 * Chuyển đổi chuỗi tiếng Việt có dấu thành slug không dấu (Dùng cho URL sản phẩm/danh mục)
 */
if (!function_exists('create_slug')) {
    function create_slug($string) {
        $search = [
            '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
            '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
            '#(ì|í|ị|ỉ|ĩ)#',
            '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
            '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
            '#(ỳ|ý|ỵ|ỷ|ỹ)#',
            '#(đ)#',
            '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
            '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
            '#(Ì|Í|Ị|Ỉ|Ĩ)#',
            '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
            '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
            '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
            '#(Đ)#',
            "/[^a-zA-Z0-9\-\s]/"
        ];
        $replace = [
            'a', 'e', 'i', 'o', 'u', 'y', 'd',
            'A', 'E', 'I', 'O', 'U', 'Y', 'D',
            ''
        ];
        $string = preg_replace($search, $replace, $string);
        $string = preg_replace('/\s+/', '-', trim($string));
        return strtolower($string);
    }
}

/**
 * Xử lý làm sạch dữ liệu đầu vào (Chống XSS cơ bản)
 */
if (!function_exists('clean_input')) {
    function clean_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

/**
 * Hàm điều hướng trang an toàn bằng PHP Header
 */
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: " . $url);
        exit();
    }
}

/**
 * Hàm hỗ trợ upload hình ảnh (Dùng cho Admin thêm/sửa sản phẩm)
 */
if (!function_exists('upload_image')) {
    function upload_image($file_input_name, $target_dir = "../image/") {
        if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$file_input_name];
        $file_name = time() . '_' . basename($file['name']);
        $target_file = rtrim($target_dir, '/') . '/' . $file_name;
        
        // Kiểm tra định dạng ảnh hợp lệ
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (!in_array($imageFileType, $allowed_types)) {
            return false; // Sai định dạng
        }

        // Tiến hành di chuyển file vào thư mục đích
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $file_name; // Trả về tên file để lưu vào database
        }
        
        return false;
    }
}
?>