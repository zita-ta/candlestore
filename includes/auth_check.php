<?php
// ================================================
// Auth Helper - Serenity's Glow
// Panggil file ini di paling atas halaman yang butuh proteksi login
// ================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login, kalau belum lempar ke halaman login
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /serenitys-glow/auth/login.php');
        exit;
    }
}

// Cek apakah user yang login punya role tertentu (admin/customer)
// Kalau rolenya beda, lempar balik ke dashboard yang sesuai rolenya
function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        if ($_SESSION['role'] === 'admin') {
            header('Location: /serenitys-glow/admin/dashboard.php');
        } else {
            header('Location: /serenitys-glow/customer/dashboard.php');
        }
        exit;
    }
}
