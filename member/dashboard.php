<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$user = getUser($pdo, $_SESSION['user_id']);

// Stats
$totalProducts = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ?");
$totalProducts->execute([$_SESSION['user_id']]);
$totalProducts = $totalProducts->fetchColumn();

$totalSales = $pdo->prepare("SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.seller_id = ? AND o.status = 'done'");
$totalSales->execute([$_SESSION['user_id']]);
$totalSales = $totalSales->fetchColumn();

$revenue = $pdo->prepare("SELECT COALESCE(SUM(oi.price * oi.quantity), 0) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.seller_id = ? AND o.status = 'done'");
$revenue->execute([$_SESSION['user_id']]);
$revenue = $revenue->fetchColumn();

$totalPurchases = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id = ?");
$totalPurchases->execute([$_SESSION['user_id']]);
$totalPurchases = $totalPurchases->fetchColumn();

// Recent incoming orders
$recentOrders = $pdo->prepare("SELECT o.*, u.name as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.id JOIN order_items oi ON oi.order_id = o.id WHERE oi.seller_id = ? GROUP BY o.id ORDER BY o.created_at DESC LIMIT 5");
$recentOrders->execute([$_SESSION['user_id']]);
$recentOrders = $recentOrders->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container dashboard-page">
  <div class="dashboard-header">
    <h1>Halo, <?= sanitize($user['name']) ?>!</h1>
    <p>Selamat datang di dashboard MarketStudent</p>
  </div>
  
  <?php if (!$user['profile_completed']): ?>
    <div class="alert alert-warning">
      <i class="fa-solid fa-camera"></i> <strong>Profil belum lengkap!</strong> Upload <strong>foto profil asli (foto wajah)</strong> dan lengkapi data diri untuk bisa jual & beli. <a href="<?= SITE_URL ?>/member/profile.php"><strong>Lengkapi profil sekarang →</strong></a>
    </div>
  <?php endif; ?>
  
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
      <div class="stat-number"><?= $totalProducts ?></div>
      <div class="stat-label">Produk Saya</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
      <div class="stat-number"><?= $totalSales ?></div>
      <div class="stat-label">Penjualan</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
      <div class="stat-number"><?= formatRupiah($revenue) ?></div>
      <div class="stat-label">Pendapatan</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
      <div class="stat-number"><?= $totalPurchases ?></div>
      <div class="stat-label">Pembelian</div>
    </div>
  </div>
  
  <div class="dashboard-section">
    <h2>Pesanan Masuk Terbaru</h2>
    <?php if ($recentOrders): ?>
      <div class="orders-list">
        <?php foreach ($recentOrders as $order): ?>
          <div class="order-card">
            <div class="order-header">
              <span class="order-number"><i class="fa-solid fa-hashtag"></i> <?= $order['order_number'] ?></span>
              <?= statusBadge($order['status']) ?>
            </div>
            <p><i class="fa-solid fa-user"></i> Pembeli: <?= sanitize($order['buyer_name']) ?></p>
            <p><i class="fa-solid fa-money-bill-wave"></i> Total: <?= formatRupiah($order['total']) ?></p>
            <p class="text-muted"><i class="fa-solid fa-clock"></i> <?= timeAgo($order['created_at']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted"><i class="fa-solid fa-circle-info"></i> Belum ada pesanan masuk</p>
    <?php endif; ?>
  </div>
  
  <div class="dashboard-section">
    <h2>Aksi Cepat</h2>
    <div class="quick-actions">
      <a href="<?= SITE_URL ?>/member/product_form.php" class="action-card">
        <span class="action-icon"><i class="fa-solid fa-plus"></i></span>
        <span>Tambah Produk</span>
      </a>
      <a href="<?= SITE_URL ?>/member/orders.php" class="action-card">
        <span class="action-icon"><i class="fa-solid fa-receipt"></i></span>
        <span>Pesanan Masuk</span>
      </a>
      <a href="<?= SITE_URL ?>/member/purchases.php" class="action-card">
        <span class="action-icon"><i class="fa-solid fa-bag-shopping"></i></span>
        <span>Riwayat Belanja</span>
      </a>
      <a href="<?= SITE_URL ?>/member/profile.php" class="action-card">
        <span class="action-icon"><i class="fa-solid fa-user-pen"></i></span>
        <span>Edit Profil</span>
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>