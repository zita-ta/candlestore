<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders.php');
    exit;
}

$orderId = $_POST['order_id'];
$status = $_POST['status'];
$validStatuses = ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

if (!in_array($status, $validStatuses)) {
    header("Location: order_detail.php?id=$orderId");
    exit;
}

// Ambil status lama dulu, biar tau apakah ini transisi BARU ke 'dibatalkan'
$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$currentOrder = $stmt->fetch(PDO::FETCH_ASSOC);

if ($currentOrder && $status === 'dibatalkan' && $currentOrder['status'] !== 'dibatalkan') {
    // Kembalikan stok produk yang sempat dikurangi saat checkout
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $it) {
        $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt->execute([$it['quantity'], $it['product_id']]);
    }
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->execute([$status, $orderId]);

$_SESSION['flash_success'] = 'Status pesanan berhasil diperbarui menjadi "' . ucfirst($status) . '".';
header("Location: order_detail.php?id=$orderId");
exit;
