<?php
$pageTitle = 'Pembayaran';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$orderId = (int)($_GET['order'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND buyer_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('warning', 'Pesanan tidak ditemukan');
    redirect('/member/purchases.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_proof'])) {
    if ($_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($_FILES['payment_proof'], 'uploads/payments/');
        if ($upload['success']) {
            $stmt = $pdo->prepare("UPDATE orders SET payment_proof = ?, status = 'paid' WHERE id = ?");
            $stmt->execute([$upload['path'], $orderId]);
            logActivity($pdo, $_SESSION['user_id'], 'payment_upload', "Bukti bayar order #{$order['order_number']}");
            setFlash('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
            redirect('/member/purchases.php');
        } else {
            setFlash('danger', $upload['error']);
        }
    } else {
        setFlash('danger', 'Pilih file bukti pembayaran');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <h1>Pembayaran</h1>
  
  <div class="payment-grid">
    <div class="payment-info">
      <h3>Detail Pesanan</h3>
      <div class="payment-detail">
        <p><strong>No. Pesanan:</strong> <?= $order['order_number'] ?></p>
        <p><strong>Total Bayar:</strong> <span class="text-primary" style="font-size:1.3rem;font-weight:700"><?= formatRupiah($order['total']) ?></span></p>
        <p><strong>Status:</strong> <?= statusBadge($order['status']) ?></p>
      </div>
      
      <?php if ($order['status'] === 'rejected'): ?>
        <div class="alert alert-warning">
          Pembayaran sebelumnya ditolak. Silakan upload ulang bukti pembayaran yang valid.
        </div>
      <?php endif; ?>
      
      <?php if (in_array($order['status'], ['pending', 'rejected'])): ?>
        <form method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="payment_proof">Upload Bukti Pembayaran</label>
            <input type="file" id="payment_proof" name="payment_proof" accept="image/*" required>
            <span class="form-hint">Format: JPG, PNG, WebP. Maksimal 5MB</span>
          </div>
          <button type="submit" class="btn btn-primary btn-full">Upload Bukti Bayar</button>
        </form>
      <?php endif; ?>
    </div>
    
    <div class="payment-qris">
      <h3>Scan QRIS</h3>
      <div class="qris-container">
        <div class="qris-icon">QRIS</div>
        <img src="<?= SITE_URL ?>/uploads/payments/qris.png" alt="QRIS" onerror="this.style.display='none'">
        <p>Scan kode QRIS di atas untuk membayar.<br>Bisa pakai Dana, OVO, GoPay, ShopeePay, atau m-banking.</p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>