<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
requireProfileComplete();

// Cancel order
if (isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND buyer_id = ? AND status = 'pending'");
    $stmt->execute([$_GET['cancel'], $_SESSION['user_id']]);
    setFlash('success', 'Pesanan dibatalkan');
    redirect('/member/purchases.php');
}

// Auto-cancel expired pending orders
$pdo->exec("UPDATE orders SET status = 'cancelled' WHERE status = 'pending' AND payment_deadline IS NOT NULL AND payment_deadline < NOW()");

$user = getUser($pdo, $_SESSION['user_id']);

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.title, p.price, p.stock, p.seller_id, p.image, u.name as seller_name FROM carts c JOIN products p ON c.product_id = p.id JOIN users u ON p.seller_id = u.id WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

if (empty($items)) {
    setFlash('warning', 'Keranjang kosong');
    redirect('/marketplace/index.php');
}

$subtotal = 0;
foreach ($items as $item) $subtotal += $item['price'] * $item['quantity'];
$total = $subtotal + SERVICE_FEE;

// Proses pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        $orderNumber = generateOrderNumber();
        $notes = sanitize($_POST['notes'] ?? '');
        $deadline = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = $pdo->prepare("INSERT INTO orders (order_number, buyer_id, subtotal, service_fee, total, notes, payment_deadline) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$orderNumber, $_SESSION['user_id'], $subtotal, SERVICE_FEE, $total, $notes, $deadline]);
        $orderId = $pdo->lastInsertId();
        
        foreach ($items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, seller_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['product_id'], $item['seller_id'], $item['quantity'], $item['price']]);
            
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        $pdo->prepare("DELETE FROM carts WHERE user_id = ?")->execute([$_SESSION['user_id']]);
        
        $pdo->commit();
        
        logActivity($pdo, $_SESSION['user_id'], 'checkout', "Order $orderNumber dibuat");
        setFlash('success', 'Pesanan berhasil dibuat! Bayar dalam 24 jam sebelum pesanan otomatis dibatalkan.');
        redirect('/marketplace/payment_upload.php?order=' . $orderId);
    } catch (Exception $e) {
        $pdo->rollBack();
        setFlash('danger', 'Gagal membuat pesanan. Coba lagi.');
        redirect('/marketplace/checkout.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container checkout-page">
  <h1>Checkout</h1>
  
  <div class="checkout-grid">
    <div class="checkout-items">
      <h3>Item Pesanan</h3>
      <?php foreach ($items as $item): ?>
        <div class="checkout-item">
          <div>
            <strong><?= sanitize($item['title']) ?></strong>
            <p><?= $item['quantity'] ?>x <?= formatRupiah($item['price']) ?></p>
            <p class="text-muted"><?= sanitize($item['seller_name']) ?></p>
          </div>
          <div class="checkout-item-total"><?= formatRupiah($item['price'] * $item['quantity']) ?></div>
        </div>
      <?php endforeach; ?>
      
      <h3>Alamat Pengiriman</h3>
      <div class="checkout-address">
        <p><strong><?= sanitize($user['name']) ?></strong></p>
        <p><?= sanitize($user['phone']) ?></p>
        <p><?= sanitize($user['address']) ?></p>
      </div>
    </div>
    
    <div class="checkout-summary">
      <h3>Ringkasan</h3>
      <div class="summary-row"><span>Subtotal</span><span><?= formatRupiah($subtotal) ?></span></div>
      <div class="summary-row"><span>Biaya Layanan</span><span><?= formatRupiah(SERVICE_FEE) ?></span></div>
      <div class="summary-row summary-total"><span>Total</span><span><?= formatRupiah($total) ?></span></div>
      
      <form method="POST">
        <div class="form-group">
          <label for="notes">Catatan (opsional)</label>
          <textarea id="notes" name="notes" rows="3" placeholder="Catatan untuk penjual..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-full" onclick="return confirm('Yakin ingin membuat pesanan?')">Buat Pesanan</button>
      </form>
      <a href="<?= SITE_URL ?>/marketplace/cart.php" class="btn btn-outline btn-full" style="margin-top:.5rem">Kembali ke Keranjang</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>