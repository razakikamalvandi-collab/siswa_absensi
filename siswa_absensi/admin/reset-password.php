<?php
require_once '../config/config.php';
check_role('admin');

$success = '';
$error = '';

// Proses Reset Password
if (isset($_POST['reset'])) {
    $id_user = clean_input($_POST['id_user']);
    $password_baru = password_hash('password', PASSWORD_BCRYPT);
    
    $update = mysqli_query($conn, "UPDATE users SET password = '$password_baru' WHERE id_user = $id_user");
    
    if ($update) {
        $success = "Password berhasil direset menjadi 'password'";
    } else {
        $error = "Gagal reset password!";
    }
}

// Ambil semua user
$users = mysqli_query($conn, "SELECT u.id_user, u.username, r.nama_role, 
    COALESCE(g.nama_guru, s.nama_siswa, 'Administrator') as nama_lengkap
    FROM users u
    JOIN roles r ON u.id_role = r.id_role
    LEFT JOIN guru g ON g.id_user = u.id_user
    LEFT JOIN siswa s ON s.id_user = u.id_user
    ORDER BY r.nama_role, u.username");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Admin</title>
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
                <h1>Reset Password User</h1>
                <p>Reset password user ke default</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h2>Daftar User</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($u = mysqli_fetch_assoc($users)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $u['username']; ?></td>
                            <td><?php echo $u['nama_lengkap']; ?></td>
                            <td><span class="badge badge-primary"><?php echo ucfirst($u['nama_role']); ?></span></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_user" value="<?php echo $u['id_user']; ?>">
                                    <button type="submit" name="reset" 
                                            onclick="return confirm('Reset password untuk <?php echo $u['username']; ?>?')" 
                                            class="btn btn-warning" style="padding: 6px 12px; font-size: 12px;">
                                        Reset Password
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="card" style="margin-top: 30px; background: #FEF3C7; border-left: 4px solid #F59E0B;">
                <p style="margin: 0; color: #92400E;">
                    ⚠️ <strong>Perhatian:</strong> Password akan direset menjadi <strong>"password"</strong> (tanpa tanda kutip)
                </p>
            </div>
        </main>
    </div>
</body>
</html>