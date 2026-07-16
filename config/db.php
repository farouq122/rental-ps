<?php
$host    = '127.0.0.1';
$db      = 'rental_ps';
$user    = 'rentaluser';
$pass    = 'RentalPS@2026!'; // Silakan ganti PASSWORD ini sesuai dengan credential database Anda di Azure VM
$charset = 'utf8mb4';

$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('<div style="font-family:sans-serif;color:red;padding:20px;"><h2>Koneksi Database Gagal</h2><p>' . htmlspecialchars($e->getMessage()) . '</p></div>');
}

// Base URL — sesuaikan dengan path sub-folder di server (http://IP_SERVER/rental-ps)
define('BASE_URL', '/rental-ps');
?>
