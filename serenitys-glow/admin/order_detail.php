<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';
require_once '../includes/helpers.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: orders.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM reviews WHERE order_id = ?");
$stmt->execute([$id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

$validStatuses = ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
$updated = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$pageTitle = "Detail Pesanan #$id - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Pesanan #<?= $order['id'] ?></h2>
            <a href="orders.php" class="btn-cancel">← Kembali ke daftar pesanan</a>
        </div>

        <?php if ($updated): ?>
            <div class="alert alert-success"><?= htmlspecialchars($updated) ?></div>
        <?php endif; ?>

        <div class="admin-form-box" style="max-width:100%; margin-bottom:24px;">
            <p><strong>Nama Pemesan:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
            <p><strong>Alamat:</strong> <?= nl2br(htmlspecialchars($order['customer_address'])) ?></p>
            <p><strong>Metode Pembayaran:</strong> <span class="payment-method-label"><?= htmlspecialchars(paymentMethodLabel($order['payment_method'])) ?></span></p>
            <p><strong>Tanggal Pesan:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
            <p><strong>Status Saat Ini:</strong> <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></p>
        </div>

        <table class="admin-table" style="margin-bottom:24px;">
            <thead>
                <tr><th>Produk</th><th>Harga Satuan</th><th>Qty</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                <tr>
                    <td><?= htmlspecialchars($it['product_name']) ?></td>
                    <td>Rp <?= number_format($it['price'], 0, ',', '.') ?></td>
                    <td><?= $it['quantity'] ?></td>
                    <td>Rp <?= number_format($it['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:bold;">Total</td>
                    <td style="font-weight:bold;">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <?php if ($review): ?>
        <div class="admin-form-box" style="max-width:100%; margin-bottom:24px;">
            <h3 style="margin-bottom:16px; color:var(--soft-green-dark);">Testimoni Customer</h3>
            <div class="star-rating-display">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                <?php endfor; ?>
            </div>
            <p style="margin-top:10px; color:#666;"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
            <p style="margin-top:10px; font-size:12px; color:#999;">Dikirim pada <?= date('d M Y, H:i', strtotime($review['created_at'])) ?></p>
        </div>
        <?php endif; ?>

        <div class="admin-form-box admin-form-box-wide">
            <form action="order_update_status.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <div class="form-group">
                    <select name="status" class="status-select">
                        <?php foreach ($validStatuses as $s): ?>
                            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Simpan Status</button>
            </form>
        </div>
    </div>

</body>
</html>
