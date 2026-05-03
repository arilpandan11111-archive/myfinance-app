<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// --- PROSES UPDATE ---
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jumlah = (int)$_POST['jumlah'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);

    if ($jumlah <= 0) {
        echo "<script>alert('Gagal! Jumlah tidak boleh nol atau negatif.'); window.history.back();</script>";
        exit();
    }

    // 1. Hitung total pemasukan & pengeluaran LAIN (selain transaksi yang lagi di-edit)
    $q_total = mysqli_query($conn, "SELECT 
        SUM(CASE WHEN tipe='masuk' AND id != '$id' THEN jumlah ELSE 0 END) as total_masuk_lain,
        SUM(CASE WHEN tipe='keluar' AND id != '$id' THEN jumlah ELSE 0 END) as total_keluar_lain
        FROM transaksi WHERE id_user='$id_user'");
    $d_total = mysqli_fetch_assoc($q_total);

    $masuk_lain = $d_total['total_masuk_lain'] ?? 0;
    $keluar_lain = $d_total['total_keluar_lain'] ?? 0;

    // 2. Simulasi saldo baru jika edit ini disimpan
    if ($tipe == 'masuk') {
        $simulasi_saldo_baru = ($masuk_lain + $jumlah) - $keluar_lain;
    } else {
        $simulasi_saldo_baru = $masuk_lain - ($keluar_lain + $jumlah);
    }

    // 3. Jika hasil simulasi bikin saldo minus, TOLAK!
    if ($simulasi_saldo_baru < 0) {
        echo "<script>alert('Gagal Update! Perubahan ini akan menyebabkan saldo minus (Sisa: Rp " . number_format($simulasi_saldo_baru) . ").'); window.history.back();</script>";
        exit();
    }

    $query = "UPDATE transaksi SET 
                keterangan = '$keterangan', 
                jumlah = '$jumlah', 
                kategori = '$kategori', 
                tipe = '$tipe' 
              WHERE id = '$id' AND id_user = '$id_user'";
    
    if(mysqli_query($conn, $query)) {
        header("Location: transaksi.php?pesan=update_berhasil");
        exit();
    }
}

if (isset($_POST['simpan'])) {
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jumlah = (int)$_POST['jumlah'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
    $tgl_sekarang = date('Y-m-d');

    // 1. CEK: Jumlah tidak boleh 0 atau minus
    if ($jumlah <= 0) {
        echo "<script>alert('Gagal! Jumlah harus lebih dari 0.'); window.history.back();</script>";
        exit();
    }

    // 2. CEK: Jika pengeluaran, apakah saldo cukup?
    if ($tipe == 'keluar') {
        $q_cek = mysqli_query($conn, "SELECT SUM(CASE WHEN tipe='masuk' THEN jumlah ELSE -jumlah END) as saldo FROM transaksi WHERE id_user='$id_user'");
        $d_cek = mysqli_fetch_assoc($q_cek);
        $saldo_saat_ini = $d_cek['saldo'] ?? 0;

        if ($jumlah > $saldo_saat_ini) {
            // Kalimat lebih formal untuk gagal simpan karena saldo tidak cukup
echo "<script>alert('Transaksi Gagal: Saldo tidak mencukupi untuk melakukan pengeluaran ini.'); window.history.back();</script>";
            exit();
        }
    }

    // 3. Jika lolos semua cek, baru INSERT
    $query = "INSERT INTO transaksi (id_user, keterangan, jumlah, kategori, tipe, tanggal) 
              VALUES ('$id_user', '$keterangan', '$jumlah', '$kategori', '$tipe', '$tgl_sekarang')";
    
    if(mysqli_query($conn, $query)) {
        header("Location: transaksi.php?pesan=tambah_berhasil");
        exit();
    }
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // 1. Ambil data yang mau dihapus buat dicek dulu
    $q_ambil = mysqli_query($conn, "SELECT jumlah, tipe FROM transaksi WHERE id='$id' AND id_user='$id_user'");
    $d_ambil = mysqli_fetch_assoc($q_ambil);
    $jumlah_hapus = $d_ambil['jumlah'] ?? 0;
    $tipe_hapus = $d_ambil['tipe'] ?? '';

    // 2. Hitung saldo saat ini
    $q_saldo = mysqli_query($conn, "SELECT SUM(CASE WHEN tipe='masuk' THEN jumlah ELSE -jumlah END) as saldo FROM transaksi WHERE id_user='$id_user'");
    $d_saldo = mysqli_fetch_assoc($q_saldo);
    $saldo_saat_ini = $d_saldo['saldo'] ?? 0;

    // 3. JIKA yang dihapus adalah uang MASUK, cek apakah sisa saldo cukup
    if ($tipe_hapus == 'masuk') {
        if (($saldo_saat_ini - $jumlah_hapus) < 0) {
            // Kalimat lebih formal untuk gagal hapus pemasukan
echo "<script>alert('Penghapusan Gagal: Menghapus transaksi pemasukan ini akan menyebabkan saldo akun Anda menjadi negatif.'); window.location='transaksi.php';</script>";
            exit();
        }
    }

    // 4. Kalau aman, baru jalankan hapus
    $query = mysqli_query($conn, "DELETE FROM transaksi WHERE id='$id' AND id_user='$id_user'");

    if ($query) {
        header("Location: transaksi.php?pesan=hapus_berhasil");
        exit();
    }
}