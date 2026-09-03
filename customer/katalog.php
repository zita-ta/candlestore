<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

$cartMsg = $_SESSION['cart_msg'] ?? '';
unset($_SESSION['cart_msg']);

// Urutan katalog (default: terbaru)
$sort = $_GET['sort'] ?? 'terbaru';
$sortOptions = [
    'terbaru'     => 'id DESC',
    'termurah'    => 'price ASC',
    'termahal'    => 'price DESC',
    'nama'        => 'name ASC',
];
$orderBy = $sortOptions[$sort] ?? $sortOptions['terbaru'];

$products = $conn->query("SELECT * FROM products ORDER BY $orderBy")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Katalog - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <div class="customer-wrapper">
        <div class="section-title" style="margin-bottom:24px;">
            <h2>Katalog Produk</h2>
            <p>Pilih aroma favoritmu, semua dibuat dengan bahan pilihan</p>
        </div>

        <?php if ($cartMsg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($cartMsg) ?></div>
        <?php endif; ?>

        <form method="GET" class="sort-bar">
            <label for="sort">Urutkan:</label>
            <select id="sort" name="sort" onchange="this.form.submit()">
                <option value="terbaru" <?= $sort === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                <option value="termurah" <?= $sort === 'termurah' ? 'selected' : '' ?>>Harga: Termurah</option>
                <option value="termahal" <?= $sort === 'termahal' ? 'selected' : '' ?>>Harga: Termahal</option>
                <option value="nama" <?= $sort === 'nama' ? 'selected' : '' ?>>Nama: A-Z</option>
            </select>
        </form>

        <div class="product-grid">
            <?php if (count($products) === 0): ?>
                <p style="text-align:center; color:#999;">Belum ada produk.</p>
            <?php endif; ?>

            <?php foreach ($products as $p):
                $imgSrc = $p['image'] ? '../assets/img/' . htmlspecialchars($p['image']) : 'https://placehold.co/300x180/f3c9d4/ffffff?text=' . urlencode($p['name']);
                $outOfStock = $p['stock'] <= 0;
            ?>
            <div class="product-card">
                <a href="product_detail.php?id=<?= $p['id'] ?>">
                    <div class="product-img" style="background-image: url('<?= $imgSrc ?>');"></div>
                </a>
                <div class="product-info">
                    <a href="product_detail.php?id=<?= $p['id'] ?>" class="product-name-link">
                        <h4><?= htmlspecialchars($p['name']) ?></h4>
                    </a>
                    <p class="desc"><?= htmlspecialchars($p['description']) ?></p>
                    <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                    <span class="stock-badge"><?= $outOfStock ? 'Habis' : 'Stok: ' . $p['stock'] ?></span>
                </div>

                <form action="cart_add.php" method="POST" class="add-cart-form">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <input type="number" name="quantity" class="qty-input" value="1" min="1" max="<?= $p['stock'] ?>" <?= $outOfStock ? 'disabled' : '' ?>>
                    <button type="submit" class="btn-cart" <?= $outOfStock ? 'disabled' : '' ?>>
                        <?= $outOfStock ? 'Stok Habis' : '+ Keranjang' ?>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
