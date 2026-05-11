<?php
session_start();
include 'config.php';
include 'template/header.php';

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

<div class="d-flex" style="min-height: 100vh;">
    <div class="flex-shrink-0 bg-dark" style="width: 250px;">
        <?php include 'template/sidebar.php'; ?>
    </div>

    <div class="flex-grow-1 bg-white p-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h2 class="fw-bold">Manajemen Kategori</h2>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card p-3 shadow-sm border-0 bg-light">
                        <h5 class="fw-bold mb-3">Tambah Kategori</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Kategori</label>
                                <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Gaji, Makan..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jenis</label>
                                <select name="jenis" class="form-select">
                                    <option value="masuk">Pemasukan</option>
                                    <option value="keluar">Pengeluaran</option>
                                </select>
                            </div>
                            <button type="submit" name="tambah_kategori" class="btn btn-primary w-100 shadow-sm">
                                <i class="fas fa-save me-2"></i>Simpan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Kategori</th>
                                        <th>Jenis</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q = mysqli_query($conn, "SELECT * FROM kategori ORDER BY jenis ASC");
                                    while($row = mysqli_fetch_array($q)) { ?>
                                        <tr class="align-middle">
                                            <td class="fw-bold"><?= $row['nama_kategori'] ?></td>
                                            <td>
                                                <span class="badge <?= $row['jenis'] == 'masuk' ? 'bg-success' : 'bg-danger' ?> px-3">
                                                    <?= ucfirst($row['jenis']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
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
            </div> </div> </div> </div> <?php include 'template/footer.php'; ?>