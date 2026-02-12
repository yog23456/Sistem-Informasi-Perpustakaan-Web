<?php
require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Jakarta');

// Load environment variables dari file .env di root
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Konfigurasi Database
$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_PORT = (int) ($_ENV['DB_PORT'] ?? 3306);
$DB_NAME = $_ENV['DB_NAME'] ?? 'perpustakaan';
$DB_USER = $_ENV['DB_USER'] ?? 'root';
$DB_PASS = $_ENV['DB_PASSWORD'] ?? ''; // Sesuaikan key .env kamu
$BASE_PATH = $_ENV['BASE_PATH'] ?? '/perpustakaan';

$connection = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- SISTEM DINAMIS MULTI-THEME ---
// 1. Set default dari .env jika database belum siap
$THEME = $_ENV['THEME'] ?? 'default';

// 2. Ambil tema aktif dari tabel 'settings'
$sql_theme = "SELECT setting_value FROM settings WHERE setting_key = 'active_theme' LIMIT 1";
$res_theme = mysqli_query($connection, $sql_theme);

if ($res_theme && mysqli_num_rows($res_theme) > 0) {
    $row_theme = mysqli_fetch_assoc($res_theme);
    if (!empty($row_theme['setting_value'])) {
        $THEME = $row_theme['setting_value']; // Mengambil nilai 'default', 'mazer', atau 'pluto'
    }
}

// Definisikan konstanta untuk digunakan di seluruh aplikasi
if (!defined('BASE_URL')) define('BASE_URL', $BASE_PATH);
if (!defined('THEME_PATH')) define('THEME_PATH', $THEME);
?>