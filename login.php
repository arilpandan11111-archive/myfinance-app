<?php
ob_start(); 
session_start();
// ... sisanya kode login kamu yang kemarin ...
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';


if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    
    if ($query && mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        
       )
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $row['id']; // Menggunakan 'id' sesuai Screenshot 47
            $_SESSION['username'] = $row['username'];
            
            header("Location: index.php");
            exit();
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - MyFinance</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=<?= time(); ?>">
</head>
<body>
    <div class="login-page">
        <div class="login-box">
            <div style="font-size: 50px; margin-bottom: 10px;">💰</div>
            <h2>Selamat Datang</h2>
            <p>Silakan login ke akun Anda</p>

            <?php if(isset($error)): ?>
                <div style="color: red; margin-bottom: 15px;">Username atau Password salah!</div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="username" class="custom-input" placeholder="Username" required>
                <input type="password" name="password" class="custom-input" placeholder="Password" required>
                <button type="submit" name="login" class="btn-ios">Masuk</button>
            </form>
            
            <div class="signup-link">
                Belum punya akun? <a href="daftar.php">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</body>
</html>