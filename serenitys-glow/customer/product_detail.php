<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: dashboard.php');
    exit;
}

// Produk lain buat rekomendasi (asal beda id, ambil 4)
$stmt = $conn->prepare("SELECT * FROM products WHERE id != ? ORDER BY RAND() LIMIT 4");
$stmt->execute([$id]);
$relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ulasan dari pesanan yang isinya ada produk ini
$stmt = $conn->prepare("
    SELECT DISTINCT r.*
    FROM reviews r
    JOIN order_items oi ON oi.order_id = r.order_id
    WHERE oi.product_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$id]);
$productReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartMsg = $_SESSION['cart_msg'] ?? '';
unset($_SESSION['cart_msg']);

$outOfStock = $product['stock'] <= 0;
$imgSrc = $product['image'] ? '../assets/img/' . htmlspecialchars($product['image']) : 'https://placehold.co/500x400/f3c9d4/ffffff?text=' . urlencode($product['name']);

$pageTitle = htmlspecialchars($product['name']) . " - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <div class="customer-wrapper">
        <a href="katalog.php" class="btn-cancel" style="display:inline-block; margin-bottom:16px;">← Kembali ke Katalog</a>

        <?php if ($cartMsg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($cartMsg) ?></div>
        <?php endif; ?>

        <div class="product-detail">
            <div class="product-detail-img" style="background-image: url('<?= $imgSrc ?>');"></div>

            <div class="product-detail-info">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <div class="price"><?= 'Rp ' . number_format($product['price'], 0, ',', '.') ?></div>
                <span class="stock-badge"><?= $outOfStock ? 'Habis' : 'Stok: ' . $product['stock'] ?></span>

                <p class="product-detail-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                <form action="cart_add.php" method="POST" class="add-cart-form" style="margin-top:20px;">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="redirect_to" value="product_detail.php?id=<?= $product['id'] ?>">
                    <input type="number" name="quantity" class="qty-input" value="1" min="1" max="<?= $product['stock'] ?>" <?= $outOfStock ? 'disabled' : '' ?>>
                    <button type="submit" class="btn-cart" style="max-width:220px;" <?= $outOfStock ? 'disabled' : '' ?>>
                        <?= $outOfStock ? 'Stok Habis' : '+ Tambah ke Keranjang' ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="section-title" style="margin:40px 0 20px;">
            <h2>Ulasan Produk</h2>
        </div>

        <?php if (count($productReviews) > 0): ?>
        <div class="admin-form-box" style="max-width:100%;">
            <?php foreach ($productReviews as $i => $rev): ?>
            <div style="<?= $i > 0 ? 'margin-top:18px; padding-top:18px; border-top:1px solid #f0ecec;' : '' ?>">
                <div class="star-rating-display">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                        <span class="star <?= $s <= $rev['rating'] ? 'filled' : '' ?>">★</span>
                    <?php endfor; ?>
                </div>
                <p style="font-weight:600; margin-top:8px;"><?= htmlspecialchars($rev['customer_name']) ?></p>
                <p style="color:#666; margin-top:4px;"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                <p style="font-size:12px; color:#999; margin-top:6px;">Dikirim pada <?= date('d M Y, H:i', strtotime($rev['created_at'])) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color:#999;">Belum ada ulasan untuk produk ini.</p>
        <?php endif; ?>

        <?php if (count($relatedProducts) > 0): ?>
        <div class="section-title" style="margin:40px 0 20px;">
            <h2>Produk Lainnya</h2>
        </div>
        <div class="product-grid">
            <?php foreach ($relatedProducts as $p):
                $relImg = $p['image'] ? '../assets/img/' . htmlspecialchars($p['image']) : 'https://placehold.co/300x180/f3c9d4/ffffff?text=' . urlencode($p['name']);
            ?>
            <a href="product_detail.php?id=<?= $p['id'] ?>" class="product-card">
                <div class="product-img" style="background-image: url('<?= $relImg ?>');"></div>
                <div class="product-info">
                    <h4><?= htmlspecialchars($p['name']) ?></h4>
                    <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>
