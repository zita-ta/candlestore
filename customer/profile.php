<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$profileError = $_SESSION['profile_error'] ?? '';
$profileSuccess = $_SESSION['profile_success'] ?? '';
$passwordError = $_SESSION['password_error'] ?? '';
$passwordSuccess = $_SESSION['password_success'] ?? '';
unset($_SESSION['profile_error'], $_SESSION['profile_success'], $_SESSION['password_error'], $_SESSION['password_success']);

$pageTitle = "Profil Saya - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <div class="customer-wrapper">
        <div class="section-title" style="margin-bottom:24px;">
            <h2>Profil Saya</h2>
        </div>

        <?php if ($profileError): ?>
            <div class="alert alert-error"><?= htmlspecialchars($profileError) ?></div>
        <?php endif; ?>
        <?php if ($profileSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($profileSuccess) ?></div>
        <?php endif; ?>
        <?php if ($passwordError): ?>
            <div class="alert alert-error"><?= htmlspecialchars($passwordError) ?></div>
        <?php endif; ?>
        <?php if ($passwordSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($passwordSuccess) ?></div>
        <?php endif; ?>

        <div class="admin-form-box">
            <h3 style="margin-bottom:16px; color:var(--soft-green-dark);">Data Diri</h3>
            <form action="profile_update.php" method="POST">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
            </form>
        </div>

        <div class="admin-form-box" style="margin-top:24px;">
            <h3 style="margin-bottom:16px; color:var(--soft-green-dark);">Ganti Password</h3>
            <form action="password_update.php" method="POST">
                <div class="form-group">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                <button type="submit" class="btn-submit">Ganti Password</button>
            </form>
        </div>
    </div>

</body>
</html>
