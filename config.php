<?php
// config.php
session_start();

$host = "localhost";
$user = "root"; // XAMPP default
$pass = "";     // XAMPP default (empty)
$db   = "db_coffee_simple";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Small helper functions used by many pages
function e($text) {
    return htmlspecialchars($text);
}

function rupiah($number) {
    return "Rp " . number_format($number, 0, ",", ".");
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_customer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'customer';
}
?>
