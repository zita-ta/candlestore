<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$currentPassword = $_POST['current_password'];
$newPassword = $_POST['new_password'];
$confirmPassword = $_POST['confirm_password'];

if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    $_SESSION['password_error'] = 'Semua field wajib diisi.';
    header('Location: profile.php');
    exit;
}

if ($newPassword !== $confirmPassword) {
    $_SESSION['password_error'] = 'Konfirmasi password baru tidak cocok.';
    header('Location: profile.php');
    exit;
}

if (strlen($newPassword) < 6) {
    $_SESSION['password_error'] = 'Password baru minimal 6 karakter.';
    header('Location: profile.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($currentPassword, $user['password'])) {
    $_SESSION['password_error'] = 'Password saat ini salah.';
    header('Location: profile.php');
    exit;
}

$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$hashedPassword, $_SESSION['user_id']]);

$_SESSION['password_success'] = 'Password berhasil diganti.';
header('Location: profile.php');
exit;
