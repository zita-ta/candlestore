<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);

if (empty($name) || empty($email)) {
    $_SESSION['profile_error'] = 'Nama dan email wajib diisi.';
    header('Location: profile.php');
    exit;
}

// Cek email belum dipakai user lain
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->execute([$email, $_SESSION['user_id']]);
if ($stmt->fetch()) {
    $_SESSION['profile_error'] = 'Email sudah dipakai akun lain.';
    header('Location: profile.php');
    exit;
}

$stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
$stmt->execute([$name, $email, $_SESSION['user_id']]);

// Sinkronkan session biar navbar & form checkout ikut update
$_SESSION['name'] = $name;
$_SESSION['email'] = $email;

$_SESSION['profile_success'] = 'Profil berhasil diperbarui.';
header('Location: profile.php');
exit;
