<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders.php');
    exit;
}

$orderId = (int) $_POST['order_id'];

// Pastikan pesanan ini milik customer yang login & masih 'pending'
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

if ($order['status'] !== 'pending') {
    $_SESSION['order_detail_error'] = 'Pesanan ini sudah diproses dan tidak bisa dibatalkan lagi.';
    header('Location: order_detail.php?id=' . $orderId);
    exit;
}

try {
    $conn->beginTransaction();

    // Kembalikan stok produk yang sempat dikurangi saat checkout
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $it) {
        $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt->execute([$it['quantity'], $it['product_id']]);
    }

    $stmt = $conn->prepare("UPDATE orders SET status = 'dibatalkan' WHERE id = ?");
    $stmt->execute([$orderId]);

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['order_detail_error'] = 'Terjadi kesalahan saat membatalkan pesanan. Coba lagi.';
    header('Location: order_detail.php?id=' . $orderId);
    exit;
}

$_SESSION['order_detail_success'] = 'Pesanan berhasil dibatalkan.';
header('Location: order_detail.php?id=' . $orderId);
exit;
