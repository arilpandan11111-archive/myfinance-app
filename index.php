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
$bulan_angka = ($filter_bulan != '') ? (int)$filter_bulan : '';


if ($bulan_angka != '') {
    $q_masuk = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transaksi WHERE tipe='masuk' AND id_user = '$id_user' AND MONTH(tanggal) = $bulan_angka");
    $q_keluar = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transaksi WHERE tipe='keluar' AND id_user = '$id_user' AND MONTH(tanggal) = $bulan_angka");
} else {
    $q_masuk = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transaksi WHERE tipe='masuk' AND id_user = '$id_user'");
    $q_keluar = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transaksi WHERE tipe='keluar' AND id_user = '$id_user'");
}

$m = mysqli_fetch_assoc($q_masuk);
$k = mysqli_fetch_assoc($q_keluar);
$total_masuk = $m['total'] ?? 0;
$total_keluar = $k['total'] ?? 0;
$saldo = $total_masuk - $total_keluar;


$sql_pie_text = "SELECT kategori, SUM(jumlah) as total FROM transaksi WHERE id_user = '$id_user' AND tipe = 'keluar'";
if ($bulan_angka != '') {
    $sql_pie_text .= " AND MONTH(tanggal) = $bulan_angka";
}
$sql_pie_text .= " GROUP BY kategori";
$res_pie = mysqli_query($conn, $sql_pie_text);


$labels = [];
$totals = [];
while($p = mysqli_fetch_assoc($res_pie)){
    $labels[] = $p['kategori'];
    $totals[] = (int)$p['total']; 
}

include 'template/header.php';
include 'template/sidebar.php';
?>

<main class="main-content col-md-10 ms-sm-auto">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h2>Dashboard Keuangan</h2>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card p-3 border-start border-success border-4 shadow-sm h-100">
                <p class="text-muted mb-1 text-uppercase small fw-bold">Pemasukan</p>
                <h3 class="text-success fw-bold">Rp <?= number_format($total_masuk) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-danger border-4 shadow-sm h-100">
                <p class="text-muted mb-1 text-uppercase small fw-bold">Pengeluaran</p>
                <h3 class="text-danger fw-bold">Rp <?= number_format($total_keluar) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card <?= ($saldo < 0) ? 'bg-danger text-white' : 'bg-primary text-white' ?> p-3 shadow h-100">
                <p class="mb-1 text-white-50 text-uppercase small fw-bold">Sisa Saldo</p>
                <h3 class="fw-bold">Rp <?= number_format($saldo) ?></h3>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card p-4 shadow-sm h-100">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-2 text-primary"></i>Perbandingan Keuangan</h6>
                <div style="height: 300px;"><canvas id="myChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 shadow-sm h-100">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2 text-info"></i>Pengeluaran/Kategori</h6>
                <div style="height: 300px;"><canvas id="pengeluaranChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-4 shadow-sm border-0 bg-light">
        <h6 class="fw-bold mb-3">Tambah Transaksi Baru</h6>
        <form action="proses.php" method="POST" class="row g-2">
            <div class="col-md-3"><input type="text" name="keterangan" class="form-control" placeholder="Keterangan..." required></div>
            <div class="col-md-2"><input type="number" name="jumlah" class="form-control" placeholder="Jumlah Rp..." required></div>
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
              <a href="cetak.php?bulan=<?= ($filter_bulan != '') ? $filter_bulan : date('m') ?>" 
   target="_blank" 
   class="btn btn-sm btn-success">
   <i class="fas fa-print"></i> Cetak Laporan
</a>
            </form>
        </div>
<?php if (isset($_GET['pesan'])): ?>
    <div class="alert alert-<?= ($_GET['pesan'] == 'gagal') ? 'danger' : 'info' ?> alert-dismissible fade show mt-3" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Sistem:</strong> 
        <?php 
            if($_GET['pesan'] == 'tambah_berhasil') echo "Data transaksi baru berhasil disimpan!";
            if($_GET['pesan'] == 'update_berhasil') echo "Perubahan data berhasil diperbarui!";
            if($_GET['pesan'] == 'hapus_berhasil') echo "Data transaksi telah dihapus dari database.";
            if($_GET['pesan'] == 'gagal') echo "Aksi gagal dilakukan. Silakan cek koneksi atau saldo.";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th><th>Keterangan</th><th>Kategori</th><th>Tipe</th><th>Jumlah</th><th>Aksi</th>
                    </tr>
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
                        <td>
                            <span class="<?= ($row['tipe'] == 'masuk') ? 'text-success' : 'text-danger' ?> fw-bold">
                                <i class="fas fa-arrow-<?= ($row['tipe'] == 'masuk') ? 'up' : 'down' ?> me-1"></i><?= ucfirst($row['tipe']) ?>
                            </span>
                        </td>
                        <td class="fw-bold">Rp <?= number_format($row['jumlah']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                           <a href="proses.php?hapus=<?= $row['id']; ?>" 
   class="btn btn-sm btn-outline-danger" 
   onclick="return confirm('Apakah Anda yakin ingin menghapus data transaksi ini secara permanen?')">
   <i class="fas fa-trash"></i>
</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

function createChart(ctxId, config) {
    const canvas = document.getElementById(ctxId);
    const existingChart = Chart.getChart(canvas);
    if (existingChart) {
        existingChart.destroy();
    }
    return new Chart(canvas, config);
}

document.addEventListener("DOMContentLoaded", function() {
    // 1. Grafik Batang
    createChart('myChart', {
        type: 'bar',
        data: {
            labels: ['Total Pemasukan', 'Total Pengeluaran'],
            datasets: [{
                label: 'Jumlah Rupiah',
                data: [<?= (int)$total_masuk ?>, <?= (int)$total_keluar ?>],
                backgroundColor: ['#1cc88a', '#e74a3b']
            }]
        },
        options: { maintainAspectRatio: false }
    });

    
    const pieData = <?= json_encode($totals) ?>;
    const pieLabels = <?= json_encode($labels) ?>;

    if (pieLabels.length > 0) {
        createChart('pengeluaranChart', {
            type: 'doughnut',
            data: {
                labels: pieLabels, 
                datasets: [{
                    data: pieData,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    } else {
       
        const ctx = document.getElementById('pengeluaranChart').getContext('2d');
        ctx.font = "14px Arial";
        ctx.textAlign = "center";
        ctx.fillText("Tidak ada data pengeluaran", ctx.canvas.width/2, ctx.canvas.height/2);
    }
});
</script>
<?php include 'template/footer.php'; ?>