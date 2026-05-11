<?php
include 'config.php';
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$username_target = $_SESSION['username']; 
$sql_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_target'");
$data_user = mysqli_fetch_assoc($sql_user);
$foto_lama = $data_user['foto'];

if (isset($_POST['update'])) {
    $username_baru = mysqli_real_escape_string($conn, $_POST['username']);
    $pw_input = $_POST['password']; 
    $konfirmasi_pw = $_POST['konfirmasi_password']; 

    // --- VALIDASI 1: Cek apakah username sudah dipakai orang lain ---
    $cek_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_baru' AND username != '$username_target'");
    
    if (mysqli_num_rows($cek_username) > 0) {
        echo "<script>alert('Gagal! Username sudah digunakan orang lain.'); window.location='edit_profile.php';</script>";
        exit;
    }

    // --- VALIDASI 2: Cek kesamaan password ---
    if (!empty($pw_input)) {
        if ($pw_input !== $konfirmasi_pw) {
            echo "<script>alert('Gagal! Konfirmasi password tidak cocok.'); window.location='edit_profile.php';</script>";
            exit;
        }
        $password_aman = password_hash($pw_input, PASSWORD_DEFAULT);
    }

    // Logika Foto
    if (!empty($_FILES['foto']['name'])) {
        $foto_final = time() . "_" . $username_baru . "." . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['foto']['tmp_name'], "img/" . $foto_final);
    } else {
        $foto_final = $foto_lama;
    }

    // Eksekusi Query
    if (!empty($pw_input)) {
        $query = "UPDATE users SET username = '$username_baru', password = '$password_aman', foto = '$foto_final' WHERE username = '$username_target'";
    } else {
        $query = "UPDATE users SET username = '$username_baru', foto = '$foto_final' WHERE username = '$username_target'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['username'] = $username_baru;
        echo "<script>alert('Profil Berhasil Diupdate!'); window.location='profile.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - MyFinance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="d-flex">
        
        <div class="flex-shrink-0" style="width: 250px; min-height: 100vh; background-color: #212529;">
            <?php include 'template/sidebar.php'; ?>
        </div>

        <div class="flex-grow-1 bg-white">
            
            <div class="p-4 border-bottom bg-white">
                <h2 class="fw-bold mb-0">Edit Profil</h2>
            </div>

            <div class="p-4">
                <div class="card shadow-sm border-0" style="max-width: 900px;">
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4 text-center border-end py-3">
                                    <h6 class="text-muted mb-3">Foto Profil</h6>
                                    <img src="img/<?= $foto_lama ? $foto_lama : 'default.png' ?>" 
                                         class="rounded-circle border mb-3 shadow-sm" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    <input type="file" name="foto" class="form-control form-control-sm mx-auto" style="max-width: 200px;">
                                </div>

                                <div class="col-md-8 ps-md-5 py-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                            <input type="text" name="username" class="form-control" value="<?= $data_user['username']; ?>" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold small">Password Baru</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tak ganti">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold small">Konfirmasi Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="fas fa-check-circle"></i></span>
                                                <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" name="update" class="btn btn-primary px-5">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                                        </button>
                                        <a href="profile.php" class="btn btn-light ms-2">Batal</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
    </div>
</body>
</html>