<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi sederhana
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['register_error'] = 'Semua field wajib diisi.';
        header('Location: register.php');
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = 'Konfirmasi password tidak cocok.';
        header('Location: register.php');
        exit;
    }

    if (strlen($password) < 6) {
        $_SESSION['register_error'] = 'Password minimal 6 karakter.';
        header('Location: register.php');
        exit;
    }

    // Cek email sudah dipakai atau belum
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['register_error'] = 'Email sudah terdaftar. Coba login.';
        header('Location: register.php');
        exit;
    }

    // Simpan user baru — role otomatis 'customer'
    // (role admin cuma dibuat manual lewat database, bukan lewat form ini)
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
    $stmt->execute([$name, $email, $hashedPassword]);

    $_SESSION['login_success'] = 'Akun berhasil dibuat! Silakan login.';
    header('Location: login.php');
    exit;
}
