<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';
require_once '../includes/helpers.php';

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Pesanan Berhasil - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <div class="customer-wrapper">
        <div class="admin-form-box" style="max-width:600px; margin:0 auto; text-align:center;">
            <h2 style="color: var(--soft-green-dark); margin-bottom:8px;">Pesanan Berhasil Dibuat!</h2>
            <p style="color:#888; margin-bottom:20px;">Nomor Pesanan: <strong>#<?= $order['id'] ?></strong></p>
            <p style="margin-bottom:20px;">
                <span class="payment-method-label"><?= htmlspecialchars(paymentMethodLabel($order['payment_method'])) ?></span>
            </p>

            <table class="cart-table" style="text-align:left;">
                <thead><tr><th>Produk</th><th>Qty</th><th>Subtotal</th></tr></thead>
                <tbody>
                    <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?= htmlspecialchars($it['product_name']) ?></td>
                        <td><?= $it['quantity'] ?></td>
                        <td>Rp <?= number_format($it['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p style="font-size:20px; font-weight:bold; color:var(--soft-pink-dark); margin:16px 0;">
                Total: Rp <?= number_format($order['total_amount'], 0, ',', '.') ?>
            </p>

            <p style="font-size:14px; color:var(--soft-green-dark); background:#f3f8f1; padding:10px 16px; border-radius:10px; margin-bottom:20px;">
                Invoice akan dikirim ke email <strong><?= htmlspecialchars($order['customer_email']) ?></strong>
            </p>

            <p style="font-size:13px; color:#999; margin-bottom:20px;">
                Simpan nomor pesanan ini untuk referensi kamu.
            </p>

            <a href="katalog.php" class="btn-submit" style="display:inline-block; text-decoration:none; width:auto; padding:12px 30px;">Kembali ke Katalog</a>
        </div>
    </div>

</body>
</html>
