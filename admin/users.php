<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../config/db.php';

$flash = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$users = $conn->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Kelola User - Serenity's Glow";
require_once '../includes/admin_nav.php';
?>

    <div class="admin-wrapper">
        <div class="admin-header-row">
            <h2>Kelola User</h2>
            <a href="user_add.php" class="btn-add">+ Tambah User</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) === 0): ?>
                    <tr><td colspan="4" style="text-align:center; color:#999;">Belum ada user.</td></tr>
                <?php endif; ?>

                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['role'] === 'admin' ? '<span class="badge-featured">Admin</span>' : 'Customer' ?></td>
                    <td class="action-links">
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <a href="user_delete.php?id=<?= $u['id'] ?>" class="delete">Hapus</a>
                        <?php else: ?>
                            <span style="color:#ccc;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
