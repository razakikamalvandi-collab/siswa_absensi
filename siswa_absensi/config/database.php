<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'razaki170309');
define('DB_NAME', 'absensi_siswa');
define('DB_PORT', '3307'); // ⭐ TAMBAHKAN INI

// Koneksi Database dengan PORT
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT); // ⭐ TAMBAHKAN DB_PORT

// Cek Koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>