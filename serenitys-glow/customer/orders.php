<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';
require_once '../includes/helpers.php';

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Pesanan Saya - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <div class="customer-wrapper">
        <div class="section-title" style="margin-bottom:24px;">
            <h2>Pesanan Saya</h2>
        </div>

        <?php if (count($orders) === 0): ?>
            <div class="empty-state">
                <p>Belum ada pesanan.</p>
                <a href="katalog.php" class="btn-add" style="display:inline-block; margin-top:16px;">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $o): ?>
            <a href="order_detail.php?id=<?= $o['id'] ?>" class="order-card">
                <div class="order-card-top">
                    <span class="order-id">Pesanan #<?= $o['id'] ?></span>
                    <span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
                </div>
                <div class="order-date"><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></div>
                <div class="order-total">Total: Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></div>
                <div style="margin-top:8px;">
                    <span class="payment-method-label"><?= htmlspecialchars(paymentMethodLabel($o['payment_method'])) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
