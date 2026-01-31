<?php
require_once '../config/config.php';
check_role('guru');

// Ambil data guru
$guru_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT g.* FROM guru g JOIN users u ON g.id_user = u.id_user WHERE u.id_user = {$_SESSION['user_id']}"));

// Ambil kelas
$kelas = mysqli_query($conn, "SELECT k.*, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.id_jurusan = j.id_jurusan ORDER BY k.nama_kelas");

// Filter
$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Ambil data absensi
$where = "WHERE a.tanggal = '$filter_tanggal'";
if ($filter_kelas) {
    $where .= " AND s.id_kelas = $filter_kelas";
}

$absensi = mysqli_query($conn, "SELECT s.nis, s.nama_siswa, k.nama_kelas, a.jam, a.status 
    FROM absensi a 
    JOIN siswa s ON a.id_siswa = s.id_siswa 
    JOIN kelas k ON s.id_kelas = k.id_kelas 
    $where 
    ORDER BY k.nama_kelas, s.nama_siswa");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi - Guru</title>
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
                <h1>Rekap Absensi</h1>
                <p>Lihat rekap kehadiran siswa</p>
            </div>

            <!-- Filter -->
            <div class="card" style="margin-bottom: 30px;">
                <h2>Filter Rekap</h2>
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

            <!-- Tabel Rekap -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Rekap Tanggal <?php echo date('d/m/Y', strtotime($filter_tanggal)); ?> (<?php echo mysqli_num_rows($absensi); ?> Siswa)</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
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
                            <td colspan="6" style="text-align: center; color: #6B7280; padding: 40px;">
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