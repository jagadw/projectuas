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

if (!function_exists('render_message')) {
    function render_message($message) {
        // Ganti **text** dengan <b>$1</b>
        $message = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $message);
        // Ganti *text* dengan <i>$1</i>
        $message = preg_replace('/\*(.*?)\*/', '<i>$1</i>', $message);
        // Ganti baris baru \n menjadi <br>
        $message = nl2br((string)$message);
        // Sanitasi input dengan mengijinkan tag HTML dasar
        return strip_tags($message, '<b><i><u><strong><em><a><br>');
    }
}

