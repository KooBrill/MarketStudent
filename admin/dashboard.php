<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

// Stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE email_verified = 1")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingPayments = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'paid'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(service_fee), 0) FROM orders WHERE status = 'done'")->fetchColumn();

// Recent activity
$recentActivity = $pdo->query("SELECT al.*, u.name as user_name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 10")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin_nav.php';
?>

<div class="container admin-page">
  <h1>Admin Dashboard</h1>
  
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
      <div class="stat-number"><?= $totalUsers ?></div>
      <div class="stat-label">Total User</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
      <div class="stat-number"><?= $totalProducts ?></div>
      <div class="stat-label">Total Produk</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
      <div class="stat-number"><?= $totalOrders ?></div>
      <div class="stat-label">Total Pesanan</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
      <div class="stat-number highlight"><?= $pendingPayments ?></div>
      <div class="stat-label">Menunggu Verifikasi</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-vault"></i></div>
      <div class="stat-number"><?= formatRupiah($totalRevenue) ?></div>
      <div class="stat-label">Pendapatan (Fee)</div>
    </div>
  </div>
  
  <div class="admin-actions">
    <a href="<?= SITE_URL ?>/admin/verify_payment.php" class="btn btn-primary">
      <i class="fa-solid fa-certificate"></i> Verifikasi Pembayaran (<?= $pendingPayments ?>)
    </a>
    <a href="<?= SITE_URL ?>/admin/transactions.php" class="btn btn-outline">
      <i class="fa-solid fa-list-check"></i> Semua Transaksi
    </a>
    <a href="<?= SITE_URL ?>/admin/members.php" class="btn btn-outline">
      <i class="fa-solid fa-users-gear"></i> Kelola Member
    </a>
  </div>
  
  <div class="dashboard-section">
    <h2>Aktivitas Terbaru</h2>
    <?php if ($recentActivity): ?>
      <div class="activity-list">
        <?php foreach ($recentActivity as $log): ?>
          <div class="activity-item">
            <div>
              <strong><?= sanitize($log['user_name'] ?? 'System') ?></strong>
              <span class="badge"><?= sanitize($log['action']) ?></span>
            </div>
            <p><?= sanitize($log['details']) ?></p>
            <small class="text-muted"><?= timeAgo($log['created_at']) ?> — IP: <?= $log['ip_address'] ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted">Belum ada aktivitas</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>