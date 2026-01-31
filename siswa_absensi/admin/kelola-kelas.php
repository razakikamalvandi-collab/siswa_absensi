<?php
require_once '../config/config.php';
check_role('admin');

// Ambil data jurusan untuk dropdown
$jurusan_query = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan");

// Proses Tambah Kelas
if (isset($_POST['tambah'])) {
    $nama_kelas = clean_input($_POST['nama_kelas']);
    $id_jurusan = clean_input($_POST['id_jurusan']);
    $tingkat = clean_input($_POST['tingkat']);
    
    $insert = mysqli_query($conn, "INSERT INTO kelas (nama_kelas, id_jurusan, tingkat) VALUES ('$nama_kelas', $id_jurusan, $tingkat)");
    
    if ($insert) {
        $success = "Kelas berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan kelas!";
    }
}

// Proses Edit Kelas
if (isset($_POST['edit'])) {
    $id_kelas = clean_input($_POST['id_kelas']);
    $nama_kelas = clean_input($_POST['nama_kelas']);
    $id_jurusan = clean_input($_POST['id_jurusan']);
    $tingkat = clean_input($_POST['tingkat']);

    $update = mysqli_query($conn, "UPDATE kelas SET nama_kelas='$nama_kelas', id_jurusan=$id_jurusan, tingkat=$tingkat WHERE id_kelas=$id_kelas");

    if ($update) {
        $success = "Kelas berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate kelas!";
    }
}

// Proses Hapus Kelas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = mysqli_query($conn, "DELETE FROM kelas WHERE id_kelas = $id");
    if ($hapus) {
        $success = "Kelas berhasil dihapus!";
    }
}

// Ambil data kelas
$kelas = mysqli_query($conn, "SELECT k.*, j.nama_jurusan, j.kode_jurusan FROM kelas k JOIN jurusan j ON k.id_jurusan = j.id_jurusan ORDER BY k.tingkat, k.nama_kelas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas - Admin</title>
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
                <h1>Kelola Kelas</h1>
                <p>Manajemen data kelas</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Form Tambah Kelas -->
            <div class="card" style="margin-bottom: 30px;">
                <h2>Tambah Kelas Baru</h2>
                <form method="POST" style="margin-top: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>Nama Kelas</label>
                            <input type="text" name="nama_kelas" placeholder="Contoh: XII RPL 1" required>
                        </div>
                        <div class="form-group">
                            <label>Jurusan</label>
                            <select name="id_jurusan" required>
                                <option value="">Pilih Jurusan</option>
                                <?php 
                                mysqli_data_seek($jurusan_query, 0);
                                while ($j = mysqli_fetch_assoc($jurusan_query)): 
                                ?>
                                    <option value="<?php echo $j['id_jurusan']; ?>">
                                        <?php echo $j['nama_jurusan']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tingkat</label>
                            <select name="tingkat" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="10">X (Sepuluh)</option>
                                <option value="11">XI (Sebelas)</option>
                                <option value="12">XII (Dua Belas)</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="tambah" class="btn btn-primary" style="margin-top: 10px;">Tambah Kelas</button>
                </form>
            </div>

            <!-- Tabel Data Kelas -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Data Kelas (<?php echo mysqli_num_rows($kelas); ?>)</h2>
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
                        while ($k = mysqli_fetch_assoc($kelas)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $k['nama_kelas']; ?></td>
                            <td><?php echo $k['nama_jurusan'] . ' (' . $k['kode_jurusan'] . ')'; ?></td>
                            <td>Kelas <?php echo $k['tingkat']; ?></td>
                            <td>
                                <button type="button" onclick="openEditModal(<?php echo $k['id_kelas']; ?>, '<?php echo $k['nama_kelas']; ?>', <?php echo $k['id_jurusan']; ?>, <?php echo $k['tingkat']; ?>)" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">Edit</button>
                                <a href="?hapus=<?php echo $k['id_kelas']; ?>"
                                   onclick="return confirm('Yakin hapus kelas ini?')"
                                   class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Edit Kelas -->
    <div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px;">
            <span class="close" onclick="closeEditModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2>Edit Kelas</h2>
            <form id="editForm" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id_kelas" id="edit_id_kelas">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" placeholder="Contoh: XII RPL 1" required>
                    </div>
                    <div class="form-group">
                        <label>Jurusan</label>
                        <select name="id_jurusan" id="edit_id_jurusan" required>
                            <option value="">Pilih Jurusan</option>
                            <?php
                            mysqli_data_seek($jurusan_query, 0);
                            while ($j = mysqli_fetch_assoc($jurusan_query)):
                            ?>
                                <option value="<?php echo $j['id_jurusan']; ?>">
                                    <?php echo $j['nama_jurusan']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tingkat</label>
                        <select name="tingkat" id="edit_tingkat" required>
                            <option value="">Pilih Tingkat</option>
                            <option value="10">X (Sepuluh)</option>
                            <option value="11">XI (Sebelas)</option>
                            <option value="12">XII (Dua Belas)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="edit" class="btn btn-primary" style="margin-top: 10px;">Update Kelas</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id_kelas, nama_kelas, id_jurusan, tingkat) {
            document.getElementById('edit_id_kelas').value = id_kelas;
            document.getElementById('edit_nama_kelas').value = nama_kelas;
            document.getElementById('edit_id_jurusan').value = id_jurusan;
            document.getElementById('edit_tingkat').value = tingkat;
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
