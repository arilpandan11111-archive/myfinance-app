<?php
ob_start();
session_start();
include 'config.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$filter_bulan = $_GET['bulan'] ?? '';

include 'template/header.php';
include 'template/sidebar.php';
?>

<main class="main-content col-md-10 ms-sm-auto">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2>Manajemen Transaksi</h2>
    </div>

    
    <?php if (isset($_GET['pesan'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Sistem:</strong> <?= ucfirst(str_replace('_', ' ', $_GET['pesan'])) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <div class="card p-4 mb-4 shadow-sm border-0 bg-light">
        <h6 class="fw-bold mb-3">Tambah Transaksi Baru</h6>
        <form action="proses.php" method="POST" class="row g-2">
            <div class="col-md-3"><input type="text" name="keterangan" class="form-control" placeholder="Keterangan..." required></div>
            <div class="col-md-2"><input type="number" name="jumlah" class="form-control" min="1" required placeholder="Jumlah Rp..."></div>
            <div class="col-md-2">
                <select name="kategori" class="form-select" required>
                    <option value="">-- Kategori --</option>
                    <?php
                    $kat_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                    while($row_kat = mysqli_fetch_array($kat_query)) {
                        echo "<option value='".$row_kat['nama_kategori']."'>".$row_kat['nama_kategori']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipe" class="form-select">
                    <option value="masuk">Pemasukan</option>
                    <option value="keluar">Pengeluaran</option>
                </select>
            </div>
            <div class="col-md-3"><button type="submit" name="simpan" class="btn btn-dark w-100">Simpan Transaksi</button></div>
        </form>
    </div>

    
    <div class="card p-4 shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Transaksi</h5>
            <form method="GET" class="d-flex gap-2">
                <select name="bulan" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">Semua Bulan</option>
                    <?php
                    $bulan_nama = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    foreach ($bulan_nama as $angka => $nama) {
                        $selected = ($filter_bulan == $angka) ? 'selected' : '';
                        echo "<option value='$angka' $selected>$nama</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
               
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Keterangan</th><th>Kategori</th><th>Tipe</th><th>Jumlah</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php
                    $query_text = "SELECT * FROM transaksi WHERE id_user = '$id_user'";
                    if ($filter_bulan != '') $query_text .= " AND MONTH(tanggal) = '$filter_bulan'";
                    $query_text .= " ORDER BY tanggal DESC";
                    $q_transaksi = mysqli_query($conn, $query_text);
                    while ($row = mysqli_fetch_assoc($q_transaksi)) :
                    ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= $row['keterangan'] ?></td>
                        <td><span class="badge bg-secondary"><?= $row['kategori'] ?></span></td>
                        <td><span class="<?= ($row['tipe'] == 'masuk') ? 'text-success' : 'text-danger' ?> fw-bold"><?= ucfirst($row['tipe']) ?></span></td>
                        <td class="fw-bold">Rp <?= number_format($row['jumlah']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                            <a href="proses.php?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php include 'template/footer.php'; ?>