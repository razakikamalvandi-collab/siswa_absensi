<?php
require_once '../config/config.php';
check_role('siswa');

// Ambil data siswa
$siswa_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, k.nama_kelas FROM siswa s JOIN kelas k ON s.id_kelas = k.id_kelas JOIN users u ON s.id_user = u.id_user WHERE u.id_user = {$_SESSION['user_id']}"));

// Cek apakah sudah absen hari ini
$cek_absen = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM absensi WHERE id_siswa = {$siswa_data['id_siswa']} AND tanggal = CURDATE()"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Absensi - Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>👨‍🎓 Siswa Panel</h2>
                <p><?php echo $siswa_data['nama_siswa']; ?></p>
                <small style="color: #9CA3AF;"><?php echo $siswa_data['nis']; ?></small>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><span>📊</span> Dashboard</a></li>
                <li><a href="scan-absensi.php" class="active"><span>📱</span> Scan Absensi</a></li>
                <li><a href="riwayat-absensi.php"><span>📝</span> Riwayat Absensi</a></li>
                <li><a href="../api/logout.php"><span>🚪</span> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Scan QR Code Absensi</h1>
                <p>Arahkan kamera ke QR Code yang ditampilkan guru</p>
            </div>

            <?php if ($cek_absen): ?>
                <div class="alert alert-success" style="max-width: 600px; margin: 0 auto 30px;">
                    ✅ Anda sudah absen hari ini pada pukul <?php echo date('H:i', strtotime($cek_absen['jam'])); ?> WIB
                </div>
            <?php endif; ?>

            <!-- Scanner Container -->
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <h2>Scanner QR Code</h2>
                
                <div id="reader" style="margin: 20px 0;"></div>
                
                <div id="result" style="margin-top: 20px;"></div>
            </div>
        </main>
    </div>

    <script>
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanner
        html5QrcodeScanner.clear();
        
        // Tampilkan loading
        document.getElementById('result').innerHTML = '<div class="alert alert-warning">⏳ Memproses absensi...</div>';
        
        // Kirim ke server untuk validasi
        fetch('../api/validasi-qr.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'token=' + encodeURIComponent(decodedText)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('result').innerHTML = 
                    '<div class="alert alert-success">✅ ' + data.message + '</div>' +
                    '<a href="index.php" class="btn btn-primary" style="margin-top: 15px;">Kembali ke Dashboard</a>';
            } else {
                document.getElementById('result').innerHTML = 
                    '<div class="alert alert-danger">❌ ' + data.message + '</div>' +
                    '<button onclick="location.reload()" class="btn btn-primary" style="margin-top: 15px;">Scan Ulang</button>';
            }
        })
        .catch(error => {
            document.getElementById('result').innerHTML = 
                '<div class="alert alert-danger">❌ Terjadi kesalahan: ' + error + '</div>' +
                '<button onclick="location.reload()" class="btn btn-primary" style="margin-top: 15px;">Scan Ulang</button>';
        });
    }

    function onScanFailure(error) {
        // Handle scan failure, biasanya karena QR tidak terdeteksi
        // Tidak perlu action, biarkan scanner tetap berjalan
    }

    <?php if (!$cek_absen): ?>
    // Inisialisasi scanner
    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { 
            fps: 10, 
            qrbox: {width: 250, height: 250},
            rememberLastUsedCamera: true
        },
        false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    <?php else: ?>
    document.getElementById('reader').innerHTML = '<div class="alert alert-success">Anda sudah absen hari ini</div>';
    <?php endif; ?>
    </script>
</body>
</html>