<?php
require_once '../config/config.php';
check_role('admin');

// Hitung statistik
$total_guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM guru"))['total'];
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa"))['total'];
$total_kelas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kelas"))['total'];
$absensi_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal = CURDATE()"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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
                <li><a href="index.php" class="active"><span>📊</span> Dashboard</a></li>
                <li><a href="kelola-guru.php"><span>👨‍🏫</span> Kelola Guru</a></li>
                <li><a href="kelola-siswa.php"><span>👨‍🎓</span> Kelola Siswa</a></li>
                <li><a href="kelola-kelas.php"><span>🏫</span> Kelola Kelas</a></li>
                <li><a href="kelola-jurusan.php"><span>📚</span> Kelola Jurusan</a></li>
                <li><a href="lihat-absensi.php"><span>📝</span> Lihat Absensi</a></li>
                <li><a href="../api/logout.php"><span>🚪</span> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Dashboard Admin</h1>
                <p>Selamat datang di Sistem Absensi Siswa</p>
            </div>

            <div class="card-grid">
                <div class="card">
                    <div class="card-icon primary">👨‍🏫</div>
                    <h3>Total Guru</h3>
                    <div class="value"><?php echo $total_guru; ?></div>
                </div>

                <div class="card">
                    <div class="card-icon success">👨‍🎓</div>
                    <h3>Total Siswa</h3>
                    <div class="value"><?php echo $total_siswa; ?></div>
                </div>

                <div class="card">
                    <div class="card-icon warning">🏫</div>
                    <h3>Total Kelas</h3>
                    <div class="value"><?php echo $total_kelas; ?></div>
                </div>

                <div class="card">
                    <div class="card-icon danger">📝</div>
                    <h3>Absensi Hari Ini</h3>
                    <div class="value"><?php echo $absensi_hari_ini; ?></div>
                </div>
            </div>

            <!-- Tabel Absensi Hari Ini -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Absensi Hari Ini</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT s.nama_siswa, k.nama_kelas, a.jam, a.status 
                                  FROM absensi a 
                                  JOIN siswa s ON a.id_siswa = s.id_siswa 
                                  JOIN kelas k ON s.id_kelas = k.id_kelas 
                                  WHERE a.tanggal = CURDATE() 
                                  ORDER BY a.jam DESC LIMIT 10";
                        $result = mysqli_query($conn, $query);
                        $no = 1;
                        
                        while ($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['nama_siswa']; ?></td>
                            <td><?php echo $row['nama_kelas']; ?></td>
                            <td><?php echo $row['jam']; ?></td>
                            <td>
                                <span class="badge badge-success"><?php echo ucfirst($row['status']); ?></span>
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