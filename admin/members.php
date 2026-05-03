<?php
$pageTitle = 'Kelola Member';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

// Ban/unban
if (isset($_GET['ban'])) {
    $stmt = $pdo->prepare("UPDATE users SET is_banned = NOT is_banned WHERE id = ? AND role != 'admin'");
    $stmt->execute([$_GET['ban']]);
    logActivity($pdo, $_SESSION['user_id'], 'admin_ban', "Toggled ban user #{$_GET['ban']}");
    setFlash('success', 'Status member diperbarui');
    redirect('/admin/members.php');
}

$members = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin_nav.php';
?>

<div class="container admin-page">
  <h1>Kelola Member</h1>
  
  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Email</th>
          <th>HP</th>
          <th>Role</th>
          <th>Status</th>
          <th>Terdaftar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($members as $m): ?>
          <tr>
            <td><?= $m['id'] ?></td>
            <td><?= sanitize($m['name']) ?></td>
            <td><?= sanitize($m['email']) ?></td>
            <td><?= sanitize($m['phone'] ?? '-') ?></td>
            <td><span class="badge badge-<?= $m['role'] === 'admin' ? 'info' : 'secondary' ?>"><?= $m['role'] ?></span></td>
            <td>
              <?php if ($m['is_banned']): ?>
                <span class="badge badge-danger">Banned</span>
              <?php elseif (!$m['email_verified']): ?>
                <span class="badge badge-warning">Belum Verifikasi</span>
              <?php else: ?>
                <span class="badge badge-success">Aktif</span>
              <?php endif; ?>
            </td>
            <td><?= date('d/m/Y', strtotime($m['created_at'])) ?></td>
            <td>
              <?php if ($m['role'] !== 'admin'): ?>
                <a href="<?= SITE_URL ?>/admin/members.php?ban=<?= $m['id'] ?>" class="btn btn-sm <?= $m['is_banned'] ? 'btn-success' : 'btn-danger' ?>" onclick="return confirm('<?= $m['is_banned'] ? 'Unban' : 'Ban' ?> user ini?')">
                  <?= $m['is_banned'] ? 'Unban' : 'Ban' ?>
                </a>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>