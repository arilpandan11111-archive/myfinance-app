<?php
$current_page = basename($_SERVER['PHP_SELF']);
$username_side = $_SESSION['username'];

// Ambil data foto dari tabel users
$query_user = mysqli_query($conn, "SELECT foto FROM users WHERE username = '$username_side'");
$data_side = mysqli_fetch_assoc($query_user);
$foto_profil = ($data_side['foto']) ? $data_side['foto'] : 'default.png';
?>

<div class="bg-dark text-white p-3 h-100 shadow" style="width: 250px;">
    <h4 class="fw-bold mb-4"><i class="fas fa-wallet me-2"></i>MyFinance</h4>
    
    <div class="text-center mb-4">
        <img src="img/<?= $foto_profil; ?>" class="rounded-circle img-thumbnail" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #fff;">
        <p class="text-white small mt-2 mb-0"><?= $username_side; ?></p>
    </div>
    <hr class="border-secondary">

    <ul class="nav flex-column gap-2">
        <li class="nav-item">
            <a class="nav-link text-white <?= ($current_page == 'index.php') ? 'bg-primary active rounded shadow-sm' : 'opacity-75' ?>" href="index.php">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?= ($current_page == 'transaksi.php') ? 'bg-primary active rounded shadow-sm' : 'opacity-75' ?>" href="transaksi.php">
                <i class="fas fa-exchange-alt me-2"></i> Transaksi
            </a>
        </li>
        <li class="nav-item">
    <a class="nav-link text-white <?= ($current_page == 'cetak_filter.php') ? 'bg-primary active rounded' : 'opacity-75' ?>" href="cetak_filter.php">
        <i class="fas fa-file-invoice-dollar me-2"></i> Cetak Laporan
    </a>
</li>
        <li class="nav-item">
            <a class="nav-link text-white <?= ($current_page == 'kategori.php') ? 'bg-primary active rounded shadow-sm' : 'opacity-75' ?>" href="kategori.php">
                <i class="fas fa-th-large me-2"></i> Kategori
            </a>
        </li>
        <hr class="border-secondary">
        <li class="nav-item">
            <a class="nav-link text-white <?= ($current_page == 'profile.php' || $current_page == 'edit_profile.php') ? 'bg-primary active rounded shadow-sm' : 'opacity-75' ?>" href="profile.php">
                <i class="fas fa-user-circle me-2"></i> Profil Saya
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link text-danger fw-bold" href="logout.php" onclick="return confirm('Yakin mau keluar?')">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar
            </a>
        </li>
    </ul>
</div>