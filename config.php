<?php
$conn = mysqli_connect("SQLxxx.infinityfree.com", "if0_xxxxxxx", "PasswordLu", "if0_xxxxxxx_db_keuangan");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
