<?php
require_once '../config/config.php';
check_role('admin');

// Proses Tambah Jurusan
if (isset($_POST['tambah'])) {
    $kode = clean_input($_POST['kode_jurusan']);
    $nama = clean_input($_POST['nama_jurusan']);

    $insert = mysqli_query($conn, "INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES ('$kode', '$nama')");

    if ($insert) {
        $success = "Jurusan berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan jurusan!";
    }
}

// Proses Edit Jurusan
if (isset($_POST['edit'])) {
    $id_jurusan = clean_input($_POST['id_jurusan']);
    $kode = clean_input($_POST['kode_jurusan']);
    $nama = clean_input($_POST['nama_jurusan']);

    $update = mysqli_query($conn, "UPDATE jurusan SET kode_jurusan='$kode', nama_jurusan='$nama' WHERE id_jurusan=$id_jurusan");

    if ($update) {
        $success = "Jurusan berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate jurusan!";
    }
}

// Proses Hapus Jurusan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = mysqli_query($conn, "DELETE FROM jurusan WHERE id_jurusan = $id");
    if ($hapus) {
        $success = "Jurusan berhasil dihapus!";
    } else {
        $error = "Gagal menghapus jurusan! (Mungkin masih ada kelas yang terkait)";
    }
}

// Ambil data jurusan
$jurusan = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jurusan - Admin</title>
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
                <h1>Kelola Jurusan</h1>
                <p>Manajemen data jurusan</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Form Tambah Jurusan -->
            <div class="card" style="margin-bottom: 30px;">
                <h2>Tambah Jurusan Baru</h2>
                <form method="POST" style="margin-top: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Kode Jurusan</label>
                            <input type="text" name="kode_jurusan" placeholder="Contoh: RPL" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Jurusan</label>
                            <input type="text" name="nama_jurusan" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                        </div>
                    </div>
                    <button type="submit" name="tambah" class="btn btn-primary" style="margin-top: 10px;">Tambah Jurusan</button>
                </form>
            </div>

            <!-- Tabel Data Jurusan -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Data Jurusan (<?php echo mysqli_num_rows($jurusan); ?>)</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Jurusan</th>
                            <th>Nama Jurusan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($j = mysqli_fetch_assoc($jurusan)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $j['kode_jurusan']; ?></strong></td>
                            <td><?php echo $j['nama_jurusan']; ?></td>
                            <td>
                                <button type="button" onclick="openEditModal(<?php echo $j['id_jurusan']; ?>, '<?php echo $j['kode_jurusan']; ?>', '<?php echo $j['nama_jurusan']; ?>')" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">Edit</button>
                                <a href="?hapus=<?php echo $j['id_jurusan']; ?>"
                                   onclick="return confirm('Yakin hapus jurusan ini?')"
                                   class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Edit Jurusan -->
    <div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px;">
            <span class="close" onclick="closeEditModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2>Edit Jurusan</h2>
            <form id="editForm" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id_jurusan" id="edit_id_jurusan">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div class="form-group">
                        <label>Kode Jurusan</label>
                        <input type="text" name="kode_jurusan" id="edit_kode_jurusan" placeholder="Contoh: RPL" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" id="edit_nama_jurusan" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                    </div>
                </div>
                <button type="submit" name="edit" class="btn btn-primary" style="margin-top: 10px;">Update Jurusan</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id_jurusan, kode_jurusan, nama_jurusan) {
            document.getElementById('edit_id_jurusan').value = id_jurusan;
            document.getElementById('edit_kode_jurusan').value = kode_jurusan;
            document.getElementById('edit_nama_jurusan').value = nama_jurusan;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
