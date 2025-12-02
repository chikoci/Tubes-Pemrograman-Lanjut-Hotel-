<?php
// Class Database untuk koneksi PDO
class Database {
    private $host = "localhost";
    private $db_name = "kluwa_hotel";
    private $username = "root";
    private $password = "";

    public function getConnection() {
        try {
            $conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $conn->exec("SET NAMES utf8");
            return $conn;
        } catch(PDOException $e) {
            die("Connection Error: " . $e->getMessage());
        }
    }
}
?>
