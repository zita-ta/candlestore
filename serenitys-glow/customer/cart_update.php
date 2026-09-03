<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$productId = (int) $_POST['product_id'];
$action = $_POST['action'] ?? 'update';

if ($action === 'remove') {
    unset($_SESSION['cart'][$productId]);
} else {
    $quantity = max(1, (int) $_POST['quantity']);

    // Validasi stok
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $stock = $stmt->fetchColumn();

    if ($stock !== false) {
        $_SESSION['cart'][$productId] = min($quantity, $stock);
    }
}

header('Location: cart.php');
exit;
