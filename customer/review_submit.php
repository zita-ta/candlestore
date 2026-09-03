<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders.php');
    exit;
}

$orderId = (int) $_POST['order_id'];
$rating = (int) ($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

// Pastikan pesanan ini milik customer yang login & statusnya 'selesai'
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order || $order['status'] !== 'selesai') {
    header('Location: orders.php');
    exit;
}

// Cegah review dobel untuk pesanan yang sama
$stmt = $conn->prepare("SELECT id FROM reviews WHERE order_id = ?");
$stmt->execute([$orderId]);
if ($stmt->fetch()) {
    header('Location: order_detail.php?id=' . $orderId);
    exit;
}

if ($rating < 1 || $rating > 5 || empty($comment)) {
    $_SESSION['review_error'] = 'Rating dan ulasan wajib diisi.';
    header('Location: order_detail.php?id=' . $orderId);
    exit;
}

$stmt = $conn->prepare("INSERT INTO reviews (order_id, user_id, customer_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$orderId, $_SESSION['user_id'], $_SESSION['name'], $rating, $comment]);

$_SESSION['review_success'] = 'Terima kasih atas testimonimu!';
header('Location: order_detail.php?id=' . $orderId);
exit;
