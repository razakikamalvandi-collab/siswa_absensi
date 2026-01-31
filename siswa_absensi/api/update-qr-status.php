<?php
require_once '../config/config.php';

// Update semua QR yang sudah melewati waktu expired
$now = date('Y-m-d H:i:s');
$update = mysqli_query($conn, "UPDATE qr_absensi SET status = 'expired' WHERE waktu_expired < '$now' AND status = 'aktif'");

if ($update) {
    echo "QR status updated successfully";
} else {
    echo "Error updating QR status";
}
?>