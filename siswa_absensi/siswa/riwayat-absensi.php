<?php
require_once '../config/config.php';
check_role('siswa');

// Ambil data siswa
$siswa_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, k.nama_kelas FROM siswa s JOIN kelas k ON s.id_kelas = k.id_kelas JOIN users u ON s.id_user = u.id_user WHERE u.id_user = {$_SESSION['user_id']}"));

// Filter bulan (default bulan ini)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Ambil riwayat absensi
$riwayat = mysqli_query($conn, "SELECT * FROM absensi WHERE id_siswa = {$siswa_data['id_siswa']} AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan' ORDER BY tanggal DESC, jam DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi - Siswa</title>
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
                <h1>Riwayat Absensi</h1>
                <p>Lihat riwayat kehadiran Anda</p>
            </div>

            <!-- Filter Bulan -->
            <div class="card" style="margin-bottom: 30px;">
                <form method="GET">
                    <div class="form-group">
                        <label>Filter Bulan</label>
                        <input type="month" name="bulan" value="<?php echo $bulan; ?>" onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            <!-- Tabel Riwayat -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Riwayat Bulan <?php echo date('F Y', strtotime($bulan . '-01')); ?></h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($riwayat) > 0):
                            $no = 1;
                            while ($r = mysqli_fetch_assoc($riwayat)):
                                $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                            <td><?php echo $hari[date('w', strtotime($r['tanggal']))]; ?></td>
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
                            <td><?php echo $r['keterangan'] ?: '-'; ?></td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #6B7280; padding: 40px;">
                                Tidak ada riwayat absensi untuk bulan ini
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>