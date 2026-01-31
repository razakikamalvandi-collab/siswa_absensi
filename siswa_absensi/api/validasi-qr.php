<?php
require_once '../config/config.php';
header('Content-Type: application/json');

// Pastikan user sudah login sebagai siswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Ambil token dari POST
$token = isset($_POST['token']) ? clean_input($_POST['token']) : '';

if (empty($token)) {
    echo json_encode(['success' => false, 'message' => 'Token QR tidak valid']);
    exit;
}

// Ambil data siswa
$siswa_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.* FROM siswa s JOIN users u ON s.id_user = u.id_user WHERE u.id_user = {$_SESSION['user_id']}"));

if (!$siswa_data) {
    echo json_encode(['success' => false, 'message' => 'Data siswa tidak ditemukan']);
    exit;
}

// Cek apakah sudah absen hari ini
$cek_absen = mysqli_query($conn, "SELECT * FROM absensi WHERE id_siswa = {$siswa_data['id_siswa']} AND tanggal = CURDATE()");
if (mysqli_num_rows($cek_absen) > 0) {
    echo json_encode(['success' => false, 'message' => 'Anda sudah absen hari ini']);
    exit;
}

// Validasi QR Code
$qr_query = mysqli_query($conn, "SELECT * FROM qr_absensi WHERE token_qr = '$token' AND status = 'aktif'");

if (mysqli_num_rows($qr_query) == 0) {
    echo json_encode(['success' => false, 'message' => 'QR Code tidak valid atau sudah tidak aktif']);
    exit;
}

$qr_data = mysqli_fetch_assoc($qr_query);

// Cek apakah QR sudah expired
$now = date('Y-m-d H:i:s');
if ($now > $qr_data['waktu_expired']) {
    // Update status QR menjadi expired
    mysqli_query($conn, "UPDATE qr_absensi SET status = 'expired' WHERE id_qr = {$qr_data['id_qr']}");
    echo json_encode(['success' => false, 'message' => 'QR Code sudah kadaluarsa']);
    exit;
}

// Simpan absensi
$tanggal = date('Y-m-d');
$jam = date('H:i:s');
$insert = mysqli_query($conn, "INSERT INTO absensi (id_siswa, id_qr, tanggal, jam, status) VALUES ({$siswa_data['id_siswa']}, {$qr_data['id_qr']}, '$tanggal', '$jam', 'hadir')");

if ($insert) {
    echo json_encode([
        'success' => true, 
        'message' => 'Absensi berhasil! Selamat, Anda hadir pada ' . date('d/m/Y H:i', strtotime("$tanggal $jam")) . ' WIB'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan absensi']);
}
?>