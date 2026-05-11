<?php
ob_start();
session_start();
include 'config.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
include 'template/header.php';
?>

<div class="d-flex" style="min-height: 100vh;">
    <div class="flex-shrink-0 bg-dark" style="width: 250px;">
        <?php include 'template/sidebar.php'; ?>
    </div>

    <div class="flex-grow-1 bg-light p-4">
        <div class="container-fluid">
            <div class="pt-3 pb-2 mb-3 border-bottom">
                <h2 class="fw-bold">Cetak Laporan Keuangan</h2>
            </div>

            <div class="card shadow-sm border-0" style="max-width: 500px;">
                <div class="card-body p-4">
                    <h5 class="mb-3 text-secondary">Pilih Periode Laporan</h5>
                    <form action="cetak.php" method="GET" target="_blank">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pilih Bulan</label>
                            <select name="bulan" class="form-select" required>
                                <?php
                                $bulan_nama = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                                $bulan_sekarang = date('n');
                                foreach ($bulan_nama as $angka => $nama) {
                                    $selected = ($angka == $bulan_sekarang) ? 'selected' : '';
                                    echo "<option value='$angka' $selected>$nama</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-print me-2"></i>Buka Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'template/footer.php'; ?>