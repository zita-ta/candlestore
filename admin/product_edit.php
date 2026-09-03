<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: products.php');
    exit;
}

$error = $_SESSION['form_error'] ?? '';
$old = $_SESSION['form_old'] ?? null;
unset($_SESSION['form_error'], $_SESSION['form_old']);

// Kalau ada data 'old' (dari validasi gagal sebelumnya), pakai itu. Kalau nggak, ambil dari database.
if (!$old) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: products.php');
        exit;
    }
} else {
    $product = $old;
}

$pageTitle = "Edit Produk - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Edit Produk</h2>
        </div>

        <div class="admin-form-box admin-form-box-wide">
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($product['image'])): ?>
                <img class="current-img-preview" src="../assets/img/<?= htmlspecialchars($product['image']) ?>" alt="Gambar saat ini">
            <?php endif; ?>

            <form action="product_save.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                <div class="form-group">
                    <label for="name">Nama Produk</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Harga (Rp)</label>
                        <input type="number" id="price" name="price" min="0" step="500" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stok</label>
                        <input type="number" id="stock" name="stock" min="0" value="<?= htmlspecialchars($product['stock']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Ganti Gambar (kosongkan kalau tidak ingin ganti)</label>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
                    <label for="is_featured" style="margin-bottom:0;">Tampilkan di slideshow beranda (featured)</label>
                </div>

                <button type="submit" class="btn-submit">Update Produk</button>
                <a href="products.php" class="btn-cancel" style="display:block;">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>
