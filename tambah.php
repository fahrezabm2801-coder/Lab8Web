<?php
require_once "koneksi.php";

$errors = [];
$nama = "";
$kategori = "Elektronik";
$harga_jual = "";
$harga_beli = "";
$stok = "";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Memproses data ketika tombol Simpan ditekan
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $harga_jual = trim($_POST["harga_jual"] ?? "");
    $harga_beli = trim($_POST["harga_beli"] ?? "");
    $stok = trim($_POST["stok"] ?? "");

    // Validasi input sederhana
    if ($nama === "") $errors[] = "Nama barang wajib diisi.";
    if (!in_array($kategori, ["Komputer", "Elektronik", "Hand Phone"], true)) {
        $errors[] = "Kategori tidak valid.";
    }
    if ($harga_jual === "" || !is_numeric($harga_jual) || (float)$harga_jual < 0) {
        $errors[] = "Harga jual harus berupa angka nol atau lebih.";
    }
    if ($harga_beli === "" || !is_numeric($harga_beli) || (float)$harga_beli < 0) {
        $errors[] = "Harga beli harus berupa angka nol atau lebih.";
    }
    if ($stok === "" || filter_var($stok, FILTER_VALIDATE_INT) === false || (int)$stok < 0) {
        $errors[] = "Stok harus berupa bilangan bulat nol atau lebih.";
    }

    // Menangani upload gambar jika pengguna memilih file
    $gambar = null;
    if (isset($_FILES["file_gambar"]) && $_FILES["file_gambar"]["error"] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES["file_gambar"]["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "Upload gambar gagal.";
        } else {
            $tmpName = $_FILES["file_gambar"]["tmp_name"];
            $fileSize = $_FILES["file_gambar"]["size"];
            $extension = strtolower(pathinfo($_FILES["file_gambar"]["name"], PATHINFO_EXTENSION));
            $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

            $imageInfo = getimagesize($tmpName);
            if ($fileSize > 2 * 1024 * 1024) {
                $errors[] = "Ukuran gambar maksimal 2 MB.";
            } elseif (!in_array($extension, $allowed, true) || $imageInfo === false) {
                $errors[] = "File harus berupa gambar JPG, PNG, GIF, atau WEBP.";
            } else {
                $newName = uniqid("barang_", true) . "." . $extension;
                $destination = __DIR__ . "/gambar/" . $newName;
                if (move_uploaded_file($tmpName, $destination)) {
                    $gambar = "gambar/" . $newName;
                } else {
                    $errors[] = "Gambar tidak dapat disimpan.";
                }
            }
        }
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO data_barang (nama, kategori, harga_jual, harga_beli, stok, gambar)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ssddis", $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambar);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?pesan=" . urlencode("Barang berhasil ditambahkan."));
            exit;
        } else {
            $errors[] = "Data gagal disimpan: " . mysqli_error($conn);
            if ($gambar && file_exists(__DIR__ . "/" . $gambar)) {
                unlink(__DIR__ . "/" . $gambar);
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container form-container">
        <h1>Tambah Barang</h1>
        <p class="subtitle">Masukkan informasi barang baru.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Periksa kembali data berikut:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="tambah.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama">Nama Barang</label>
                <input id="nama" type="text" name="nama" value="<?= e($nama) ?>" required>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="Komputer" <?= $kategori === "Komputer" ? "selected" : "" ?>>Komputer</option>
                    <option value="Elektronik" <?= $kategori === "Elektronik" ? "selected" : "" ?>>Elektronik</option>
                    <option value="Hand Phone" <?= $kategori === "Hand Phone" ? "selected" : "" ?>>Hand Phone</option>
                </select>
            </div>

            <div class="form-group">
                <label for="harga_jual">Harga Jual</label>
                <input id="harga_jual" type="number" name="harga_jual" min="0" step="0.01" value="<?= e($harga_jual) ?>" required>
            </div>

            <div class="form-group">
                <label for="harga_beli">Harga Beli</label>
                <input id="harga_beli" type="number" name="harga_beli" min="0" step="0.01" value="<?= e($harga_beli) ?>" required>
            </div>

            <div class="form-group">
                <label for="stok">Stok</label>
                <input id="stok" type="number" name="stok" min="0" step="1" value="<?= e($stok) ?>" required>
            </div>

            <div class="form-group">
                <label for="file_gambar">File Gambar (opsional, maksimal 2 MB)</label>
                <input id="file_gambar" type="file" name="file_gambar" accept="image/*">
            </div>

            <div class="form-actions">
                <button type="submit">Simpan</button>
                <a class="button button-secondary" href="index.php">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>
