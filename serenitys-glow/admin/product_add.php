<?php
require_once '../includes/auth_check.php';
requireRole('admin');

$error = $_SESSION['form_error'] ?? '';
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_error'], $_SESSION['form_old']);

$pageTitle = "Tambah Produk - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Tambah Produk Baru</h2>
        </div>

        <div class="admin-form-box admin-form-box-wide">
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="product_save.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="">

                <div class="form-group">
                    <label for="name">Nama Produk</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Harga (Rp)</label>
                        <input type="number" id="price" name="price" min="0" step="500" value="<?= htmlspecialchars($old['price'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stok</label>
                        <input type="number" id="stock" name="stock" min="0" value="<?= htmlspecialchars($old['stock'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Gambar Produk</label>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1">
                    <label for="is_featured" style="margin-bottom:0;">Tampilkan di slideshow beranda (featured)</label>
                </div>

                <button type="submit" class="btn-submit">Simpan Produk</button>
                <a href="products.php" class="btn-cancel" style="display:block;">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>
