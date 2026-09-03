<?php
require_once '../includes/auth_check.php';
requireRole('admin');

$error = $_SESSION['form_error'] ?? '';
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_error'], $_SESSION['form_old']);

$pageTitle = "Tambah User - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Tambah User Baru</h2>
        </div>

        <div class="admin-form-box admin-form-box-wide">
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="user_save.php" method="POST">
                <input type="hidden" name="id" value="">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="customer" <?= ($old['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Simpan User</button>
                <a href="users.php" class="btn-cancel" style="display:block;">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>
