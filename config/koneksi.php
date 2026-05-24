<?php
ob_start();
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // ganti sesuai user MySQL Anda
define('DB_PASS', '');          // ganti sesuai password MySQL Anda
define('DB_NAME', 'puskesmas_db');
define('APP_NAME', 'Puskesmas Sehat Sejahtera');

// BASE_URL otomatis terdeteksi (tidak perlu diubah manual)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$script   = dirname($_SERVER['SCRIPT_NAME']);
// Normalisasi path: hapus /admin atau /admin/includes di akhir
$basePath = preg_replace('#/(admin|includes|admin/includes)(/.*)?$#', '', $script);
$basePath = rtrim($basePath, '/');
define('BASE_URL', $protocol . '://' . $host . $basePath);

session_start();

function koneksi() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die('<p style="color:red">Koneksi gagal: ' . $conn->connect_error . '</p>');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function bersihkan($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateKode() {
    return 'PKM-' . strtoupper(substr(uniqid(), -5)) . '-' . date('dmy');
}

function isAdminLogin() {
    return isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0;
}

function wajibLogin() {
    if (!isAdminLogin()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}
?>