<?php
// Dipanggil setelah requireRole('customer') dan session sudah aktif
$pageTitle = $pageTitle ?? "Serenity's Glow";
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartCount += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/landing.css">
    <link rel="stylesheet" href="../assets/css/customer.css">
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
            <a href="dashboard.php">Dashboard</a>
            <a href="katalog.php">Katalog</a>
            <a href="orders.php">Pesanan Saya</a>
            <a href="cart.php">Keranjang <?= $cartCount > 0 ? "($cartCount)" : '' ?></a>
            <span>Halo, <a href="profile.php" style="font-weight:600;"><?= htmlspecialchars($_SESSION['name']) ?></a></span>
            <a href="../auth/logout.php" class="btn-login" style="padding:8px 18px; border-radius:20px;">Keluar</a>
        </div>
    </nav>
