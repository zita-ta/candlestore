<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';
require_once '../includes/helpers.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: orders.php');
    exit;
}

// Pastikan pesanan ini memang milik customer yang sedang login
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cek apakah pesanan ini sudah pernah direview
$stmt = $conn->prepare("SELECT * FROM reviews WHERE order_id = ?");
$stmt->execute([$id]);
$existingReview = $stmt->fetch(PDO::FETCH_ASSOC);

$detailError = $_SESSION['order_detail_error'] ?? '';
$detailSuccess = $_SESSION['order_detail_success'] ?? '';
$reviewError = $_SESSION['review_error'] ?? '';
$reviewSuccess = $_SESSION['review_success'] ?? '';
unset($_SESSION['order_detail_error'], $_SESSION['order_detail_success'], $_SESSION['review_error'], $_SESSION['review_success']);

$pageTitle = "Detail Pesanan #$id - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <div class="customer-wrapper">
        <div class="admin-header-row" style="margin-bottom:24px;">
            <h2>Pesanan #<?= $order['id'] ?></h2>
            <a href="orders.php" class="btn-cancel">← Kembali ke Pesanan Saya</a>
        </div>

        <?php if ($detailError): ?>
            <div class="alert alert-error"><?= htmlspecialchars($detailError) ?></div>
        <?php endif; ?>
        <?php if ($detailSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($detailSuccess) ?></div>
        <?php endif; ?>

        <div class="admin-form-box" style="max-width:100%; margin-bottom:24px;">
            <p><strong>Nama Penerima:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
            <p><strong>Alamat Pengiriman:</strong> <?= nl2br(htmlspecialchars($order['customer_address'])) ?></p>
            <p><strong>Metode Pembayaran:</strong> <span class="payment-method-label"><?= htmlspecialchars(paymentMethodLabel($order['payment_method'])) ?></span></p>
            <p><strong>Tanggal Pesan:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
            <p><strong>Status Pesanan:</strong> <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></p>

            <?php if ($order['payment_method'] === 'e_wallet' && $order['status'] === 'pending'): ?>
            <div class="qr-box" style="margin-top:18px;">
                <img src="../assets/img/qr-ewallet.png" alt="QR Code Pembayaran E-Wallet" class="qr-image">
                <p class="qr-caption">Scan QR ini pakai OVO / GoPay / DANA untuk membayar<br>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></p>
            </div>
            <?php endif; ?>

            <?php if ($order['status'] === 'pending'): ?>
            <form id="cancelOrderForm" action="order_cancel.php" method="POST" style="margin-top:18px;">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button type="button" class="btn-mini btn-remove" onclick="document.getElementById('cancelConfirmModal').style.display='flex';">Batalkan Pesanan</button>
            </form>

            <div id="cancelConfirmModal" class="confirm-modal-overlay">
                <div class="confirm-modal-box">
                    <h3>Batalkan Pesanan?</h3>
                    <p>Yakin ingin membatalkan pesanan ini?</p>
                    <div class="confirm-modal-actions">
                        <button type="button" class="btn-mini" onclick="document.getElementById('cancelConfirmModal').style.display='none';">Batal</button>
                        <button type="button" class="btn-mini btn-remove" onclick="document.getElementById('cancelOrderForm').submit();">Ya, Batalkan</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <table class="cart-table" style="margin-bottom:24px;">
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

        <?php if ($order['status'] === 'selesai'): ?>
        <div class="admin-form-box" style="max-width:100%;">
            <h3 style="margin-bottom:16px; color:var(--soft-green-dark);">Testimoni Kamu</h3>

            <?php if ($reviewError): ?>
                <div class="alert alert-error"><?= htmlspecialchars($reviewError) ?></div>
            <?php endif; ?>
            <?php if ($reviewSuccess): ?>
                <div class="alert alert-success"><?= htmlspecialchars($reviewSuccess) ?></div>
            <?php endif; ?>

            <?php if ($existingReview): ?>
                <div class="star-rating-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?= $i <= $existingReview['rating'] ? 'filled' : '' ?>">★</span>
                    <?php endfor; ?>
                </div>
                <p style="margin-top:10px; color:#666;"><?= nl2br(htmlspecialchars($existingReview['comment'])) ?></p>
                <p style="margin-top:10px; font-size:12px; color:#999;">Dikirim pada <?= date('d M Y, H:i', strtotime($existingReview['created_at'])) ?></p>
            <?php else: ?>
                <p style="margin-bottom:16px; color:#888; font-size:14px;">Pesananmu sudah selesai. Yuk bagikan pengalaman belanjamu!</p>
                <form action="review_submit.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <div class="form-group">
                        <label>Rating</label>
                        <div class="star-rating-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i == 5 ? 'checked' : '' ?>>
                                <label for="star<?= $i ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Ulasan</label>
                        <textarea id="comment" name="comment" rows="3" placeholder="Ceritain pengalaman belanjamu di sini..." required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Kirim Testimoni</button>
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>
