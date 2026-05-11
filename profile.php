<?php
include 'config.php';
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$username_lama = $_SESSION['username'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_lama'");
$data_user = mysqli_fetch_assoc($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Profil - MyFinance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container-fluid p-0">
    <div class="d-flex">
        <div class="bg-dark" style="min-width: 250px; min-height: 100vh;">
            <?php include 'template/sidebar.php'; ?>
        </div>

        <div class="flex-grow-1 p-5 bg-light">
            <h2 class="text-center mb-4">Profil Saya</h2>
            <div class="card shadow-sm border-0 mx-auto" style="max-width: 450px;">
                <div class="card-body text-center p-4">
                    <img src="img/<?= $data_user['foto'] ? $data_user['foto'] : 'default.png' ?>" 
                         class="rounded-circle mb-3 border" style="width: 150px; height: 150px; object-fit: cover;">
                    <h3 class="fw-bold"><?= $data_user['username']; ?></h3>
                    <p class="text-muted small">
                        <i class="fas fa-clock me-1"></i> 
                        Terakhir diperbarui: <?= date('d M Y, H:i', strtotime($data_user['updated_at'])); ?> WIB
                    </p>
                    <hr>
                    <a href="edit_profile.php" class="btn btn-primary w-100 shadow-sm">
                        <i class="fas fa-user-edit me-2"></i>Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>