<?php
$pageTitle = 'Pesanan Masuk';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $allowedTransitions = ['verified' => 'packed', 'packed' => 'shipped'];
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['new_status'];
    
    $stmt = $pdo->prepare("SELECT o.status FROM orders o JOIN order_items oi ON oi.order_id = o.id WHERE o.id = ? AND oi.seller_id = ? LIMIT 1");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch();
    
    if ($order && isset($allowedTransitions[$order['status']]) && $allowedTransitions[$order['status']] === $newStatus) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        logActivity($pdo, $_SESSION['user_id'], 'order_update', "Order #$orderId status -> $newStatus");
        setFlash('success', 'Status pesanan diperbarui!');
    }
    redirect('/member/orders.php');
}

$stmt = $pdo->prepare("SELECT o.*, u.name as buyer_name, u.phone as buyer_phone FROM orders o JOIN users u ON o.buyer_id = u.id JOIN order_items oi ON oi.order_id = o.id WHERE oi.seller_id = ? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <h1>Pesanan Masuk</h1>
  
  <?php if ($orders): ?>
    <div class="orders-list">
      <?php foreach ($orders as $order): ?>
        <div class="order-card">
          <div class="order-header">
            <span class="order-number"><?= $order['order_number'] ?></span>
            <?= statusBadge($order['status']) ?>
          </div>
          <p class="order-buyer"><?= sanitize($order['buyer_name']) ?> &mdash; <?= sanitize($order['buyer_phone'] ?? '-') ?></p>
          <p>Total: <strong><?= formatRupiah($order['total']) ?></strong></p>
          <p class="text-muted"><?= timeAgo($order['created_at']) ?></p>
          
          <?php if ($order['notes']): ?>
            <p class="order-notes"><em>Catatan: <?= sanitize($order['notes']) ?></em></p>
          <?php endif; ?>
          
          <div class="order-actions">
            <?php if ($order['status'] === 'verified'): ?>
              <form method="POST" style="display:inline">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <input type="hidden" name="new_status" value="packed">
                <button class="btn btn-sm btn-primary"><i class="fa-solid fa-box"></i> Kemas</button>
              </form>
            <?php elseif ($order['status'] === 'packed'): ?>
              <form method="POST" style="display:inline">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <input type="hidden" name="new_status" value="shipped">
                <button class="btn btn-sm btn-primary"><i class="fa-solid fa-truck"></i> Kirim</button>
              </form>
            <?php endif; ?>

            <?php if (in_array($order['status'], ['verified', 'packed', 'shipped']) && !empty($order['buyer_phone'])): ?>
              <a href="https://wa.me/<?= preg_replace('/^0/', '62', $order['buyer_phone']) ?>?text=<?= urlencode('Halo ' . $order['buyer_name'] . ', pesanan ' . $order['order_number'] . ' di MarketStudent. Mau koordinasi COD.') ?>" target="_blank" class="btn-wa-sm">
                <i class="fa-brands fa-whatsapp"></i> Chat Pembeli
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon">Kosong</div>
      <h3>Belum ada pesanan masuk</h3>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>