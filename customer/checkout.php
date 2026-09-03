<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header('Location: cart.php');
    exit;
}

$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$productsInCart = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartItems = [];
$grandTotal = 0;
foreach ($productsInCart as $p) {
    $qty = $_SESSION['cart'][$p['id']];
    $subtotal = $qty * $p['price'];
    $grandTotal += $subtotal;
    $cartItems[] = ['product' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
}

$error = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);

$pageTitle = "Checkout - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <div class="customer-wrapper">
        <div class="section-title" style="margin-bottom:24px;">
            <h2>Checkout</h2>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <table class="cart-table">
            <thead>
                <tr><th>Produk</th><th>Jumlah</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product']['name']) ?></td>
                    <td><?= $item['qty'] ?></td>
                    <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="admin-form-box" style="max-width:100%; margin-bottom:24px;">
            <form action="checkout_process.php" method="POST">
                <div class="form-group">
                    <label for="customer_name">Nama Penerima</label>
                    <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($_SESSION['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="customer_email">Email (invoice akan dikirim ke sini)</label>
                    <input type="email" id="customer_email" name="customer_email" value="<?= htmlspecialchars($_SESSION['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="customer_address">Alamat Pengiriman</label>
                    <textarea id="customer_address" name="customer_address" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="transfer_bank" checked onclick="togglePaymentDetail()">
                            <span>
                                <strong>Transfer Bank</strong>
                                <small>BCA 1234567890 a.n. Serenity's Glow</small>
                            </span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="e_wallet" onclick="togglePaymentDetail()">
                            <span>
                                <strong>E-Wallet</strong>
                                <small>OVO / GoPay / DANA - scan QR di bawah</small>
                            </span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" onclick="togglePaymentDetail()">
                            <span>
                                <strong>COD (Bayar di Tempat)</strong>
                                <small>Bayar tunai saat pesanan diantar</small>
                            </span>
                        </label>
                    </div>

                    <div id="qr-ewallet-box" class="qr-box" style="display:none;">
                        <img src="../assets/img/qr-ewallet.png" alt="QR Code Pembayaran E-Wallet" class="qr-image">
                        <p class="qr-caption">Scan QR ini pakai OVO / GoPay / DANA untuk membayar<br>Rp <?= number_format($grandTotal, 0, ',', '.') ?></p>
                    </div>
                </div>

                <div class="cart-summary" style="padding:0; box-shadow:none; margin-bottom:20px;">
                    <div class="total-line">Total: Rp <?= number_format($grandTotal, 0, ',', '.') ?></div>
                </div>

                <button type="submit" class="btn-submit">Buat Pesanan</button>
            </form>
        </div>
    </div>
    <script>
        function togglePaymentDetail() {
            var selected = document.querySelector('input[name="payment_method"]:checked').value;
            var qrBox = document.getElementById('qr-ewallet-box');
            qrBox.style.display = (selected === 'e_wallet') ? 'block' : 'none';
        }
    </script>
</body>
</html>
