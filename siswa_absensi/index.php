<?php
require_once 'config/config.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/index.php");
            break;
        case 'guru':
            header("Location: guru/index.php");
            break;
        case 'siswa':
            header("Location: siswa/index.php");
            break;
    }
    exit();
}

$error = '';

// Proses Login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    // Query dengan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT u.id_user, u.username, u.password, r.nama_role 
                            FROM users u 
                            JOIN roles r ON u.id_role = r.id_role 
                            WHERE u.username = ? AND u.status = 'aktif'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['nama_role'];
            
            // Redirect sesuai role
            if ($user['nama_role'] == 'admin') {
                header("Location: admin/index.php");
            } elseif ($user['nama_role'] == 'guru') {
                header("Location: guru/index.php");
            } elseif ($user['nama_role'] == 'siswa') {
                header("Location: siswa/index.php");
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan atau akun tidak aktif!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi Siswa</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>🎓 Sistem Absensi Siswa</h1>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username atau NIS" required autofocus>
                    <small style="color: #6B7280; font-size: 12px; display: block; margin-top: 5px;">

                    </small>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                    <small style="color: #6B7280; font-size: 12px; display: block; margin-top: 5px;">
                    </small>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <div class="login-footer">
                <p style="font-size: 13px; color: #6B7280; margin-top: 20px;">
                    © 2026 Sistem Absensi Siswa
                </p>
            </div>
        </div>
    </div>
</body>
</html>