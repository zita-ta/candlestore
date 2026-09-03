<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ambil detail produk yang ada di keranjang
$cartItems = [];
$grandTotal = 0;

if (count($_SESSION['cart']) > 0) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productsInCart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($productsInCart as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $qty * $p['price'];
        $grandTotal += $subtotal;
        $cartItems[] = [
            'product' => $p,
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}

$pageTitle = "Keranjang - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <div class="customer-wrapper">
        <div class="section-title" style="margin-bottom:24px;">
            <h2>Keranjang Belanja</h2>
        </div>

        <?php if (count($cartItems) === 0): ?>
            <div class="empty-state">
                <p>Keranjangmu masih kosong.</p>
                <a href="katalog.php" class="btn-add" style="display:inline-block; margin-top:16px;">Lihat Katalog</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item):
                        $p = $item['product'];
                        $imgSrc = $p['image'] ? '../assets/img/' . htmlspecialchars($p['image']) : 'https://placehold.co/50x50/a8c9a1/ffffff?text=%20';
                    ?>
                    <tr>
                        <td style="display:flex; align-items:center; gap:10px;">
                            <img class="thumb" src="<?= $imgSrc ?>" alt="">
                            <?= htmlspecialchars($p['name']) ?>
                        </td>
                        <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                        <td>
                            <form action="cart_update.php" method="POST" class="qty-form">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['qty'] ?>" min="1" max="<?= $p['stock'] ?>">
                                <button type="submit" name="action" value="update" class="btn-mini">Update</button>
                            </form>
                        </td>
                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                        <td>
                            <form action="cart_update.php" method="POST">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <button type="submit" name="action" value="remove" class="btn-mini btn-remove">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="total-line">Total: Rp <?= number_format($grandTotal, 0, ',', '.') ?></div>
                <a href="checkout.php" class="btn-checkout" style="display:inline-block; text-decoration:none;">Lanjut ke Checkout</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
