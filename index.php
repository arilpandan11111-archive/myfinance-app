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

// Query untuk Card Saldo
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

// Query untuk Pie Chart
$sql_pie_text = "SELECT kategori, SUM(jumlah) as total FROM transaksi WHERE id_user = '$id_user' AND tipe = 'keluar'";
if ($bulan_angka != '') { $sql_pie_text .= " AND MONTH(tanggal) = $bulan_angka"; }
$sql_pie_text .= " GROUP BY kategori";
$res_pie = mysqli_query($conn, $sql_pie_text);

$labels = []; $totals = [];
while($p = mysqli_fetch_assoc($res_pie)){
    $labels[] = $p['kategori'];
    $totals[] = (int)$p['total']; 
}

include 'template/header.php';
?>

<div class="d-flex" style="min-height: 100vh;">
    <div class="flex-shrink-0 bg-dark" style="width: 250px;">
        <?php include 'template/sidebar.php'; ?>
    </div>

    <div class="flex-grow-1 bg-white p-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2 class="fw-bold">Dashboard Keuangan</h2>
                <form method="GET" class="d-flex gap-2">
                    <select name="bulan" class="form-select form-select-sm" style="width: 150px;">
                        <option value="">Semua Bulan</option>
                        <?php 
                        $nama_bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        foreach($nama_bulan as $angka => $nama): ?>
                            <option value="<?= $angka ?>" <?= ($filter_bulan == $angka) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                </form>
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
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx1 = document.getElementById('myChart');
    new Chart(ctx1, {
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
        new Chart(document.getElementById('pengeluaranChart'), {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                }]
            },
            options: { maintainAspectRatio: false }
        });
    }
});
</script>
<?php include 'template/footer.php'; ?>