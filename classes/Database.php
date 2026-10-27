<?php
class Database {
    private $host = "localhost";
    private $db_name = "shoe_store_db"; // Tên CSDL shop giày của bạn
    private $username = "root";                  // Tài khoản WampServer mặc định
    private $password = "";                      // Mật khẩu WampServer (để trống nếu không đổi)
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Thiết lập mã hóa UTF-8 và chế độ báo lỗi ngoại lệ
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Lỗi kết nối cơ sở dữ liệu: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>