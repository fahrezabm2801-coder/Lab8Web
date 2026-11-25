<?php
require_once "koneksi.php";

// Mengambil seluruh data barang dari database
$sql = "SELECT * FROM data_barang ORDER BY id_barang DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Gagal mengambil data: " . mysqli_error($conn));
}

// Fungsi sederhana untuk mengamankan teks saat ditampilkan ke HTML
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Pesan dari halaman tambah, ubah, atau hapus
$pesan = $_GET["pesan"] ?? "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="page-header">
            <div>
                <h1>Data Barang</h1>
            </div>
            <a class="button" href="tambah.php">+ Tambah Barang</a>
        </header>

        <?php if ($pesan !== ""): ?>
            <div class="alert"><?= e($pesan) ?></div>
        <?php endif; ?>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row["gambar"]) && file_exists(__DIR__ . "/" . $row["gambar"])): ?>
                                        <img class="product-image" src="<?= e($row["gambar"]) ?>" alt="<?= e($row["nama"]) ?>">
                                    <?php else: ?>
                                        <span class="no-image">Tidak ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($row["nama"]) ?></td>
                                <td><?= e($row["kategori"]) ?></td>
                                <td>Rp <?= number_format((float)$row["harga_beli"], 0, ",", ".") ?></td>
                                <td>Rp <?= number_format((float)$row["harga_jual"], 0, ",", ".") ?></td>
                                <td><?= e($row["stok"]) ?></td>
                                <td class="actions">
                                    <a href="ubah.php?id=<?= (int)$row["id_barang"] ?>">Ubah</a>
                                    <a class="delete-link"
                                       href="hapus.php?id=<?= (int)$row["id_barang"] ?>"
                                       onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty">Belum ada data barang.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
