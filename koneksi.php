<?php
// Pengaturan koneksi ke MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db   = "latihan 1";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Menghentikan program jika koneksi gagal
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengatur karakter agar mendukung teks UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>
