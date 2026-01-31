<?php
require_once '../config/config.php';
check_role('admin');

// Filter
$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Ambil kelas untuk dropdown
$kelas = mysqli_query($conn, "SELECT k.*, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.id_jurusan = j.id_jurusan ORDER BY k.nama_kelas");

// Ambil data absensi
$where = "WHERE a.tanggal = '$filter_tanggal'";
if ($filter_kelas) {
    $where .= " AND s.id_kelas = $filter_kelas";
}

$absensi = mysqli_query($conn, "SELECT s.nis, s.nama_siswa, k.nama_kelas, j.nama_jurusan, a.jam, a.status 
    FROM absensi a 
    JOIN siswa s ON a.id_siswa = s.id_siswa 
    JOIN kelas k ON s.id_kelas = k.id_kelas 
    JOIN jurusan j ON k.id_jurusan = j.id_jurusan
    $where 
    ORDER BY k.nama_kelas, s.nama_siswa");

// Hitung statistik
$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
    SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
    SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
    SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha
    FROM absensi a JOIN siswa s ON a.id_siswa = s.id_siswa $where"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Absensi - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🎓 Admin Panel</h2>
                <p><?php echo $_SESSION['username']; ?></p>
            </div>
            <ul class="sidebar-menu">
    <ul class="sidebar-menu">
        <li><a href="index.php"><span>📊</span> Dashboard</a></li>
        <li><a href="kelola-guru.php"><span>👨‍🏫</span> Kelola Guru</a></li>
        <li><a href="kelola-siswa.php"><span>👨‍🎓</span> Kelola Siswa</a></li>
        <li><a href="kelola-kelas.php"><span>🏫</span> Kelola Kelas</a></li>
        <li><a href="kelola-jurusan.php"><span>📚</span> Kelola Jurusan</a></li>
        <li><a href="lihat-absensi.php"><span>📝</span> Lihat Absensi</a></li>
        <li><a href="reset-password.php"><span>🔑</span> Reset Password</a></li>
        <li><a href="../api/logout.php"><span>🚪</span> Logout</a></li>
    </ul>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Lihat Absensi</h1>
                <p>Monitoring kehadiran siswa</p>
            </div>

            <!-- Statistik -->
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

            <!-- Filter -->
            <div class="card" style="margin-top: 30px; margin-bottom: 30px;">
                <h2>Filter Data</h2>
                <form method="GET" style="margin-top: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas" onchange="this.form.submit()">
                                <option value="">Semua Kelas</option>
                                <?php 
                                mysqli_data_seek($kelas, 0);
                                while ($k = mysqli_fetch_assoc($kelas)): 
                                ?>
                                    <option value="<?php echo $k['id_kelas']; ?>" <?php echo ($filter_kelas == $k['id_kelas']) ? 'selected' : ''; ?>>
                                        <?php echo $k['nama_kelas'] . ' - ' . $k['nama_jurusan']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" value="<?php echo $filter_tanggal; ?>" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Absensi -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Data Absensi - <?php echo date('d/m/Y', strtotime($filter_tanggal)); ?> (<?php echo mysqli_num_rows($absensi); ?> Siswa)</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Jam Absen</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($absensi) > 0):
                            $no = 1;
                            while ($a = mysqli_fetch_assoc($absensi)):
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $a['nis']; ?></td>
                            <td><?php echo $a['nama_siswa']; ?></td>
                            <td><?php echo $a['nama_kelas']; ?></td>
                            <td><?php echo $a['nama_jurusan']; ?></td>
                            <td><?php echo date('H:i', strtotime($a['jam'])); ?> WIB</td>
                            <td>
                                <?php
                                $badge_class = 'badge-success';
                                if ($a['status'] == 'izin') $badge_class = 'badge-warning';
                                if ($a['status'] == 'sakit') $badge_class = 'badge-primary';
                                if ($a['status'] == 'alpha') $badge_class = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($a['status']); ?></span>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #6B7280; padding: 40px;">
                                Tidak ada data absensi untuk filter yang dipilih
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
