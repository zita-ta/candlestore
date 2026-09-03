<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: katalog.php');
    exit;
}

$productId = (int) $_POST['product_id'];
$quantity = max(1, (int) $_POST['quantity']);

// Pastikan produknya ada & stoknya cukup
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: katalog.php');
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$currentQty = $_SESSION['cart'][$productId] ?? 0;
$newQty = $currentQty + $quantity;

// Jangan sampai keranjang minta lebih dari stok yang ada
if ($newQty > $product['stock']) {
    $newQty = $product['stock'];
}

$_SESSION['cart'][$productId] = $newQty;
$_SESSION['cart_msg'] = htmlspecialchars($product['name']) . ' ditambahkan ke keranjang.';

// Kalau tombolnya ditekan dari halaman detail produk, balikin ke situ lagi
// (validasi ketat biar nggak bisa dipakai buat open redirect ke luar situs)
$redirectTo = $_POST['redirect_to'] ?? '';
if (preg_match('/^product_detail\.php\?id=\d+$/', $redirectTo)) {
    header('Location: ' . $redirectTo);
    exit;
}

header('Location: katalog.php');
exit;
