<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php'; // Gunakan lagi config agar praktis


if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];


if (isset($_POST['update'])) {
    $id_edit = $_POST['id_transaksi'];
    
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jml = (int)$_POST['jumlah']; // Pastikan jadi angka
    $kat = mysqli_real_escape_string($conn, $_POST['kategori']);
    $tip = mysqli_real_escape_string($conn, $_POST['tipe']);

    $sql_update = "UPDATE transaksi SET 
                   keterangan = '$ket', 
                   jumlah = '$jml', 
                   kategori = '$kat', 
                   tipe = '$tip' 
                   WHERE id = '$id_edit' AND id_user = '$id_user'";
    
    if(mysqli_query($conn, $sql_update)) {
        
        header("Location: index.php?pesan=update_berhasil");
        exit;
    }
}

$id_get = $_GET['id'];
$query_lama = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = '$id_get' AND id_user = '$id_user'");
$data = mysqli_fetch_assoc($query_lama);

if (!$data) {
    header("Location: index.php");
    exit;
}


include 'template/header.php';
include 'template/sidebar.php';
?>

<main class="main-content col-md-10 ms-sm-auto p-4">
    <div class="card p-4 shadow-sm border-0" style="border-radius: 15px;">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-warning p-2 rounded-3 me-3 text-white">
                <i class="fas fa-edit"></i>
            </div>
            <h5 class="mb-0">Edit Transaksi</h5>
        </div>
        <hr>
        
        <form action="" method="POST">
            <input type="hidden" name="id_transaksi" value="<?= $data['id'] ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold text-muted">Keterangan</label>
                <input type="text" name="keterangan" class="form-control form-control-lg bg-light" value="<?= $data['keterangan'] ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" class="form-control form-control-lg bg-light" value="<?= $data['jumlah'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted">Kategori</label>
                    <select name="kategori" class="form-select form-select-lg bg-light" required>
                        <?php
                        $kat_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while($row_kat = mysqli_fetch_array($kat_query)) {
                            $selected = ($row_kat['nama_kategori'] == $data['kategori']) ? 'selected' : '';
                            echo "<option value='".$row_kat['nama_kategori']."' $selected>".$row_kat['nama_kategori']."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-muted">Tipe Transaksi</label>
                <div class="d-flex gap-3">
                    <div class="form-check p-3 border rounded-3 flex-fill">
                        <input class="form-check-input" type="radio" name="tipe" id="masuk" value="masuk" <?= ($data['tipe'] == 'masuk') ? 'checked' : '' ?>>
                        <label class="form-check-label text-success fw-bold" for="masuk">Pemasukan</label>
                    </div>
                    <div class="form-check p-3 border rounded-3 flex-fill">
                        <input class="form-check-input" type="radio" name="tipe" id="keluar" value="keluar" <?= ($data['tipe'] == 'keluar') ? 'checked' : '' ?>>
                        <label class="form-check-label text-danger fw-bold" for="keluar">Pengeluaran</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-light px-5 py-2 border fw-bold">Batal</a>
            </div>
        </form>
    </div>
</main>

<?php include 'template/footer.php'; ?>