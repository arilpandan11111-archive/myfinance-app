<?php

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar col-md-2 d-none d-md-block bg-dark text-white p-0">
    <div class="p-3">
        <h4 class="fw-bold mb-4"><i class="fas fa-wallet me-2"></i>MyFinance</h4>
        <ul class="nav flex-column gap-2">
            
            
            <li class="nav-item">
                <a class="nav-link text-white <?= ($current_page == 'index.php') ? 'bg-primary active rounded' : 'opacity-75' ?>" href="index.php">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>

            
            <li class="nav-item">
                <a class="nav-link text-white <?= ($current_page == 'transaksi.php') ? 'bg-primary active rounded' : 'opacity-75' ?>" href="transaksi.php">
                    <i class="fas fa-exchange-alt me-2"></i> Transaksi
                </a>
            </li>

                
    <li class="nav-item">
        <a class="nav-link text-white <?= ($current_page == 'cetak.php') ? 'bg-primary active rounded' : 'opacity-75' ?>" href="cetak.php">
            <i class="fas fa-file-invoice-dollar me-2"></i> Cetak Laporan
        </a>
    </li>

            
            <li class="nav-item">
                <a class="nav-link text-white <?= ($current_page == 'kategori.php') ? 'bg-primary active rounded' : 'opacity-75' ?>" href="kategori.php">
                    <i class="fas fa-th-large me-2"></i> Kategori
                </a>
            </li>

            <hr class="border-secondary">
            <li class="nav-item">
                <a class="nav-link text-white <?= ($current_page == 'profile.php') ? 'bg-primary active rounded' : 'opacity-75' ?>" href="profile.php">
                    <i class="fas fa-user-circle me-2"></i> Profil Saya
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i> Keluar
                </a>
            </li>
        </ul>
    </div>
</div>