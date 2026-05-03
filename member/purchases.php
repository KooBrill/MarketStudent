<?php
$pageTitle = 'Riwayat Belanja';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

// Confirm received
if (isset($_GET['confirm'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = 'done' WHERE id = ? AND buyer_id = ? AND status = 'shipped'");
    $stmt->execute([$_GET['confirm'], $_SESSION['user_id']]);
    if ($stmt->rowCount()) {
        logActivity($pdo, $_SESSION['user_id'], 'confirm_order', "Confirmed order #{$_GET['confirm']}");
        setFlash('success', 'Pesanan dikonfirmasi diterima!');
    }
    redirect('/member/purchases.php');
}

$stmt = $pdo->prepare("
    SELECT o.*, 
           u.name as seller_name, u.phone as seller_phone,
           oi.product_id
    FROM orders o 
    JOIN order_items oi ON oi.order_id = o.id 
    JOIN users u ON oi.seller_id = u.id 
    WHERE o.buyer_id = ? 
    GROUP BY o.id 
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$purchases = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <h1>Riwayat Belanja</h1>
  
  <?php if ($purchases): ?>
    <div class="orders-list">
      <?php foreach ($purchases as $p): ?>
        <div class="order-card">
          <div class="order-header">
            <span class="order-number"><?= $p['order_number'] ?></span>
            <?= statusBadge($p['status']) ?>
          </div>
          <?php if (!empty($p['seller_name'])): ?>
            <p class="text-muted" style="font-size:.85rem">Penjual: <strong><?= sanitize($p['seller_name']) ?></strong></p>
          <?php endif; ?>
          <p>Total: <strong><?= formatRupiah($p['total']) ?></strong></p>
          <p class="text-muted"><?= timeAgo($p['created_at']) ?></p>
          
          <div class="order-actions">
            <?php if ($p['status'] === 'pending'): ?>
              <?php if (!empty($p['payment_deadline'])): ?>
                <p class="text-muted" style="font-size:.8rem"><i class="fa-solid fa-clock"></i> Batas bayar: <strong><?= date('d M Y, H:i', strtotime($p['payment_deadline'])) ?></strong></p>
              <?php endif; ?>
              <a href="<?= SITE_URL ?>/marketplace/payment_upload.php?order=<?= $p['id'] ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-credit-card"></i> Bayar Sekarang</a>
              <a href="<?= SITE_URL ?>/marketplace/checkout.php?cancel=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan pesanan ini?')"><i class="fa-solid fa-xmark"></i> Batalkan</a>
            <?php elseif ($p['status'] === 'shipped'): ?>
              <a href="<?= SITE_URL ?>/member/purchases.php?confirm=<?= $p['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi barang sudah diterima?')"><i class="fa-solid fa-check"></i> Barang Diterima</a>
            <?php elseif ($p['status'] === 'rejected'): ?>
              <a href="<?= SITE_URL ?>/marketplace/payment_upload.php?order=<?= $p['id'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-upload"></i> Upload Ulang Bukti</a>
            <?php endif; ?>

            <?php if (in_array($p['status'], ['verified', 'packed', 'shipped']) && !empty($p['seller_phone'])): ?>
              <a href="https://wa.me/<?= preg_replace('/^0/', '62', $p['seller_phone']) ?>?text=<?= urlencode('Halo, saya pembeli pesanan ' . $p['order_number'] . ' di MarketStudent. Mau koordinasi COD.') ?>" target="_blank" class="btn-wa-sm">
                <i class="fa-brands fa-whatsapp"></i> Chat Penjual
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="fa-solid fa-bag-shopping"></i></div>
      <h3>Belum ada riwayat belanja</h3>
      <a href="<?= SITE_URL ?>/marketplace/index.php" class="btn btn-primary">Ke Marketplace</a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>