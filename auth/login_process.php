<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email dan password wajib diisi.';
        header('Location: login.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek user ada & password cocok
    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['login_error'] = 'Email atau password salah.';
        header('Location: login.php');
        exit;
    }

    // Login berhasil -> simpan data user di session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    // Inilah bagian yang bikin 1 halaman login, tapi tujuan beda sesuai role
    if ($user['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../customer/dashboard.php');
    }
    exit;
}
