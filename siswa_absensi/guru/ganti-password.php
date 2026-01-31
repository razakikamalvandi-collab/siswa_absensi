<?php
require_once '../config/config.php';
check_role('guru');

$guru_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT g.* FROM guru g JOIN users u ON g.id_user = u.id_user WHERE u.id_user = {$_SESSION['user_id']}"));

$success = '';
$error = '';

if (isset($_POST['ganti'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    // Cek password lama
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id_user = {$_SESSION['user_id']}"));
    
    if (!password_verify($password_lama, $user['password'])) {
        $error = "Password lama salah!";
    } elseif ($password_baru != $konfirmasi_password) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password_baru) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        $password_hash = password_hash($password_baru, PASSWORD_BCRYPT);
        $update = mysqli_query($conn, "UPDATE users SET password = '$password_hash' WHERE id_user = {$_SESSION['user_id']}");
        
        if ($update) {
            $success = "Password berhasil diubah!";
        } else {
            $error = "Gagal mengubah password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - Guru</title>
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
                <li><a href="index.php"><span>📊</span> Dashboard</a></li>
                <li><a href="generate-qr.php"><span>📱</span> Generate QR Code</a></li>
                <li><a href="rekap-absensi.php"><span>📝</span> Rekap Absensi</a></li>
                <li><a href="ganti-password.php" class="active"><span>🔑</span> Ganti Password</a></li>
                <li><a href="../api/logout.php"><span>🚪</span> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Ganti Password</h1>
                <p>Ubah password akun Anda</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <h2>Form Ganti Password</h2>
                <form method="POST" style="margin-top: 20px;">
                    <div class="form-group">
                        <label>Password Lama</label>
                        <input type="password" name="password_lama" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" minlength="6" required>
                        <small style="color: #6B7280; font-size: 12px;">Minimal 6 karakter</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi_password" minlength="6" required>
                    </div>
                    
                    <button type="submit" name="ganti" class="btn btn-primary">Ganti Password</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>