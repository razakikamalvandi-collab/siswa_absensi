<?php
require_once '../config/config.php';
check_role('admin');

// Ambil data kelas untuk dropdown
$kelas_query = mysqli_query($conn, "SELECT k.*, j.nama_jurusan FROM kelas k JOIN jurusan j ON k.id_jurusan = j.id_jurusan ORDER BY k.tingkat, k.nama_kelas");

// Proses Tambah Siswa
if (isset($_POST['tambah'])) {
    $nis = clean_input($_POST['nis']);
    $nama = clean_input($_POST['nama']);
    $id_kelas = clean_input($_POST['id_kelas']);
    $email = clean_input($_POST['email']);
    $no_telp = clean_input($_POST['no_telp']);
    $password = password_hash('password', PASSWORD_BCRYPT);
    
    // Cek NIS sudah ada atau belum
    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "NIS sudah terdaftar!";
    } else {
        // Insert ke tabel users
        $insert_user = mysqli_query($conn, "INSERT INTO users (username, password, id_role, status) VALUES ('$nis', '$password', 3, 'aktif')");
        $id_user = mysqli_insert_id($conn);
        
        // Insert ke tabel siswa
        $insert_siswa = mysqli_query($conn, "INSERT INTO siswa (id_user, nis, nama_siswa, id_kelas, email, no_telp) VALUES ($id_user, '$nis', '$nama', $id_kelas, '$email', '$no_telp')");
        
        if ($insert_siswa) {
            $success = "Siswa berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan siswa!";
        }
    }
}

// Proses Edit Siswa
if (isset($_POST['edit'])) {
    $id_siswa = clean_input($_POST['id_siswa']);
    $nis = clean_input($_POST['nis']);
    $nama = clean_input($_POST['nama']);
    $id_kelas = clean_input($_POST['id_kelas']);
    $email = clean_input($_POST['email']);
    $no_telp = clean_input($_POST['no_telp']);
    
    // Update siswa
    $update = mysqli_query($conn, "UPDATE siswa SET nis='$nis', nama_siswa='$nama', id_kelas=$id_kelas, email='$email', no_telp='$no_telp' WHERE id_siswa=$id_siswa");
    
    if ($update) {
        $success = "Siswa berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate siswa!";
    }
}

// Proses Hapus Siswa
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = mysqli_query($conn, "DELETE FROM siswa WHERE id_siswa = $id");
    if ($hapus) {
        $success = "Siswa berhasil dihapus!";
    }
}

// Ambil data siswa
$siswa = mysqli_query($conn, "SELECT s.*, k.nama_kelas, j.nama_jurusan FROM siswa s JOIN kelas k ON s.id_kelas = k.id_kelas JOIN jurusan j ON k.id_jurusan = j.id_jurusan ORDER BY s.nama_siswa");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Siswa - Admin</title>
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
                <h1>Kelola Siswa</h1>
                <p>Manajemen data siswa</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Form Tambah Siswa -->
            <div class="card" style="margin-bottom: 30px;">
                <h2>Tambah Siswa Baru</h2>
                <form method="POST" style="margin-top: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>NIS</label>
                            <input type="text" name="nis" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="id_kelas" required>
                                <option value="">Pilih Kelas</option>
                                <?php while ($k = mysqli_fetch_assoc($kelas_query)): ?>
                                    <option value="<?php echo $k['id_kelas']; ?>">
                                        <?php echo $k['nama_kelas'] . ' - ' . $k['nama_jurusan']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="text" name="no_telp">
                        </div>
                    </div>
                    <button type="submit" name="tambah" class="btn btn-primary" style="margin-top: 10px;">Tambah Siswa</button>
                    <small style="display: block; margin-top: 10px; color: #6B7280;">*Password default: password</small>
                </form>
            </div>

            <!-- Tabel Data Siswa -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Data Siswa (<?php echo mysqli_num_rows($siswa); ?>)</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        mysqli_data_seek($siswa, 0); // Reset pointer
                        while ($s = mysqli_fetch_assoc($siswa)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $s['nis']; ?></td>
                            <td><?php echo $s['nama_siswa']; ?></td>
                            <td><?php echo $s['nama_kelas']; ?></td>
                            <td><?php echo $s['nama_jurusan']; ?></td>
                            <td><?php echo $s['email'] ?: '-'; ?></td>
                            <td><?php echo $s['no_telp'] ?: '-'; ?></td>
                            <td>
                                <button type="button" onclick="openEditModal(<?php echo $s['id_siswa']; ?>, '<?php echo $s['nis']; ?>', '<?php echo $s['nama_siswa']; ?>', <?php echo $s['id_kelas']; ?>, '<?php echo $s['email']; ?>', '<?php echo $s['no_telp']; ?>')" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">Edit</button>
                                <a href="?hapus=<?php echo $s['id_siswa']; ?>"
                                   onclick="return confirm('Yakin hapus siswa ini?')"
                                   class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Edit Siswa -->
    <div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px;">
            <span class="close" onclick="closeEditModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2>Edit Siswa</h2>
            <form id="editForm" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id_siswa" id="edit_id_siswa">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div class="form-group">
                        <label>NIS</label>
                        <input type="text" name="nis" id="edit_nis" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_nama" required>
                    </div>
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="id_kelas" id="edit_id_kelas" required>
                            <option value="">Pilih Kelas</option>
                            <?php
                            mysqli_data_seek($kelas_query, 0); // Reset pointer
                            while ($k = mysqli_fetch_assoc($kelas_query)): ?>
                                <option value="<?php echo $k['id_kelas']; ?>">
                                    <?php echo $k['nama_kelas'] . ' - ' . $k['nama_jurusan']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email">
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp" id="edit_no_telp">
                    </div>
                </div>
                <button type="submit" name="edit" class="btn btn-primary" style="margin-top: 10px;">Update Siswa</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id_siswa, nis, nama, id_kelas, email, no_telp) {
            document.getElementById('edit_id_siswa').value = id_siswa;
            document.getElementById('edit_nis').value = nis;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_id_kelas').value = id_kelas;
            document.getElementById('edit_email').value = email || '';
            document.getElementById('edit_no_telp').value = no_telp || '';
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
