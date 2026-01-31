<?php
require_once '../config/config.php';
check_role('guru');

// Ambil data guru
$guru_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT g.* FROM guru g JOIN users u ON g.id_user = u.id_user WHERE u.id_user = {$_SESSION['user_id']}"));

// Ambil kelas
$kelas = mysqli_query($conn, "SELECT k.*, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.id_jurusan = j.id_jurusan ORDER BY k.nama_kelas");

$qr_code = null;
$qr_info = null;

// Proses Generate QR
if (isset($_POST['generate']) || isset($_GET['kelas'])) {
    $id_kelas = isset($_POST['id_kelas']) ? $_POST['id_kelas'] : $_GET['kelas'];
    $durasi = isset($_POST['durasi']) ? $_POST['durasi'] : 10; // Default 10 menit
    
    // Generate token unik
    $token = bin2hex(random_bytes(32));
    $waktu_generate = date('Y-m-d H:i:s');
    $waktu_expired = date('Y-m-d H:i:s', strtotime("+$durasi minutes"));
    $tanggal = date('Y-m-d');
    
    // Simpan ke database
    $insert = mysqli_query($conn, "INSERT INTO qr_absensi (token_qr, id_kelas, id_guru, tanggal, waktu_generate, waktu_expired, status) VALUES ('$token', $id_kelas, {$guru_data['id_guru']}, '$tanggal', '$waktu_generate', '$waktu_expired', 'aktif')");
    
    if ($insert) {
        // Ambil info kelas
        $kelas_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT k.nama_kelas, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.id_jurusan = j.id_jurusan WHERE k.id_kelas = $id_kelas"));
        
        // Generate QR Code URL (menggunakan API gratis)
        $qr_data = $token; // Data yang akan di-encode dalam QR
        $qr_code = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_data);
        
        $qr_info = [
            'kelas' => $kelas_info['nama_kelas'] . ' - ' . $kelas_info['nama_jurusan'],
            'token' => $token,
            'waktu_generate' => $waktu_generate,
            'waktu_expired' => $waktu_expired,
            'durasi' => $durasi
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR Code - Guru</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        // Auto refresh untuk update status expired
        <?php if ($qr_code): ?>
        setTimeout(function() {
            location.reload();
        }, <?php echo $qr_info['durasi'] * 60 * 1000; ?>);
        <?php endif; ?>
    </script>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>👨‍🏫 Guru Panel</h2>
                <p><?php echo $guru_data['nama_guru']; ?></p>
            </div>
            <ul class="sidebar-menu">
    <ul class="sidebar-menu">
        <li><a href="index.php"><span>📊</span> Dashboard</a></li>
        <li><a href="generate-qr.php"><span>📱</span> Generate QR Code</a></li>
        <li><a href="rekap-absensi.php"><span>📝</span> Rekap Absensi</a></li>
        <li><a href="ganti-password.php"><span>🔑</span> Ganti Password</a></li>
        <li><a href="../api/logout.php"><span>🚪</span> Logout</a></li>
    </ul>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Generate QR Code Absensi</h1>
                <p>Buat QR Code untuk absensi siswa</p>
            </div>

            <?php if (!$qr_code): ?>
            <!-- Form Generate QR -->
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <h2>Pilih Kelas & Durasi</h2>
                <form method="POST" style="margin-top: 20px;">
                    <div class="form-group">
                        <label>Pilih Kelas</label>
                        <select name="id_kelas" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php 
                            mysqli_data_seek($kelas, 0);
                            while ($k = mysqli_fetch_assoc($kelas)): 
                            ?>
                                <option value="<?php echo $k['id_kelas']; ?>" <?php echo (isset($_GET['kelas']) && $_GET['kelas'] == $k['id_kelas']) ? 'selected' : ''; ?>>
                                    <?php echo $k['nama_kelas'] . ' - ' . $k['nama_jurusan']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Durasi QR Code (Menit)</label>
                        <select name="durasi" required>
                            <option value="5">5 Menit</option>
                            <option value="10" selected>10 Menit</option>
                            <option value="15">15 Menit</option>
                            <option value="30">30 Menit</option>
                            <option value="60">60 Menit</option>
                        </select>
                    </div>

                    <button type="submit" name="generate" class="btn btn-primary" style="width: 100%;">Generate QR Code</button>
                </form>
            </div>
            <?php else: ?>
            <!-- Tampilkan QR Code -->
            <div class="qr-container">
                <h2>QR Code Absensi</h2>
                <div style="background: #f9fafb; padding: 20px; border-radius: 12px; margin: 20px 0;">
                    <p><strong>Kelas:</strong> <?php echo $qr_info['kelas']; ?></p>
                    <p><strong>Waktu Generate:</strong> <?php echo date('d/m/Y H:i:s', strtotime($qr_info['waktu_generate'])); ?></p>
                    <p><strong>Berlaku Sampai:</strong> <?php echo date('d/m/Y H:i:s', strtotime($qr_info['waktu_expired'])); ?></p>
                    <p><strong>Durasi:</strong> <?php echo $qr_info['durasi']; ?> Menit</p>
                </div>
                
                <img src="<?php echo $qr_code; ?>" alt="QR Code Absensi" style="max-width: 400px; margin: 20px auto; display: block;">
                
                <div class="alert alert-success" style="max-width: 500px; margin: 20px auto;">
                    ✅ QR Code berhasil dibuat! Siswa dapat melakukan scan untuk absensi.
                </div>

                <a href="generate-qr.php" class="btn btn-primary">Generate QR Baru</a>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>