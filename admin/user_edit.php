<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: users.php');
    exit;
}

$error = $_SESSION['form_error'] ?? '';
$old = $_SESSION['form_old'] ?? null;
unset($_SESSION['form_error'], $_SESSION['form_old']);

// Kalau ada data 'old' (dari validasi gagal sebelumnya), pakai itu. Kalau nggak, ambil dari database.
if (!$old) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: users.php');
        exit;
    }
} else {
    $user = $old;
}

$pageTitle = "Edit User - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Edit User</h2>
        </div>

        <div class="admin-form-box admin-form-box-wide">
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="user_save.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" <?= $id == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                        <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <?php if ($id == $_SESSION['user_id']): ?>
                        <input type="hidden" name="role" value="<?= htmlspecialchars($user['role']) ?>">
                        <small style="color:#999;">Tidak bisa mengubah role akun sendiri.</small>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" id="password" name="password" minlength="6" placeholder="Kosongkan kalau tidak ingin ganti">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" minlength="6" placeholder="Kosongkan kalau tidak ingin ganti">
                    </div>
                </div>

                <button type="submit" class="btn-submit">Update User</button>
                <a href="users.php" class="btn-cancel" style="display:block;">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>
