<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

$totalProduk = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$stokMenipis = $conn->query("SELECT COUNT(*) FROM products WHERE stock <= 5")->fetchColumn();
$totalCustomer = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalPenjualan = $conn->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'selesai'")->fetchColumn();

$pageTitle = "Dashboard Admin - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Selamat datang, <?= htmlspecialchars($_SESSION['name']) ?></h2>
        </div>

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-number">Rp<?= number_format($totalPenjualan, 0, ',', '.') ?></div>
                <div class="stat-label">Total Penjualan</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalProduk ?></div>
                <div class="stat-label">Total Produk</div>
            </div>
            <div class="stat-card pink">
                <div class="stat-number"><?= $stokMenipis ?></div>
                <div class="stat-label">Stok Menipis (≤ 5)</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalCustomer ?></div>
                <div class="stat-label">Total Customer Terdaftar</div>
            </div>
        </div>

        <a href="products.php" class="btn-add">Kelola Produk →</a>
        <a href="orders.php" class="btn-add" style="background-color: var(--soft-green-dark); margin-left:10px;">Kelola Pesanan →</a>
    </div>

</body>
</html>
