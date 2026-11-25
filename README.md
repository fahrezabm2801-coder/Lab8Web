# LAPORAN PRAKTIKUM 8
## PHP dan Database MySQL

### 1. Identitas Praktikum
- **Mata Kuliah:** Pemrograman Web
- **Praktikum:** 8
- **Nama:** Fahreza Bintang Mahesa
- **NIM:** 312110220

### 2. Langkah-Langkah Praktikum

#### Langkah 1 — Menjalankan XAMPP
Menjalankan Apache dan MySQL melalui XAMPP Control Panel agar aplikasi PHP dapat diakses dan terhubung ke database.

![imge alt]()

#### Langkah 2 — Membuat Database
Membuka phpMyAdmin melalui `http://localhost/phpmyadmin/`, kemudian membuat database dengan nama `latihan1`.

**Screenshot:** `screenshots/02-database.png` — database latihan1 di phpMyAdmin.

![Database](screenshots/02-database.png)

#### Langkah 3 — Membuat Tabel data_barang
Membuat tabel `data_barang` dengan kolom `id_barang`, `kategori`, `nama`, `gambar`, `harga_beli`, `harga_jual`, dan `stok`. Kolom `id_barang` menjadi primary key dan auto increment. Kolom `gambar` boleh bernilai NULL karena gambar bersifat opsional.

**Screenshot:** `screenshots/03-struktur-tabel.png` — struktur tabel data_barang.

![Struktur tabel](screenshots/03-struktur-tabel.png)

#### Langkah 4 — Memasukkan Data Awal
Memasukkan tiga data barang awal, yaitu HP Samsung Android, HP Xiaomi Android, dan HP OPPO Android, untuk menguji proses menampilkan data.

**Screenshot:** `screenshots/04-data-awal.png` — isi tabel data_barang di phpMyAdmin.

![Data awal](screenshots/04-data-awal.png)

#### Langkah 5 — Membuat Folder Project
Membuat folder `lab8_php_database` di dalam direktori `htdocs` XAMPP sebagai tempat menyimpan file aplikasi.

**Screenshot:** `screenshots/05-folder-project.png` — folder project di dalam htdocs.

![Folder project](screenshots/05-folder-project.png)

#### Langkah 6 — Membuat Koneksi Database
Membuat file `koneksi.php` untuk menghubungkan PHP dengan MySQL menggunakan `mysqli_connect()`. Program akan menampilkan pesan kesalahan jika koneksi gagal.

**Screenshot:** `screenshots/06-koneksi.png` — kode koneksi atau hasil pengujian koneksi.

![Koneksi](screenshots/06-koneksi.png)

#### Langkah 7 — Menampilkan Data (Read)
Membuat `index.php` yang menjalankan query `SELECT` untuk mengambil data barang dari database dan menampilkannya dalam tabel HTML.

**Screenshot:** `screenshots/07-read.png` —halaman utama yang menampilkan daftar barang.

![Read](screenshots/07-read.png)

#### Langkah 8 — Menambah Data (Create)
Membuat `tambah.php` berisi formulir data barang. Data dikirim menggunakan POST dan disimpan ke database dengan query `INSERT`. Gambar dapat diunggah secara opsional.

**Screenshot:**
- `screenshots/08-form-tambah.png` — formulir tambah barang.
- `screenshots/09-hasil-tambah.png` — daftar barang setelah penambahan berhasil.

![Form tambah](screenshots/08-form-tambah.png)

![Hasil tambah](screenshots/09-hasil-tambah.png)

#### Langkah 9 — Mengubah Data (Update)
Membuat `ubah.php` untuk mengambil data berdasarkan `id_barang`, menampilkan data lama pada formulir, dan menyimpan perubahan menggunakan query `UPDATE`.

**Screenshot:**
- `screenshots/10-form-ubah.png` — formulir ubah barang.
- `screenshots/11-hasil-ubah.png` — data setelah perubahan tersimpan.

![Form ubah](screenshots/10-form-ubah.png)

![Hasil ubah](screenshots/11-hasil-ubah.png)

#### Langkah 10 — Menghapus Data (Delete)
Membuat `hapus.php` untuk menghapus data berdasarkan `id_barang` menggunakan query `DELETE`, kemudian kembali ke halaman utama.

**Screenshot:**
- `screenshots/12-konfirmasi-hapus.png` — konfirmasi sebelum menghapus.
- `screenshots/13-hasil-hapus.png` — daftar setelah data dihapus.

![Konfirmasi hapus](screenshots/12-konfirmasi-hapus.png)

![Hasil hapus](screenshots/13-hasil-hapus.png)

### 3. Struktur Folder
```text
lab8_php_database/
├── gambar/
├── koneksi.php
├── index.php
├── tambah.php
├── ubah.php
├── hapus.php
└── style.css
```

### 4. Kesimpulan
Praktikum ini menunjukkan bahwa PHP dapat digunakan untuk mengelola data pada MySQL. Aplikasi menerapkan empat operasi CRUD, yaitu Create (menambah), Read (menampilkan), Update (mengubah), dan Delete (menghapus) data barang. Praktikum juga melatih penggunaan koneksi database, query SQL, formulir, dan upload gambar.

### 5. Repository
**URL GitHub:** [https://github.com/fahrezabm2801-coder/Lab8Web.git]
