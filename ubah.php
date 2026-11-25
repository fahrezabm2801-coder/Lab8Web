<?php
require_once "koneksi.php";

$errors = [];
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php?pesan=" . urlencode("ID barang tidak valid."));
    exit;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Mengambil data lama berdasarkan ID
$stmt = mysqli_prepare($conn, "SELECT * FROM data_barang WHERE id_barang = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php?pesan=" . urlencode("Data barang tidak ditemukan."));
    exit;
}

$nama = $data["nama"];
$kategori = $data["kategori"];
$harga_jual = $data["harga_jual"];
$harga_beli = $data["harga_beli"];
$stok = $data["stok"];
$gambarLama = $data["gambar"];

// Memproses perubahan ketika tombol Simpan ditekan
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $harga_jual = trim($_POST["harga_jual"] ?? "");
    $harga_beli = trim($_POST["harga_beli"] ?? "");
    $stok = trim($_POST["stok"] ?? "");

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

    $gambarBaru = null;
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
                    $gambarBaru = "gambar/" . $newName;
                } else {
                    $errors[] = "Gambar tidak dapat disimpan.";
                }
            }
        }
    }

    if (empty($errors)) {
        if ($gambarBaru !== null) {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE data_barang
                 SET nama = ?, kategori = ?, harga_jual = ?, harga_beli = ?, stok = ?, gambar = ?
                 WHERE id_barang = ?"
            );
            mysqli_stmt_bind_param($stmt, "ssddisi", $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambarBaru, $id);
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE data_barang
                 SET nama = ?, kategori = ?, harga_jual = ?, harga_beli = ?, stok = ?
                 WHERE id_barang = ?"
            );
            mysqli_stmt_bind_param($stmt, "ssddii", $nama, $kategori, $harga_jual, $harga_beli, $stok, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            // Hapus gambar lama hanya setelah gambar baru berhasil disimpan ke database
            if ($gambarBaru !== null && !empty($gambarLama) && file_exists(__DIR__ . "/" . $gambarLama)) {
                unlink(__DIR__ . "/" . $gambarLama);
            }

            header("Location: index.php?pesan=" . urlencode("Data barang berhasil diubah."));
            exit;
        } else {
            $errors[] = "Data gagal diubah: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
            if ($gambarBaru && file_exists(__DIR__ . "/" . $gambarBaru)) {
                unlink(__DIR__ . "/" . $gambarBaru);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container form-container">
        <h1>Ubah Barang</h1>
        <p class="subtitle">Perbarui informasi barang yang dipilih.</p>

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

        <form method="post" action="ubah.php?id=<?= (int)$id ?>" enctype="multipart/form-data">
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
                <label>Gambar Saat Ini</label>
                <?php if (!empty($gambarLama) && file_exists(__DIR__ . "/" . $gambarLama)): ?>
                    <img class="preview-image" src="<?= e($gambarLama) ?>" alt="<?= e($nama) ?>">
                <?php else: ?>
                    <p class="no-image">Belum ada gambar.</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="file_gambar">Ganti Gambar (opsional)</label>
                <input id="file_gambar" type="file" name="file_gambar" accept="image/*">
            </div>

            <div class="form-actions">
                <button type="submit">Simpan Perubahan</button>
                <a class="button button-secondary" href="index.php">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>
