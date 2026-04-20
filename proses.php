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

// --- PROSES UPDATE DATA (EDIT) ---
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jumlah = (int)$_POST['jumlah'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);

    if (empty($id)) {
        die("Error: ID Transaksi hilang.");
    }

    // --- [VALIDASI SALDO KHUSUS UPDATE] ---
    if ($tipe == 'keluar') {
        // 1. Ambil jumlah transaksi yang lama dulu sebelum diupdate
        $q_lama = mysqli_query($conn, "SELECT jumlah FROM transaksi WHERE id='$id' AND id_user='$id_user'");
        $d_lama = mysqli_fetch_assoc($q_lama);
        $jumlah_lama = $d_lama['jumlah'] ?? 0;

        // 2. Hitung saldo total saat ini
        $q_cek = mysqli_query($conn, "SELECT SUM(CASE WHEN tipe='masuk' THEN jumlah ELSE -jumlah END) as saldo FROM transaksi WHERE id_user='$id_user'");
        $d_cek = mysqli_fetch_assoc($q_cek);
        $saldo_saat_ini = $d_cek['saldo'] ?? 0;

        // 3. Logika: Saldo Sekarang + Jumlah Lama (dibalikin dulu) harus cukup buat Jumlah Baru
        if ($jumlah > ($saldo_saat_ini + $jumlah_lama)) {
            echo "<script>alert('Gagal Update! Saldo tidak cukup untuk perubahan ini.'); window.location='index.php';</script>";
            exit();
        }
    }

    $query = "UPDATE transaksi SET 
                keterangan = '$keterangan', 
                jumlah = '$jumlah', 
                kategori = '$kategori', 
                tipe = '$tipe' 
              WHERE id = '$id' AND id_user = '$id_user'";
    
    if(mysqli_query($conn, $query)) {
        header("Location: index.php?pesan=update_berhasil");
        exit();
    } else {
        die("Gagal update data: " . mysqli_error($conn));
    }
}

// --- PROSES TAMBAH DATA (SIMPAN) ---
if (isset($_POST['simpan'])) {
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jumlah = (int)$_POST['jumlah'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
    $tgl_sekarang = date('Y-m-d');

    // [VALIDASI SALDO] Cek jika tipe pengeluaran
    if ($tipe == 'keluar') {
        $q_cek = mysqli_query($conn, "SELECT SUM(CASE WHEN tipe='masuk' THEN jumlah ELSE -jumlah END) as saldo FROM transaksi WHERE id_user='$id_user'");
        $d_cek = mysqli_fetch_assoc($q_cek);
        $saldo_saat_ini = $d_cek['saldo'] ?? 0;

        if ($jumlah > $saldo_saat_ini) {
            echo "<script>alert('Gagal! Saldo tidak cukup. Sisa saldo Anda: Rp " . number_format($saldo_saat_ini) . "'); window.location='index.php';</script>";
            exit();
        }
    }

    $query = "INSERT INTO transaksi (id_user, keterangan, jumlah, kategori, tipe, tanggal) 
              VALUES ('$id_user', '$keterangan', '$jumlah', '$kategori', '$tipe', '$tgl_sekarang')";
    
    if(mysqli_query($conn, $query)) {
        header("Location: index.php?pesan=tambah_berhasil");
        exit();
    }
}

// --- PROSES HAPUS DATA (Cukup Satu Aja Bree) ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    $query = mysqli_query($conn, "DELETE FROM transaksi WHERE id='$id' AND id_user='$id_user'");

    if ($query) {
        header("Location: index.php?pesan=hapus_berhasil");
        exit();
    } else {
        header("Location: index.php?pesan=gagal");
        exit();
    }
}
?>