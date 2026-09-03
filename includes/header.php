<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? "Serenity's Glow" ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (!empty($includeLandingCss)): ?>
    <link rel="stylesheet" href="assets/css/landing.css">
    <?php endif; ?>
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <svg class="logo-mark" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M16 3c1.6 2.3 2.4 4 2.4 5.3 0 1.5-1.1 2.6-2.4 2.6s-2.4-1.1-2.4-2.6C13.6 7 14.4 5.3 16 3z" fill="var(--soft-pink-dark)"/>
                <rect x="9.5" y="13" width="13" height="14.5" rx="3" fill="var(--soft-green)"/>
                <rect x="9.5" y="13" width="13" height="4" rx="2" fill="var(--soft-green-dark)"/>
            </svg>
            Serenity's Glow
        </div>
        <div class="nav-links">
            <a href="index.php">Beranda</a>
            <a href="#katalog">Katalog</a>
            <a href="#testimoni">Testimoni</a>
            <a href="#kontak">Kontak</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'customer/dashboard.php' ?>" class="btn-daftar" style="padding:8px 18px; border-radius:20px;">Dashboard</a>
                <a href="auth/logout.php" class="btn-login" style="padding:8px 18px; border-radius:20px;">Keluar</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn-login" style="padding:8px 18px; border-radius:20px;">Masuk</a>
                <a href="auth/register.php" class="btn-daftar" style="padding:8px 18px; border-radius:20px;">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>
