<?php
session_start();
include 'config.php';

if (isset($_POST['daftar'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Hash password agar aman (sesuai dengan password_verify di login)
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username sudah ada
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        $error_user = true;
    } else {
        // Masukkan ke database
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$password_hash')";
        if (mysqli_query($conn, $query)) {
            // Jika berhasil, lempar ke login.php dengan pesan sukses
            header("Location: login.php?pesan=berhasil");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun - MyFinance</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
   <div class="login-page">
    <div class="login-box">
        <div style="font-size: 50px; margin-bottom: 10px;">📝</div>
        <h2>Daftar Akun</h2>
        <p>Buat akun baru MyFinance</p>

        <?php if(isset($error_user)): ?>
            <div style="color: red; margin-bottom: 15px; font-weight: bold;">Username sudah ada!</div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" class="custom-input" placeholder="Username Baru" required>
            <input type="password" name="password" class="custom-input" placeholder="Password Baru" required>
            
            <button type="submit" name="daftar" class="btn-ios">Daftar Sekarang</button>
        </form>
        
        <div class="signup-link" style="margin-top: 20px;">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</div>
</body>
</html>