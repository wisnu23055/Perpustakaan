<?php
// Mulai session
session_start();

// Cek apakah pengguna sudah login sebagai pustakawan
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

// Periksa koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data pustakawan berdasarkan username
$username_pustakawan = $_SESSION['username'];
$query_pustakawan = "SELECT * FROM pustakawan WHERE username = '$username_pustakawan'";
$pustakawan_result = mysqli_query($koneksi, $query_pustakawan);
$pustakawan = mysqli_fetch_assoc($pustakawan_result);

// Menangani proses hapus peminjaman
if (isset($_GET['hapus_peminjaman'])) {
    $id_peminjaman = $_GET['hapus_peminjaman'];
    $query_hapus_peminjaman = "DELETE FROM peminjaman WHERE id = '$id_peminjaman'";
    mysqli_query($koneksi, $query_hapus_peminjaman);
    echo "<script>alert('Peminjaman berhasil dihapus');</script>";
}

// Menangani proses hapus buku
if (isset($_GET['hapus_buku'])) {
    $id_buku = $_GET['hapus_buku'];
    $query_hapus_buku = "DELETE FROM buku WHERE id = '$id_buku'";
    mysqli_query($koneksi, $query_hapus_buku);
    echo "<script>alert('Buku berhasil dihapus');</script>";
}

// Menangani proses hapus anggota
if (isset($_GET['hapus_anggota'])) {
    $id_anggota = $_GET['hapus_anggota'];
    $query_hapus_anggota = "DELETE FROM anggota WHERE id = '$id_anggota'";
    mysqli_query($koneksi, $query_hapus_anggota);
    echo "<script>alert('Anggota berhasil dihapus');</script>";
}

// Menangani proses tambah anggota
if (isset($_POST['tambah_anggota'])) {
    $nama = $_POST['nama'];
    $username_anggota = $_POST['username'];
    $password_anggota = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query_tambah_anggota = "INSERT INTO anggota (nama, username, password) VALUES ('$nama', '$username_anggota', '$password_anggota')";
    mysqli_query($koneksi, $query_tambah_anggota);
    echo "<script>alert('Anggota berhasil ditambahkan');</script>";
}

// Menangani proses tambah buku
if (isset($_POST['tambah_buku'])) {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $nomor_buku = $_POST['nomor_buku'];
    
    // Pastikan input tidak kosong
    if (!empty($judul) && !empty($penulis) && !empty($nomor_buku)) {
        $query_tambah_buku = "INSERT INTO buku (judul, penulis, nomor_buku) VALUES ('$judul', '$penulis', '$nomor_buku')";
        if (mysqli_query($koneksi, $query_tambah_buku)) {
            echo "<script>alert('Buku berhasil ditambahkan');</script>";
        } else {
            echo "<script>alert('Gagal menambahkan buku. Silakan coba lagi.');</script>";
        }
    } else {
        echo "<script>alert('Semua field harus diisi.');</script>";
    }
}

// Ambil daftar anggota
$query_anggota = "SELECT * FROM anggota";
$anggota_result = mysqli_query($koneksi, $query_anggota);

// Ambil daftar buku
$query_buku = "SELECT * FROM buku";
$buku_result = mysqli_query($koneksi, $query_buku);

// Ambil data peminjaman
$query_peminjaman = "SELECT peminjaman.*, anggota.username AS username_anggota, buku.judul AS judul_buku FROM peminjaman 
                     JOIN anggota ON peminjaman.id_anggota = anggota.id 
                     JOIN buku ON peminjaman.id_buku = buku.id";
$peminjaman_result = mysqli_query($koneksi, $query_peminjaman);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pustakawan</title>
    <!-- Tambahkan Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Tambahkan CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        h1, h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600; /* Bold untuk header */
        }
        .card-header{
            background-color: #3498db;
        }
        #daftaranggota{
            background-color: #2ecc71;
        }

    </style>
</head>
<body class="bg-light">
    <div class="container my-4">
        <h1 class="text-center">Dashboard Pustakawan</h1>
        <h2 class="text-center">Selamat datang, <?php echo htmlspecialchars($pustakawan['username']); ?>!</h2>

        <div class="d-flex justify-content-end my-3">
            <a href="index.php" class="btn btn-danger">Logout</a>
        </div>

        <!-- Tambah Anggota -->
        <div class="card mb-4">
            <div class="card-header text-white">Tambah Anggota</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama:</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" name="tambah_anggota" class="btn btn-primary">Tambah Anggota</button>
                </form>
            </div>
        </div>

        <!-- Tambah Buku -->
        <div class="card mb-4">
            <div class="card-header text-white">Tambah Buku</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Buku:</label>
                        <input type="text" name="judul" id="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="penulis" class="form-label">Penulis:</label>
                        <input type="text" name="penulis" id="penulis" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_buku" class="form-label">Nomor Buku:</label>
                        <input type="text" name="nomor_buku" id="nomor_buku" class="form-control" required>
                    </div>
                    <button type="submit" name="tambah_buku" class="btn btn-primary">Tambah Buku</button>
                </form>
            </div>
        </div>

        <!-- Daftar Anggota -->
        <div class="card mb-4">
            <div id="daftaranggota" class="card-header text-white">Daftar Anggota</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($anggota = mysqli_fetch_assoc($anggota_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($anggota['username']); ?></td>
                            <td><?php echo htmlspecialchars($anggota['nama']); ?></td>
                            <td>
                                <a href="?hapus_anggota=<?php echo $anggota['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Buku -->
        <div class="card mb-4">
            <div id="daftaranggota" class="card-header text-white">Daftar Buku</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Nomor Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($buku = mysqli_fetch_assoc($buku_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($buku['judul']); ?></td>
                            <td><?php echo htmlspecialchars($buku['penulis']); ?></td>
                            <td><?php echo htmlspecialchars($buku['nomor_buku']); ?></td>
                            <td>
                                <a href="?hapus_buku=<?php echo $buku['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Peminjaman -->
        <div class="card mb-4">
            <div id="daftaranggota" class="card-header text-white">Daftar Peminjaman Buku</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Username Anggota</th>
                            <th>Judul Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($peminjaman = mysqli_fetch_assoc($peminjaman_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($peminjaman['username_anggota']); ?></td>
                            <td><?php echo htmlspecialchars($peminjaman['judul_buku']); ?></td>
                            <td>
                                <a href="?hapus_peminjaman=<?php echo $peminjaman['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menkonfirmasi bawa buku peminjaman ini sudah di kembalikan?')">Di Kembalikan</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tambahkan Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
