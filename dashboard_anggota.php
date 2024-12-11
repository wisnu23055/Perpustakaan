<?php
session_start();

// Pastikan anggota sudah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Koneksi ke database
$conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data anggota
$username = $_SESSION['username'];
$query = "SELECT * FROM anggota WHERE username = ?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Kesalahan query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$anggota = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Ambil daftar buku yang dipinjam oleh anggota
$peminjaman_query = "
    SELECT b.judul, b.penulis, b.nomor_buku
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id
    WHERE p.id_anggota = {$anggota['id']}
";
$peminjaman_result = mysqli_query($conn, $peminjaman_query);

// Cek apakah query berhasil atau tidak
if (!$peminjaman_result) {
    die("Query gagal: " . mysqli_error($conn));
}

// Ambil daftar buku yang tersedia untuk dipinjam
$buku_tersedia_query = "
    SELECT * FROM buku
    WHERE id NOT IN (
        SELECT id_buku FROM peminjaman WHERE id_anggota = {$anggota['id']} AND tanggal_kembali IS NULL
    )
";
$buku_tersedia_result = mysqli_query($conn, $buku_tersedia_query);

// Cek apakah query berhasil atau tidak
if (!$buku_tersedia_result) {
    die("Query gagal: " . mysqli_error($conn));
}

// Fitur Meminjam Buku
if (isset($_POST['pinjam'])) {
    $buku_id = $_POST['buku_id'];
    $id_anggota = $anggota['id']; // Mengambil id_anggota dari session
    $tanggal_pinjam = date('Y-m-d'); // Menggunakan tanggal saat ini
    $tanggal_kembali = null; // Belum ada tanggal kembali karena buku masih dipinjam

    // Pastikan query berhasil dijalankan
    $peminjaman_query = "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali) VALUES ('$id_anggota', '$buku_id', '$tanggal_pinjam', '$tanggal_kembali')";
    if (mysqli_query($conn, $peminjaman_query)) {
        echo "Buku berhasil dipinjam!";
    } else {
        echo "Gagal meminjam buku: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background-color: #f0f4f8;
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        .message, .logout-container {
            text-align: center;
            margin: 20px 0;
        }

        .logout-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            max-width: 800px;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }

        td {
            background-color: #fafafa;
        }

        button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #27ae60;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            table {
                font-size: 14px;
            }

            button {
                padding: 8px 16px;
            }

            .logout-button {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <h2>Selamat datang, <?php echo htmlspecialchars($anggota['username']); ?>!</h2>

    <!-- Pesan login berhasil -->
    <div class="message">
        <p>Login berhasil sebagai anggota!</p>
    </div>

    <!-- Tombol Logout -->
    <div class="logout-container">
        <a href="index.php" class="logout-button">Logout</a>
    </div>

    <!-- Daftar Buku yang Dipinjam -->
    <h3>Daftar Buku yang Dipinjam:</h3>
    <table>
        <tr>
            <th>Judul Buku</th>
            <th>Pengarang</th>
            <th>Nomor Buku</th>
        </tr>
        <?php while ($peminjaman = mysqli_fetch_assoc($peminjaman_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($peminjaman['judul']); ?></td>
                <td><?php echo htmlspecialchars($peminjaman['penulis']); ?></td>
                <td><?php echo htmlspecialchars($peminjaman['nomor_buku']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Daftar Buku yang Tersedia -->
    <h3>Daftar Buku yang Tersedia untuk Dipinjam:</h3>
    <table>
        <tr>
            <th>Judul Buku</th>
            <th>Pengarang</th>
            <th>Nomor Buku</th>
            <th>Aksi</th>
        </tr>
        <?php while ($buku = mysqli_fetch_assoc($buku_tersedia_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($buku['judul']); ?></td>
                <td><?php echo htmlspecialchars($buku['penulis']); ?></td>
                <td><?php echo htmlspecialchars($buku['nomor_buku']); ?></td>
                <td>
                    <!-- Form untuk meminjam buku -->
                    <form method="post">
                        <input type="hidden" name="buku_id" value="<?php echo $buku['id']; ?>">
                        <button type="submit" name="pinjam">Pinjam Buku</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
