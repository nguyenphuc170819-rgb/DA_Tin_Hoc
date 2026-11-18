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
    //Đếm số lượng user
    public function countCustomers() {
        return $this->conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    }
    //lấy thông tin user
    public function getAllCustomers() {
        $query = "SELECT user_id, full_name, email, phone_number, is_active, created_at 
                  FROM " . $this->table . " WHERE role = 'customer' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lấy danh sách toàn bộ Admin và Nhân viên (Staff)
    public function getStaffAndAdmins() {
        $query = "SELECT user_id, full_name, email, phone_number, role, is_active, created_at 
                  FROM " . $this->table . " 
                  WHERE role IN ('admin', 'staff') 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm tài khoản Admin hoặc Staff mới (Do Admin tổng tạo)
    public function addStaffOrAdmin($full_name, $email, $phone_number, $password, $role) {
        // Kiểm tra email tồn tại chưa
        $check = $this->conn->prepare("SELECT user_id FROM " . $this->table . " WHERE email = :email LIMIT 1");
        $check->execute([':email' => $email]);
        if ($check->rowCount() > 0) {
            return "Email này đã được sử dụng!";
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table . " (full_name, email, phone_number, password_hash, role, is_active) 
                  VALUES (:full_name, :email, :phone_number, :password_hash, :role, 1)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':full_name' => $full_name,
            ':email' => $email,
            ':phone_number' => $phone_number,
            ':password_hash' => $password_hash,
            ':role' => $role // 'admin' hoặc 'staff'
        ]);
    }
    // Chỉ cập nhật riêng phân quyền (role)
    public function updateRole($user_id, $role) {
        $query = "UPDATE " . $this->table . " SET role = :role WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':role' => $role,
            ':user_id' => $user_id
        ]);
    }
    // Đổi mật khẩu cho tài khoản (dùng cho Admin quản lý nhân sự)
    public function updatePassword($user_id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $query = "UPDATE " . $this->table . " SET password_hash = :password_hash WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':password_hash' => $password_hash,
            ':user_id' => $user_id
        ]);
    }
}
?>