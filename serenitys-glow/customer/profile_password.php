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

$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$hashedPassword = $stmt->fetchColumn();

if (!password_verify($currentPassword, $hashedPassword)) {
    $_SESSION['profile_error'] = 'Password saat ini salah.';
    header('Location: profile.php');
    exit;
}

if ($newPassword !== $confirmPassword) {
    $_SESSION['profile_error'] = 'Konfirmasi password baru tidak cocok.';
    header('Location: profile.php');
    exit;
}

if (strlen($newPassword) < 6) {
    $_SESSION['profile_error'] = 'Password baru minimal 6 karakter.';
    header('Location: profile.php');
    exit;
}

$newHashed = password_hash($newPassword, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$newHashed, $_SESSION['user_id']]);

$_SESSION['profile_success'] = 'Password berhasil diganti.';
header('Location: profile.php');
exit;
