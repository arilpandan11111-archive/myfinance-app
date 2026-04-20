<?php
date_default_timezone_set('Asia/Jakarta'); // Tambahin baris ini biar jamnya WIB!
ob_start();
session_start();
include 'config.php';

if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$id_user = $_SESSION['id_user'];
// Perbaikan agar tidak muncul "Undefined array key" seperti di Screenshot 65
$bulan_input = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$bulan = str_pad($bulan_input, 2, "0", STR_PAD_LEFT); 
$tahun = date('Y');

$nama_bulan = [
    '01' => 'JANUARI', '02' => 'FEBRUARI', '03' => 'MARET', '04' => 'APRIL',
    '05' => 'MEI', '06' => 'JUNI', '07' => 'JULI', '08' => 'AGUSTUS',
    '09' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER'
];

$sql = "SELECT * FROM transaksi WHERE id_user = '$id_user' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' ORDER BY tanggal ASC";
$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lap_<?= $bulan ?>_<?= $id_user ?></title>
    <style>
        /* Desain Custom - Biar gak kelihatan template banget */
        body { font-family: 'Courier New', Courier, monospace; color: #333; line-height: 1.2; }
        .wrapper { width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { border-bottom: 2px solid #000; padding: 10px; text-align: left; background: #f9f9f9; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        
        .total-section { margin-top: 30px; text-align: right; }
        .total-row { font-size: 16px; margin-bottom: 5px; }
        .grand-total { font-size: 20px; font-weight: bold; border-top: 2px solid #000; padding-top: 5px; display: inline-block; }
        
        .text-success { color: #2d8a2d; }
        .text-danger { color: #d9534f; }

        @media print {
            .no-print { display: none; }
            .wrapper { border: none; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()">PRINT LAPORAN</button>
        <button onclick="window.close()">TUTUP</button>
    </div>

    <div class="wrapper">
        <div class="header">
            <div class="title">Keuangan Pribadi: <?= $_SESSION['username'] ?></div>
            <div>Periode: <?= $nama_bulan[$bulan] ?> <?= $tahun ?></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>TGL</th>
                    <th>KETERANGAN</th>
                    <th>KATEGORI</th>
                    <th style="text-align: right;">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_in = 0; $total_out = 0;
                while($row = mysqli_fetch_assoc($query)): 
                    $is_in = ($row['tipe'] == 'masuk');
                    if($is_in) $total_in += $row['jumlah']; else $total_out += $row['jumlah'];
                ?>
                <tr>
                    <td><?= date('d/m', strtotime($row['tanggal'])) ?></td>
                    <td><?= strtoupper($row['keterangan']) ?></td>
                    <td><small><?= $row['kategori'] ?></small></td>
                    <td style="text-align: right;" class="<?= $is_in ? 'text-success' : 'text-danger' ?>">
                        <?= $is_in ? '+' : '-' ?> <?= number_format($row['jumlah'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">Total Masuk: Rp <?= number_format($total_in, 0, ',', '.') ?></div>
            <div class="total-row">Total Keluar: Rp <?= number_format($total_out, 0, ',', '.') ?></div>
            <div class="grand-total text-primary">SISA SALDO: Rp <?= number_format($total_in - $total_out, 0, ',', '.') ?></div>
        </div>
        
        <div style="margin-top: 50px; font-size: 10px; color: #aaa;">
            Dokumen ini dibuat otomatis oleh MyFinance System pada <?= date('d/m/Y H:i') ?>
        </div>
    </div>
</body>
</html>