<?php
session_start();
include 'config.php';
include 'template/header.php';
include 'template/sidebar.php';

if (isset($_POST['tambah_kategori'])) {
    $nama = $_POST['nama_kategori'];
    $jenis = $_POST['jenis'];
    mysqli_query($conn, "INSERT INTO kategori (nama_kategori, jenis) VALUES ('$nama', '$jenis')");
    header("Location: kategori.php");
}


if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kategori WHERE id = $id");
    header("Location: kategori.php");
}
?>

<main class="main-content col-md-10 ms-sm-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Kategori</h2>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Tambah Kategori</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Jenis</label>
                        <select name="jenis" class="form-select">
                            <option value="masuk">Pemasukan</option>
                            <option value="keluar">Pengeluaran</option>
                        </select>
                    </div>
                    <button type="submit" name="tambah_kategori" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Jenis</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($conn, "SELECT * FROM kategori ORDER BY jenis ASC");
                        while($row = mysqli_fetch_array($q)) { ?>
                            <tr>
                                <td><?= $row['nama_kategori'] ?></td>
                                <td>
                                    <span class="badge <?= $row['jenis'] == 'masuk' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($row['jenis']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="kategori.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include 'template/footer.php'; ?>