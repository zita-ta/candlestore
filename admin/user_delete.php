<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

// ---------- Kalau POST: eksekusi hapus beneran ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if ($id == $_SESSION['user_id']) {
        $_SESSION['flash_success'] = 'Tidak bisa menghapus akun sendiri.';
        header('Location: users.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash_success'] = 'User ini masih punya riwayat pesanan, tidak bisa dihapus.';
        header('Location: users.php');
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash_success'] = 'User berhasil dihapus.';
    header('Location: users.php');
    exit;
}

// ---------- Kalau GET: tampilkan halaman konfirmasi dulu ----------
$id = $_GET['id'] ?? null;
if (!$id || $id == $_SESSION['user_id']) {
    header('Location: users.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: users.php');
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$id]);
$orderCount = $stmt->fetchColumn();

$pageTitle = "Hapus User - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-form-box" style="text-align:center;">
            <h2 style="color:#c9524a; margin-bottom:16px;">Yakin mau hapus user ini?</h2>
            <p style="margin-bottom:20px; color:#666;">
                User <strong><?= htmlspecialchars($user['name']) ?></strong> (<?= htmlspecialchars($user['email']) ?>) akan dihapus permanen dan tidak bisa dikembalikan.
            </p>

            <?php if ($orderCount > 0): ?>
                <div class="alert alert-error" style="margin-bottom:20px;">
                    User ini masih punya <?= $orderCount ?> riwayat pesanan, jadi tidak bisa dihapus. Hapus/pindahkan dulu riwayat pesanannya kalau memang ingin menghapus user ini.
                </div>
                <a href="users.php" class="btn-cancel" style="display:block;">← Kembali</a>
            <?php else: ?>
                <form action="user_delete.php" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                    <button type="submit" class="btn-submit" style="background-color:#c9524a;">Ya, Hapus User</button>
                </form>
                <a href="users.php" class="btn-cancel" style="display:block;">Batal, kembali</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
