<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

class Database {
    protected $conn;

    public function __construct() {
        $this->conn = new mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            $_ENV['DB_NAME']
        );
        if ($this->conn->connect_error) {
            die('Koneksi database gagal: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8mb4');
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

\Midtrans\Config::$serverKey    = $_ENV['MIDTRANS_SERVER_KEY'];
\Midtrans\Config::$clientKey    = $_ENV['MIDTRANS_CLIENT_KEY'];
\Midtrans\Config::$isProduction = filter_var($_ENV['MIDTRANS_IS_PRODUCTION'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
\Midtrans\Config::$isSanitized  = true;
\Midtrans\Config::$is3ds        = true;
