<?php
date_default_timezone_set('Asia/Jakarta');

$host     = getenv('MYSQLHOST') ?: 'localhost';
$dbname   = getenv('MYSQLDATABASE') ?: 'serenitys_glow';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';
$port     = getenv('MYSQLPORT') ?: '3306';
try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET time_zone = '+07:00'");
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
