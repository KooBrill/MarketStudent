<?php
$pageTitle = 'Keranjang';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

// Hapus item
if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare("DELETE FROM carts WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['remove'], $_SESSION['user_id']]);
    setFlash('success', 'Item dihapus dari keranjang');
    redirect('/marketplace/cart.php');
}

// Update qty
if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $cartId => $qty) {
        $qty = max(1, (int)$qty);
        $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$qty, $cartId, $_SESSION['user_id']]);
    }
    setFlash('success', 'Keranjang diperbarui');
    redirect('/marketplace/cart.php');
}

$stmt = $pdo->prepare("SELECT c.*, p.title, p.price, p.stock, p.image, p.is_active, u.name as seller_name FROM carts c JOIN products p ON c.product_id = p.id JOIN users u ON p.seller_id = u.id WHERE c.user_id = ? ORDER BY c.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

$subtotal = 0;
foreach ($items as $item) $subtotal += $item['price'] * $item['quantity'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <h1>Keranjang Belanja</h1>
  
  <?php if ($items): ?>
    <form method="POST">
      <div class="cart-items">
        <?php foreach ($items as $item): ?>
          <div class="cart-item">
            <?php if ($item['image']): ?>
              <div class="ci-image"><img src="<?= SITE_URL ?>/<?= $item['image'] ?>" alt=""></div>
            <?php else: ?>
              <div class="ci-no-image">No Image</div>
            <?php endif; ?>
            <div class="ci-info">
              <h3><?= sanitize($item['title']) ?></h3>
              <p class="ci-seller"><?= sanitize($item['seller_name']) ?></p>
              <p class="ci-price"><?= formatRupiah($item['price']) ?></p>
            </div>
            <div class="ci-actions">
              <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" class="qty-input">
              <a href="<?= SITE_URL ?>/marketplace/cart.php?remove=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus item ini?')">Hapus</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <div class="cart-summary">
        <div class="cart-total">
          <span>Subtotal:</span>
          <span><?= formatRupiah($subtotal) ?></span>
        </div>
        <div class="cart-total">
          <span>Biaya Layanan:</span>
          <span><?= formatRupiah(SERVICE_FEE) ?></span>
        </div>
        <div class="cart-total cart-grand-total">
          <span>Total:</span>
          <span><?= formatRupiah($subtotal + SERVICE_FEE) ?></span>
        </div>
        <button type="submit" name="update_cart" class="btn btn-outline btn-full">Update Keranjang</button>
        <a href="<?= SITE_URL ?>/marketplace/checkout.php" class="btn btn-primary btn-full">Lanjut ke Checkout</a>
      </div>
    </form>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon">Kosong</div>
      <h3>Keranjang masih kosong</h3>
      <p>Yuk mulai belanja!</p>
      <a href="<?= SITE_URL ?>/marketplace/index.php" class="btn btn-primary">Belanja Sekarang</a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>