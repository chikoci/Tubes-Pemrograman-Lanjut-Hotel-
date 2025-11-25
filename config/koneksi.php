<?php

// Class Database untuk koneksi PDO
class Database {
    private $host = "localhost";
    private $db_name = "hotel_app";
    private $username = "root";
    private $password = "";
    private $conn = null;

    // dapetin koneksi database
    public function getConnection() {
        if ($this->conn === null) {
            try {
                // Membuat koneksi PDO
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->exec("set names utf8");
            } catch(PDOException $e) {
                echo "Connection Error: " . $e->getMessage();
            }
        }
        return $this->conn;
    }
}
?>
