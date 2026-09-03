<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit;
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$role = $_POST['role'] ?? 'customer';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (!in_array($role, ['admin', 'customer'])) {
    $role = 'customer';
}

// Validasi dasar
if (empty($name) || empty($email)) {
    $_SESSION['form_error'] = 'Nama dan email wajib diisi.';
    $_SESSION['form_old'] = $_POST;
    header('Location: user_add.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['form_error'] = 'Format email tidak valid.';
    $_SESSION['form_old'] = $_POST;
    header('Location: user_add.php');
    exit;
}

if (empty($password)) {
    $_SESSION['form_error'] = 'Password wajib diisi.';
    $_SESSION['form_old'] = $_POST;
    header('Location: user_add.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['form_error'] = 'Password minimal 6 karakter.';
    $_SESSION['form_old'] = $_POST;
    header('Location: user_add.php');
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['form_error'] = 'Konfirmasi password tidak cocok.';
    $_SESSION['form_old'] = $_POST;
    header('Location: user_add.php');
    exit;
}

// Cek email sudah dipakai atau belum
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['form_error'] = 'Email sudah dipakai akun lain.';
    $_SESSION['form_old'] = $_POST;
    header('Location: user_add.php');
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email, $hashedPassword, $role]);

$_SESSION['flash_success'] = 'User berhasil ditambahkan.';
header('Location: users.php');
exit;
