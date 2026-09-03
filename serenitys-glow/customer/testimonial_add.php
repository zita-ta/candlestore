<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: orders.php');
    exit;
}

$orderId = $_POST['order_id'];
$rating = (int) $_POST['rating'];
$comment = trim($_POST['comment']);

// Pastikan order ini beneran punya customer yang login & statusnya 'selesai'
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = 'selesai'");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

if ($rating < 1 || $rating > 5 || empty($comment)) {
    $_SESSION['testi_error'] = 'Rating dan ulasan wajib diisi.';
    header("Location: order_detail.php?id=$orderId");
    exit;
}

// Cek belum pernah kasih ulasan buat pesanan ini (dijaga juga oleh UNIQUE KEY di database)
$stmt = $conn->prepare("SELECT id FROM testimonials WHERE order_id = ?");
$stmt->execute([$orderId]);
if ($stmt->fetch()) {
    $_SESSION['testi_error'] = 'Pesanan ini sudah pernah diberi ulasan.';
    header("Location: order_detail.php?id=$orderId");
    exit;
}

$stmt = $conn->prepare("INSERT INTO testimonials (user_id, order_id, customer_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $orderId, $_SESSION['name'], $rating, $comment]);

$_SESSION['testi_success'] = 'Terima kasih atas ulasannya!';
header("Location: order_detail.php?id=$orderId");
exit;
