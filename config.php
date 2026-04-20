<?php
$conn = mysqli_connect("localhost", "root", "", "db_keuangan");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
