<?php
// Username dan password
$username = "pustakawan";
$password = "pustakawan";

// Hashing password menggunakan algoritma BCRYPT
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Menampilkan hasil hash
echo "Username: " . $username . "<br>";
echo "Hashed Password: " . $hashed_password . "<br>";

// Contoh cara memverifikasi password ketika login
// $input_password adalah password yang dimasukkan saat login
$input_password = "pustakawan"; // Misalnya input dari form login

if (password_verify($input_password, $hashed_password)) {
    echo "Password valid!";
} else {
    echo "Password tidak valid!";
}
?>
