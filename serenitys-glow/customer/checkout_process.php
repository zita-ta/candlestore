<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header('Location: cart.php');
    exit;
}

$customerName = trim($_POST['customer_name']);
$customerEmail = trim($_POST['customer_email']);
$customerAddress = trim($_POST['customer_address']);
$paymentMethod = trim($_POST['payment_method'] ?? '');

if (empty($customerName) || empty($customerEmail) || empty($customerAddress) || empty($paymentMethod)) {
    $_SESSION['checkout_error'] = 'Semua field wajib diisi.';
    header('Location: checkout.php');
    exit;
}

$validPaymentMethods = ['transfer_bank', 'cod', 'e_wallet'];
if (!in_array($paymentMethod, $validPaymentMethods)) {
    $_SESSION['checkout_error'] = 'Metode pembayaran tidak valid.';
    header('Location: checkout.php');
    exit;
}

$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$productsInCart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Validasi ulang stok sebelum order dibuat (jaga-jaga stok berubah)
$grandTotal = 0;
$itemsToInsert = [];
foreach ($productsInCart as $p) {
    $qty = $_SESSION['cart'][$p['id']];
    if ($qty > $p['stock']) {
        $_SESSION['checkout_error'] = 'Stok "' . $p['name'] . '" tidak mencukupi. Silakan sesuaikan keranjang.';
        header('Location: cart.php');
        exit;
    }
    $subtotal = $qty * $p['price'];
    $grandTotal += $subtotal;
    $itemsToInsert[] = ['product' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
}

try {
    $conn->beginTransaction();

    // 1. Buat order utama
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, customer_email, customer_address, payment_method, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$_SESSION['user_id'], $customerName, $customerEmail, $customerAddress, $paymentMethod, $grandTotal]);
    $orderId = $conn->lastInsertId();

    // 2. Simpan tiap item pesanan & kurangi stok produk
    foreach ($itemsToInsert as $item) {
        $p = $item['product'];
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$orderId, $p['id'], $p['name'], $p['price'], $item['qty'], $item['subtotal']]);

        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['qty'], $p['id']]);
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['checkout_error'] = 'Terjadi kesalahan saat membuat pesanan. Coba lagi.';
    header('Location: checkout.php');
    exit;
}

// Kosongkan keranjang
$_SESSION['cart'] = [];

// Kirim invoice ke email customer
// NONAKTIF SEMENTARA - config/mail_config.php belum disetup dengan Gmail App Password
// Invoice tetap bisa dilihat customer di halaman order_success.php
// Kalau nanti mau diaktifkan lagi, hapus komentar /* */ di bawah ini
/*
require_once '../includes/send_invoice.php';
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$orderData = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$orderItemsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$emailResult = sendInvoiceEmail($orderData, $orderItemsData);
if ($emailResult !== true) {
    error_log("Gagal kirim invoice untuk order #$orderId: $emailResult");
}
*/

header('Location: order_success.php?order_id=' . $orderId);
exit;
