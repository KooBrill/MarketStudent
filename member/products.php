<?php
$pageTitle = 'Produk Saya';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

// Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
    $stmt->execute([$_GET['delete'], $_SESSION['user_id']]);
    setFlash('success', 'Produk berhasil dihapus');
    redirect('/member/products.php');
}

// Toggle active
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE products SET is_active = NOT is_active WHERE id = ? AND seller_id = ?");
    $stmt->execute([$_GET['toggle'], $_SESSION['user_id']]);
    setFlash('success', 'Status produk diperbarui');
    redirect('/member/products.php');
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <div class="page-header-row">
    <h1>Produk Saya</h1>
    <a href="<?= SITE_URL ?>/member/product_form.php" class="btn btn-primary">Tambah Produk</a>
  </div>
  
  <?php if ($products): ?>
    <div class="products-manage">
      <?php foreach ($products as $product): ?>
        <div class="product-manage-card <?= !$product['is_active'] ? 'inactive' : '' ?>">
          <?php if ($product['image']): ?>
            <div class="pm-image"><img src="<?= SITE_URL ?>/<?= $product['image'] ?>" alt=""></div>
          <?php else: ?>
            <div class="pm-no-image">No Image</div>
          <?php endif; ?>
          <div class="pm-info">
            <h3><?= sanitize($product['title']) ?></h3>
            <p class="pm-price"><?= formatRupiah($product['price']) ?></p>
            <div class="pm-meta">
              <span>Stok: <?= $product['stock'] ?></span>
              <span><?= sanitize($product['category'] ?? 'Tanpa kategori') ?></span>
            </div>
          </div>
          <div class="pm-actions">
            <a href="<?= SITE_URL ?>/member/product_form.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm">
              <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            <a href="<?= SITE_URL ?>/member/products.php?toggle=<?= $product['id'] ?>" class="btn btn-sm <?= $product['is_active'] ? 'btn-warning' : 'btn-success' ?>">
              <i class="fa-solid <?= $product['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
              <?= $product['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
            </a>
            <a href="<?= SITE_URL ?>/member/products.php?delete=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')">
              <i class="fa-solid fa-trash-can"></i> Hapus
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon">Kosong</div>
      <h3>Belum ada produk</h3>
      <p>Mulai jual barang kamu sekarang!</p>
      <a href="<?= SITE_URL ?>/member/product_form.php" class="btn btn-primary">Tambah Produk</a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>