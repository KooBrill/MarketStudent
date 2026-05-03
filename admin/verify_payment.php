<?php
$pageTitle = 'Verifikasi Pembayaran';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

// Approve / Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)$_POST['order_id'];
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE orders SET status = 'verified' WHERE id = ? AND status = 'paid'");
        $stmt->execute([$orderId]);
        logActivity($pdo, $_SESSION['user_id'], 'verify_payment', "Approved order #$orderId");
        setFlash('success', 'Pembayaran diverifikasi');
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE orders SET status = 'rejected' WHERE id = ? AND status = 'paid'");
        $stmt->execute([$orderId]);
        logActivity($pdo, $_SESSION['user_id'], 'reject_payment', "Rejected order #$orderId");
        setFlash('warning', 'Pembayaran ditolak');
    }
    redirect('/admin/verify_payment.php');
}

$pendingOrders = $pdo->query("SELECT o.*, u.name as buyer_name, u.phone as buyer_phone FROM orders o JOIN users u ON o.buyer_id = u.id WHERE o.status = 'paid' ORDER BY o.created_at ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin_nav.php';
?>

<div class="container admin-page">
  <h1>Verifikasi Pembayaran</h1>
  <p class="text-muted"><?= count($pendingOrders) ?> pembayaran menunggu verifikasi</p>
  
  <?php if ($pendingOrders): ?>
    <div class="verify-list">
      <?php foreach ($pendingOrders as $order): ?>
        <div class="verify-card">
          <div class="verify-header">
            <h3><?= $order['order_number'] ?></h3>
            <span class="badge badge-warning">Menunggu</span>
          </div>
          
          <div class="verify-grid">
            <div class="verify-info">
              <p><strong>Pembeli:</strong> <?= sanitize($order['buyer_name']) ?></p>
              <p><strong>HP:</strong> <?= sanitize($order['buyer_phone'] ?? '-') ?></p>
              <p><strong>Total:</strong> <?= formatRupiah($order['total']) ?></p>
              <p><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
              <?php if ($order['notes']): ?>
                <p><strong>Catatan:</strong> <?= sanitize($order['notes']) ?></p>
              <?php endif; ?>
            </div>
            
            <div class="verify-proof">
              <p><strong>Bukti Pembayaran:</strong></p>
              <?php 
                $proofFile = basename($order['payment_proof']);
                $proofUrl = SITE_URL . '/serve_payment.php?file=' . urlencode($proofFile);
              ?>
                <a href="<?= $proofUrl ?>" target="_blank">
                  <img src="<?= $proofUrl ?>" alt="Bukti bayar" class="proof-image">
                </a>
              <?php else: ?>
                <p class="text-muted">Belum diupload</p>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="verify-actions">
            <form method="POST" style="display:inline">
              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
              <input type="hidden" name="action" value="approve">
              <button class="btn btn-success" onclick="return confirm('Setujui pembayaran ini?')">Setujui</button>
            </form>
            <form method="POST" style="display:inline">
              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
              <input type="hidden" name="action" value="reject">
              <button class="btn btn-danger" onclick="return confirm('Tolak pembayaran ini?')">Tolak</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon">Semua Selesai</div>
      <h3>Tidak ada pembayaran yang menunggu</h3>
      <a href="<?= SITE_URL ?>/admin/dashboard.php" class="btn btn-outline">Kembali ke Dashboard</a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>