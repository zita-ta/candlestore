<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

$deleted = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$products = $conn->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Kelola Produk - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Kelola Produk</h2>
            <a href="product_add.php" class="btn-add">+ Tambah Produk</a>
        </div>

        <?php if ($deleted): ?>
            <div class="alert alert-success"><?= htmlspecialchars($deleted) ?></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Featured</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) === 0): ?>
                    <tr><td colspan="6" style="text-align:center; color:#999;">Belum ada produk.</td></tr>
                <?php endif; ?>

                <?php foreach ($products as $p):
                    $imgSrc = $p['image'] ? '../assets/img/' . htmlspecialchars($p['image']) : 'https://placehold.co/50x50/a8c9a1/ffffff?text=%20';
                ?>
                <tr>
                    <td><img class="thumb" src="<?= $imgSrc ?>" alt=""></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                    <td><?= $p['stock'] ?></td>
                    <td><?= $p['is_featured'] ? '<span class="badge-featured">Featured</span>' : '-' ?></td>
                    <td class="action-links">
                        <a href="product_edit.php?id=<?= $p['id'] ?>" class="edit">Edit</a>
                        <a href="product_delete.php?id=<?= $p['id'] ?>" class="delete">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
