<?php
require_once '../config/config.php';
check_role('guru');

// Ambil data guru
$guru_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT g.* FROM guru g JOIN users u ON g.id_user = u.id_user WHERE u.id_user = {$_SESSION['user_id']}"));

// Ambil kelas yang bisa diakses (untuk demo, kita ambil semua kelas)
$kelas = mysqli_query($conn, "SELECT k.*, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.id_jurusan = j.id_jurusan ORDER BY k.nama_kelas");

// Hitung statistik
$total_kelas = mysqli_num_rows($kelas);
$absensi_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal = CURDATE()"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                <h1>Dashboard Guru</h1>
                <p>Selamat datang, <?php echo $guru_data['nama_guru']; ?>!</p>
            </div>

            <div class="card-grid">
                <div class="card">
                    <div class="card-icon primary">🏫</div>
                    <h3>Total Kelas</h3>
                    <div class="value"><?php echo $total_kelas; ?></div>
                </div>

                <div class="card">
                    <div class="card-icon success">✅</div>
                    <h3>Absensi Hari Ini</h3>
                    <div class="value"><?php echo $absensi_hari_ini; ?></div>
                </div>
            </div>

            <!-- Daftar Kelas -->
            <div class="table-container" style="margin-top: 30px;">
                <div class="table-header">
                    <h2>Daftar Kelas</h2>
                    <a href="generate-qr.php" class="btn btn-primary">Generate QR Code</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Jurusan</th>
                            <th>Tingkat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        mysqli_data_seek($kelas, 0);
                        while ($k = mysqli_fetch_assoc($kelas)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $k['nama_kelas']; ?></td>
                            <td><?php echo $k['nama_jurusan']; ?></td>
                            <td>Kelas <?php echo $k['tingkat']; ?></td>
                            <td>
                                <a href="generate-qr.php?kelas=<?php echo $k['id_kelas']; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">Generate QR</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>