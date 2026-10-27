<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đăng ký tài khoản mới
    public function register($full_name, $email, $phone_number, $password) {
        // Kiểm tra xem email đã tồn tại chưa
        $check_query = "SELECT user_id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            return "Email này đã được sử dụng!";
        }

        // Mã hóa mật khẩu bảo mật
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO " . $this->table . " (full_name, email, phone_number, password_hash, role) 
                  VALUES (:full_name, :email, :phone_number, :password_hash, 'customer')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':password_hash', $password_hash);

        if($stmt->execute()) {
            return true;
        }
        return "Đăng ký thất bại, vui lòng thử lại!";
    }

    // Đăng nhập hệ thống
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email AND is_active = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Kiểm tra mật khẩu đã mã hóa
            if(password_verify($password, $user['password_hash'])) {
                return $user; // Trả về mảng thông tin user để lưu vào Session
            }
        }
        return false;
    }
}
?>