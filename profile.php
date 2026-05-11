<?php
include 'config.php';
session_start();


if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$current_page = 'profile.php';
$username = $_SESSION['username'];


$query = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");


if (!$query) {
    
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
}

$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - MyFinance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'template/sidebar.php'; ?>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Profil Saya</h1>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-user-circle fa-7x text-secondary"></i>
                                </div>
                                <h3 class="card-title"><?= $username; ?></h3>
                                <p class="text-muted">Administrator MyFinance</p>
                                <hr>
                                <div class="text-start mb-3">
                                    <label class="form-label fw-bold">Status Akun:</label>
                                    <span class="badge bg-success">Aktif</span>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" disabled>Edit Profil (Coming Soon)</button>
            
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>