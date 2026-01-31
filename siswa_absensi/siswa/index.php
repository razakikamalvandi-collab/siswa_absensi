<?php
require_once '../config/config.php';
check_role('siswa');

// Ambil data siswa
$siswa_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, k.nama_kelas, j.nama_jurusan FROM siswa s JOIN kelas k ON s.id_kelas = k.id_kelas JOIN jurusan j ON k.id_jurusan = j.id_jurusan JOIN users u ON s.id_user = u.id_user WHERE u.id_user = {$_SESSION['user_id']}"));

// Hitung statistik absensi bulan ini
$bulan_ini = date('Y-m');
$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    COUNT(*) as total_absen,
    SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
    SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
    SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
    SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha
    FROM absensi WHERE id_siswa = {$siswa_data['id_siswa']} AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'"));

// Cek apakah sudah absen hari ini
$cek_absen = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM absensi WHERE id_siswa = {$siswa_data['id_siswa']} AND tanggal = CURDATE()"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
    <ul class="sidebar-menu">
        <li><a href="index.php"><span>📊</span> Dashboard</a></li>
        <li><a href="scan-absensi.php"><span>📱</span> Scan Absensi</a></li>
        <li><a href="riwayat-absensi.php"><span>📝</span> Riwayat Absensi</a></li>
        <li><a href="ganti-password.php"><span>🔑</span> Ganti Password</a></li>
        <li><a href="../api/logout.php"><span>🚪</span> Logout</a></li>
    </ul>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Dashboard Siswa</h1>
                <p><?php echo $siswa_data['nama_kelas'] . ' - ' . $siswa_data['nama_jurusan']; ?></p>
            </div>

            <!-- Status Absensi Hari Ini -->
            <?php if ($cek_absen): ?>
                <div class="alert alert-success">
                    ✅ Anda sudah absen hari ini pada pukul <?php echo date('H:i', strtotime($cek_absen['jam'])); ?> WIB
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    ⚠️ Anda belum absen hari ini. <a href="scan-absensi.php" style="color: #4F46E5; font-weight: bold;">Klik di sini untuk absen</a>
                </div>
            <?php endif; ?>

            <!-- Statistik Absensi Bulan Ini -->
            <div class="card-grid">
                <div class="card">
                    <div class="card-icon success">✅</div>
                    <h3>Hadir</h3>
                    <div class="value"><?php echo $stats['hadir'] ?? 0; ?></div>
                </div>

                <div class="card">
                    <div class="card-icon warning">📋</div>
                    <h3>Izin</h3>
                    <div class="value"><?php echo $stats['izin'] ?? 0; ?></div>
                </div>

                <div class="card">
                    <div class="card-icon primary">🏥</div>
                    <h3>Sakit</h3>
                    <div class="value"><?php echo $stats['sakit'] ?? 0; ?></div>
                </div>

                <div class="card">
                    <div class="card-icon danger">❌</div>
                    <h3>Alpha</h3>
                    <div class="value"><?php echo $stats['alpha'] ?? 0; ?></div>
                </div>
            </div>

            <!-- Riwayat Absensi Terakhir -->
            <div class="table-container" style="margin-top: 30px;">
                <div class="table-header">
                    <h2>Riwayat Absensi (7 Hari Terakhir)</h2>
                    <a href="riwayat-absensi.php" class="btn btn-primary">Lihat Semua</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $riwayat = mysqli_query($conn, "SELECT * FROM absensi WHERE id_siswa = {$siswa_data['id_siswa']} ORDER BY tanggal DESC, jam DESC LIMIT 7");
                        if (mysqli_num_rows($riwayat) > 0):
                            while ($r = mysqli_fetch_assoc($riwayat)):
                        ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                            <td><?php echo date('H:i', strtotime($r['jam'])); ?> WIB</td>
                            <td>
                                <?php
                                $badge_class = 'badge-success';
                                if ($r['status'] == 'izin') $badge_class = 'badge-warning';
                                if ($r['status'] == 'sakit') $badge_class = 'badge-primary';
                                if ($r['status'] == 'alpha') $badge_class = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($r['status']); ?></span>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #6B7280;">Belum ada riwayat absensi</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>