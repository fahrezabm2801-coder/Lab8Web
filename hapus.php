<?php
require_once "koneksi.php";

// Pastikan ID yang diterima berupa angka
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php?pesan=" . urlencode("ID barang tidak valid."));
    exit;
}

// Ambil lokasi gambar sebelum data dihapus
$stmt = mysqli_prepare($conn, "SELECT gambar FROM data_barang WHERE id_barang = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php?pesan=" . urlencode("Data barang tidak ditemukan."));
    exit;
}

// Hapus data menggunakan prepared statement
$stmt = mysqli_prepare($conn, "DELETE FROM data_barang WHERE id_barang = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    // Hapus file gambar dari folder jika ada
    if (!empty($data["gambar"]) && file_exists(__DIR__ . "/" . $data["gambar"])) {
        unlink(__DIR__ . "/" . $data["gambar"]);
    }
    $pesan = "Barang berhasil dihapus.";
} else {
    $pesan = "Barang gagal dihapus.";
}

mysqli_stmt_close($stmt);

header("Location: index.php?pesan=" . urlencode($pesan));
exit;
?>
