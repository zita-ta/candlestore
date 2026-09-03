<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';
require_once '../includes/helpers.php';

// Filter status (opsional, lewat dropdown yang submit form GET biar tanpa JS)
$filterStatus = $_GET['status'] ?? '';
$validStatuses = ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

if ($filterStatus && in_array($filterStatus, $validStatuses)) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$filterStatus]);
} else {
    $stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Kelola Pesanan - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Semua Pesanan</h2>
        </div>

        <form method="GET" style="margin-bottom:20px; display:flex; gap:10px; align-items:flex-end;">
            <div class="form-group" style="max-width:250px; margin-bottom:0;">
                <label for="status">Filter Status</label>
                <select id="status" name="status" style="width:100%; padding:10px; border-radius:8px; border:1.5px solid #e0d8d8;">
                    <option value="">Semua Status</option>
                    <?php foreach ($validStatuses as $s): ?>
                        <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-add" style="height:42px;">Filter</button>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pemesan</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) === 0): ?>
                    <tr><td colspan="7" style="text-align:center; color:#999;">Belum ada pesanan.</td></tr>
                <?php endif; ?>

                <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['customer_name']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></td>
                    <td>Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></td>
                    <td><span class="payment-method-label"><?= htmlspecialchars(paymentMethodLabel($o['payment_method'])) ?></span></td>
                    <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                    <td class="action-links">
                        <a href="order_detail.php?id=<?= $o['id'] ?>" class="edit">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
