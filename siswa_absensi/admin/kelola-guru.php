<?php
require_once '../config/config.php';
check_role('admin');

// Proses Tambah Guru
if (isset($_POST['tambah'])) {
    $nip = clean_input($_POST['nip']);
    $nama = clean_input($_POST['nama']);
    $email = clean_input($_POST['email']);
    $no_telp = clean_input($_POST['no_telp']);
    $username = clean_input($_POST['username']);
    $password = password_hash('password', PASSWORD_BCRYPT);
    
    // Cek NIP sudah ada atau belum
    $cek = mysqli_query($conn, "SELECT * FROM guru WHERE nip = '$nip'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "NIP sudah terdaftar!";
    } else {
        // Insert ke tabel users
        $insert_user = mysqli_query($conn, "INSERT INTO users (username, password, id_role, status) VALUES ('$username', '$password', 2, 'aktif')");
        $id_user = mysqli_insert_id($conn);
        
        // Insert ke tabel guru
        $insert_guru = mysqli_query($conn, "INSERT INTO guru (id_user, nip, nama_guru, email, no_telp) VALUES ($id_user, '$nip', '$nama', '$email', '$no_telp')");
        
        if ($insert_guru) {
            $success = "Guru berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan guru!";
        }
    }
}

// Proses Edit Guru
if (isset($_POST['edit'])) {
    $id_guru = clean_input($_POST['id_guru']);
    $nip = clean_input($_POST['nip']);
    $nama = clean_input($_POST['nama']);
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $no_telp = clean_input($_POST['no_telp']);

    // Update guru
    $update_guru = mysqli_query($conn, "UPDATE guru SET nip='$nip', nama_guru='$nama', email='$email', no_telp='$no_telp' WHERE id_guru=$id_guru");

    // Update users table if username changed
    $cek_user = mysqli_query($conn, "SELECT id_user FROM guru WHERE id_guru=$id_guru");
    $user = mysqli_fetch_assoc($cek_user);
    $update_user = mysqli_query($conn, "UPDATE users SET username='$username' WHERE id_user=" . $user['id_user']);

    if ($update_guru && $update_user) {
        $success = "Guru berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate guru!";
    }
}

// Proses Hapus Guru
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = mysqli_query($conn, "DELETE FROM guru WHERE id_guru = $id");
    if ($hapus) {
        $success = "Guru berhasil dihapus!";
    }
}

// Ambil data guru
$guru = mysqli_query($conn, "SELECT g.*, u.username FROM guru g JOIN users u ON g.id_user = u.id_user ORDER BY g.nama_guru");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Guru - Admin</title>
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
                <h1>Kelola Guru</h1>
                <p>Manajemen data guru</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Form Tambah Guru -->
            <div class="card" style="margin-bottom: 30px;">
                <h2>Tambah Guru Baru</h2>
                <form method="POST" style="margin-top: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <div class="form-group">
                            <label>NIP</label>
                            <input type="text" name="nip" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" required>
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
                    <button type="submit" name="tambah" class="btn btn-primary" style="margin-top: 10px;">Tambah Guru</button>
                    <small style="display: block; margin-top: 10px; color: #6B7280;">*Password default: password</small>
                </form>
            </div>

            <!-- Tabel Data Guru -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Data Guru (<?php echo mysqli_num_rows($guru); ?>)</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Guru</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($g = mysqli_fetch_assoc($guru)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $g['nip']; ?></td>
                            <td><?php echo $g['nama_guru']; ?></td>
                            <td><?php echo $g['username']; ?></td>
                            <td><?php echo $g['email'] ?: '-'; ?></td>
                            <td><?php echo $g['no_telp'] ?: '-'; ?></td>
                            <td>
                                <button type="button" onclick="openEditModal(<?php echo $g['id_guru']; ?>, '<?php echo $g['nip']; ?>', '<?php echo $g['nama_guru']; ?>', '<?php echo $g['username']; ?>', '<?php echo $g['email']; ?>', '<?php echo $g['no_telp']; ?>')" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">Edit</button>
                                <a href="?hapus=<?php echo $g['id_guru']; ?>"
                                   onclick="return confirm('Yakin hapus guru ini?')"
                                   class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Edit Guru -->
    <div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px;">
            <span class="close" onclick="closeEditModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2>Edit Guru</h2>
            <form id="editForm" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id_guru" id="edit_id_guru">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" id="edit_nip" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_nama" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="edit_username" required>
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
                <button type="submit" name="edit" class="btn btn-primary" style="margin-top: 10px;">Update Guru</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id_guru, nip, nama, username, email, no_telp) {
            document.getElementById('edit_id_guru').value = id_guru;
            document.getElementById('edit_nip').value = nip;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_username').value = username;
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
