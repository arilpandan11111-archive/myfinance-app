<nav class="col-md-2 d-none d-md-block sidebar bg-dark text-white p-3">
    <div class="text-center mb-4">
        <h4 class="fw-bold">💰 MyFinance</h4>
        <small class="text-muted">Halo, <?= $_SESSION['user'] ?? 'User'; ?></small>
        <hr class="border-secondary">
    </div>
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link text-white" href="index.php">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white font-weight-bold" href="kategori.php">
                <i class="fas fa-list me-2"></i> Kategori
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar
            </a>
        </li>
    </ul>
</nav>