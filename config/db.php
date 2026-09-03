<?php
// ================================================
// Koneksi Database - Serenity's Glow
// ================================================

$host = 'localhost';
$dbname = 'serenitys_glow';
$username = 'root';   // sesuaikan dengan username MySQL kamu
$password = '';        // sesuaikan dengan password MySQL kamu

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Supaya error keliatan jelas kalau ada masalah query
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
