<?php
// Test Koneksi Database
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'absensi_siswa';

$conn = mysqli_connect($host, $user, $pass, $db);

if ($conn) {
    echo "✅ Koneksi database BERHASIL!<br>";
    
    // Cek jumlah tabel
    $result = mysqli_query($conn, "SHOW TABLES");
    $num_tables = mysqli_num_rows($result);
    echo "✅ Jumlah tabel: $num_tables<br>";
    
    // Cek jumlah users
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    $row = mysqli_fetch_assoc($result);
    echo "✅ Jumlah users: " . $row['total'] . "<br>";
    
    // Cek jumlah siswa
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
    $row = mysqli_fetch_assoc($result);
    echo "✅ Jumlah siswa: " . $row['total'] . "<br>";
    
    echo "<br><strong style='color: green;'>DATABASE SUDAH SIAP DIGUNAKAN!</strong>";
} else {
    echo "❌ Koneksi GAGAL: " . mysqli_connect_error();
}
?>
```

**Cara test:**

1. Akses: `http://localhost/absensi_siswa/test_koneksi.php`
2. Harusnya muncul:
```
   ✅ Koneksi database BERHASIL!
   ✅ Jumlah tabel: 8
   ✅ Jumlah users: 35
   ✅ Jumlah siswa: 33
   
   DATABASE SUDAH SIAP DIGUNAKAN!
```

---

### **LANGKAH 5: Login ke Sistem**

1. Akses: `http://localhost/absensi_siswa/`
2. Login dengan:
   - **Username**: `admin`
   - **Password**: `password`

---

## **🔍 JIKA MASIH ERROR**

Coba cek hal-hal ini:

### 1. **Pastikan MySQL Running**
```
- Buka XAMPP Control Panel
- Pastikan MySQL ada tulisan "Running" (hijau)
- Jika belum, klik "Start"
```

### 2. **Cek Port MySQL**
```
- Klik "Config" di sebelah MySQL
- Pilih "my.ini"
- Cari baris: port=3306
- Pastikan portnya 3306 (default)
```

### 3. **Restart XAMPP**
```
- Stop semua service
- Tutup XAMPP
- Buka lagi XAMPP
- Start Apache dan MySQL