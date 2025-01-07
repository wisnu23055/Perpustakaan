<?php
// Mulai sesi untuk melacak status login
session_start();

// Koneksi ke database
$conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');

// Proses login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username dan password untuk pustakawan
    $query = "SELECT * FROM pustakawan WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Menyimpan data pustakawan dalam sesi
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'pustakawan'; // Peran pengguna

            // Arahkan ke dashboard pustakawan
            header('Location: dashboard_pustakawan.php');
            exit();
        } else {
            echo "Password salah!";
        }
    } else {
        // Cek username dan password untuk anggota
        $query = "SELECT * FROM anggota WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Verifikasi password
            if (password_verify($password, $row['password'])) {
                // Menyimpan data anggota dalam sesi
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'anggota'; // Peran pengguna

                // Arahkan ke dashboard anggota
                header('Location: dashboard_anggota.php');
                exit();
            } else {
                echo "Password salah!";
            }
        } else {
            echo "Username tidak ditemukan!";
        }
    }
}

// Proses pendaftaran pustakawan
if (isset($_POST['register_pustakawan'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Tambahkan pustakawan baru ke database
    $query = "INSERT INTO pustakawan (username, password) VALUES ('$username', '$password')";
    if (mysqli_query($conn, $query)) {
        echo "Pustakawan berhasil didaftarkan!";
    } else {
        echo "Terjadi kesalahan: " . mysqli_error($conn);
    }
}

// Proses pendaftaran anggota
if (isset($_POST['register_anggota'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Tambahkan anggota baru ke database
    $query = "INSERT INTO anggota (username, password) VALUES ('$username', '$password')";
    if (mysqli_query($conn, $query)) {
        echo "Anggota berhasil didaftarkan!";
    } else {
        echo "Terjadi kesalahan: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/image/logo.jpg" type="image/jpg">
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Gaya umum untuk body */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        /* Gaya untuk kontainer tombol */
        .button-container {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Gaya untuk kontainer form */
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 10px auto;
            display: none;
        }

        /* Gaya untuk tombol */
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 0;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px 0;
            width: 100%;
        }

        button:hover {
            background-color: #3488db;
        }

        /* Gaya untuk input form */
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Gaya untuk judul */
        h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Responsif untuk perangkat mobile */
        @media (max-width: 600px) {
            .form-container {
                padding: 15px;
                max-width: 90%;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h2>Perpustakaan</h2>
    <div class="button-container">
        <!-- Tombol untuk menampilkan form login atau pendaftaran -->
        <button onclick="showForm('login')">Login</button>
        <button onclick="showForm('register_anggota')">Register Anggota</button>
    </div>

    <!-- Form Login -->
    <div id="login-form" class="form-container">
        <h3>Login</h3>
        <form method="POST" action="index.php">
            <label>Username:</label>
            <input type="text" name="username" required><br>
            
            <label>Password:</label>
            <input type="password" name="password" required><br>
            
            <button type="submit" name="login">Login</button>
        </form>
    </div>

    <!-- Form Pendaftaran Anggota -->
    <div id="register-anggota-form" class="form-container">
        <h3>Register Anggota</h3>
        <form method="POST" action="index.php">
            <label>Username:</label>
            <input type="text" name="username" required><br>
            
            <label>Password:</label>
            <input type="password" name="password" required><br>
            
            <button type="submit" name="register_anggota">Register Anggota</button>
        </form>
    </div>

    <script>
        // Fungsi untuk menampilkan form yang sesuai
        function showForm(form) {
            // Sembunyikan semua form
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-anggota-form').style.display = 'none';

            // Tampilkan form yang sesuai
            if (form === 'login') {
                document.getElementById('login-form').style.display = 'block';
            } else if (form === 'register_anggota') {
                document.getElementById('register-anggota-form').style.display = 'block';
            }
        }
    </script>
</body>
</html>
