<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

$id = $_POST['id'] ?? '';
$isEdit = !empty($id);

$name = trim($_POST['name']);
$description = trim($_POST['description']);
$price = $_POST['price'];
$stock = $_POST['stock'];
$isFeatured = isset($_POST['is_featured']) ? 1 : 0;

$redirectTarget = $isEdit ? "product_edit.php?id=$id" : "product_add.php";

// Validasi dasar
if (empty($name) || $price === '' || $stock === '') {
    $_SESSION['form_error'] = 'Nama, harga, dan stok wajib diisi.';
    $_SESSION['form_old'] = $_POST;
    header("Location: $redirectTarget");
    exit;
}

if ($price < 0 || $stock < 0) {
    $_SESSION['form_error'] = 'Harga dan stok tidak boleh negatif.';
    $_SESSION['form_old'] = $_POST;
    header("Location: $redirectTarget");
    exit;
}

// ---------- Handle upload gambar (kalau ada file baru di-upload) ----------
$imageName = null; // null berarti: tidak ganti gambar (khusus mode edit)

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        $_SESSION['form_error'] = 'Format gambar harus jpg, jpeg, png, atau webp.';
        $_SESSION['form_old'] = $_POST;
        header("Location: $redirectTarget");
        exit;
    }

    if ($_FILES['image']['size'] > 2 * 1024 * 1024) { // maks 2MB
        $_SESSION['form_error'] = 'Ukuran gambar maksimal 2MB.';
        $_SESSION['form_old'] = $_POST;
        header("Location: $redirectTarget");
        exit;
    }

    $imageName = 'product_' . time() . '_' . uniqid() . '.' . $ext;
    $uploadPath = '../assets/img/' . $imageName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        $_SESSION['form_error'] = 'Gagal upload gambar, coba lagi.';
        $_SESSION['form_old'] = $_POST;
        header("Location: $redirectTarget");
        exit;
    }
}

// ---------- Simpan ke database ----------
if ($isEdit) {
    if ($imageName) {
        // Hapus gambar lama biar nggak numpuk file, lalu update dengan gambar baru
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $oldImage = $stmt->fetchColumn();
        if ($oldImage && file_exists('../assets/img/' . $oldImage)) {
            unlink('../assets/img/' . $oldImage);
        }

        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, is_featured=?, image=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $stock, $isFeatured, $imageName, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, is_featured=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $stock, $isFeatured, $id]);
    }
    $_SESSION['flash_success'] = 'Produk berhasil diperbarui.';
} else {
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $stock, $imageName, $isFeatured]);
    $_SESSION['flash_success'] = 'Produk berhasil ditambahkan.';
}

header('Location: products.php');
exit;
