<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

// ---------- Kalau POST: eksekusi hapus beneran ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    if ($image && file_exists('../assets/img/' . $image)) {
        unlink('../assets/img/' . $image);
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash_success'] = 'Produk berhasil dihapus.';
    header('Location: products.php');
    exit;
}

// ---------- Kalau GET: tampilkan halaman konfirmasi dulu ----------
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: products.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = "Hapus Produk - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-form-box" style="text-align:center;">
            <h2 style="color:#c9524a; margin-bottom:16px;">Yakin mau hapus produk ini?</h2>
            <p style="margin-bottom:20px; color:#666;">
                Produk <strong><?= htmlspecialchars($product['name']) ?></strong> akan dihapus permanen dan tidak bisa dikembalikan.
            </p>

            <form action="product_delete.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
                <button type="submit" class="btn-submit" style="background-color:#c9524a;">Ya, Hapus Produk</button>
            </form>
            <a href="products.php" class="btn-cancel" style="display:block;">Batal, kembali</a>
        </div>
    </div>

</body>
</html>
