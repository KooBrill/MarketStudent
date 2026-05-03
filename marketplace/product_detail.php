<?php
require_once __DIR__ . '/../includes/functions.php';

$productId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, u.name as seller_name, u.avatar as seller_avatar, u.id as seller_id, u.phone as seller_phone FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ? AND p.is_active = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('warning', 'Produk tidak ditemukan');
    redirect('/marketplace/index.php');
}

$pageTitle = $product['title'];

// Add to cart
if (isset($_POST['add_to_cart']) && isLoggedIn()) {
    if ($product['seller_id'] == $_SESSION['user_id']) {
        setFlash('warning', 'Tidak bisa membeli produk sendiri');
    } else {
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        $stmt = $pdo->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->execute([$_SESSION['user_id'], $productId, $qty, $qty]);
        setFlash('success', 'Ditambahkan ke keranjang!');
    }
    redirect('/marketplace/product_detail.php?id=' . $productId);
}

// Related products
$stmt = $pdo->prepare("SELECT p.*, u.name as seller_name FROM products p JOIN users u ON p.seller_id = u.id WHERE p.is_active = 1 AND p.id != ? AND p.category = ? ORDER BY RAND() LIMIT 4");
$stmt->execute([$productId, $product['category']]);
$related = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container product-detail-page">
  <div class="pd-grid">
    <div class="pd-image-section">
      <?php if ($product['image']): ?>
        <img src="<?= SITE_URL ?>/<?= $product['image'] ?>" alt="<?= sanitize($product['title']) ?>" class="pd-image">
      <?php else: ?>
        <div class="pd-no-image">No Image</div>
      <?php endif; ?>
    </div>
    
    <div class="pd-info-section">
      <?php if ($product['category']): ?>
        <span class="pd-category"><?= sanitize($product['category']) ?></span>
      <?php endif; ?>
      <h1><?= sanitize($product['title']) ?></h1>
      <div class="pd-price"><?= formatRupiah($product['price']) ?></div>
      <div class="pd-meta">
        <span>Stok: <strong><?= $product['stock'] ?></strong></span>
        <span><?= timeAgo($product['created_at']) ?></span>
      </div>
      
      <?php if ($product['description']): ?>
        <div class="pd-description">
          <h3>Deskripsi</h3>
          <p><?= nl2br(sanitize($product['description'])) ?></p>
        </div>
      <?php endif; ?>
      
      <div class="pd-seller">
        <h3>Penjual</h3>
        <div class="pd-seller-info">
          <?php if ($product['seller_avatar']): ?>
            <img src="<?= SITE_URL ?>/<?= $product['seller_avatar'] ?>" class="pd-seller-avatar" alt="">
          <?php else: ?>
            <span class="pd-seller-avatar-placeholder">U</span>
          <?php endif; ?>
          <span><?= sanitize($product['seller_name']) ?></span>
        </div>
      </div>
      
      <?php 
        $currentBuyer = isLoggedIn() ? getUser($pdo, $_SESSION['user_id']) : null;
      ?>
      <?php if (isLoggedIn() && (!$currentBuyer['profile_completed'] || empty($currentBuyer['avatar']))): ?>
        <div class="alert alert-warning" style="margin-top:1rem">
          <i class="fa-solid fa-camera"></i>
          <div>
            <strong>Foto profil wajib!</strong> Kamu harus <strong>upload foto profil asli (foto wajah)</strong> dan lengkapi profil sebelum bisa membeli produk.
            <br><a href="<?= SITE_URL ?>/member/profile.php" class="btn btn-warning btn-sm" style="margin-top:.5rem"><i class="fa-solid fa-user-pen"></i> Lengkapi Profil Sekarang</a>
          </div>
        </div>
      <?php elseif (isLoggedIn() && $product['stock'] > 0 && $product['seller_id'] != $_SESSION['user_id']): ?>
        <form method="POST" class="pd-add-cart">
          <div class="pd-action-group">
            <label class="pd-qty-label">Jumlah</label>
            <div class="qty-selector">
              <button type="button" onclick="changeQty(-1)" class="qty-btn"><i class="fa-solid fa-minus"></i></button>
              <input type="number" name="quantity" id="qty" value="1" min="1" max="<?= $product['stock'] ?>">
              <button type="button" onclick="changeQty(1)" class="qty-btn"><i class="fa-solid fa-plus"></i></button>
            </div>
          </div>
          <button type="submit" name="add_to_cart" class="btn-add-cart">
            <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
          </button>
        </form>
        <?php if (!empty($product['seller_phone'])): ?>
          <a href="https://wa.me/<?= preg_replace('/^0/', '62', $product['seller_phone']) ?>?text=<?= urlencode('Halo ' . $product['seller_name'] . ', saya tertarik dengan produk "' . $product['title'] . '" (' . formatRupiah($product['price']) . ') di MarketStudent. Apakah masih tersedia?') ?>" target="_blank" class="btn-wa-chat">
            <i class="fa-brands fa-whatsapp"></i> Chat Penjual via WhatsApp
          </a>
        <?php endif; ?>
      <?php elseif ($product['stock'] <= 0): ?>
        <div class="pd-stock-empty">
          <i class="fa-solid fa-box-open"></i>
          <span>Stok Habis</span>
        </div>
        <?php if (!empty($product['seller_phone'])): ?>
          <a href="https://wa.me/<?= preg_replace('/^0/', '62', $product['seller_phone']) ?>?text=<?= urlencode('Halo ' . $product['seller_name'] . ', saya mau tanya soal produk "' . $product['title'] . '" di MarketStudent. Apakah akan restock?') ?>" target="_blank" class="btn-wa-chat">
            <i class="fa-brands fa-whatsapp"></i> Tanya Penjual via WhatsApp
          </a>
        <?php endif; ?>
      <?php elseif (!isLoggedIn()): ?>
        <a href="<?= SITE_URL ?>/auth/login.php" class="btn-add-cart">
          <i class="fa-solid fa-right-to-bracket"></i> Login untuk Membeli
        </a>
        <?php if (!empty($product['seller_phone'])): ?>
          <a href="https://wa.me/<?= preg_replace('/^0/', '62', $product['seller_phone']) ?>?text=<?= urlencode('Halo ' . $product['seller_name'] . ', saya tertarik dengan produk "' . $product['title'] . '" (' . formatRupiah($product['price']) . ') di MarketStudent. Bisa info lebih lanjut?') ?>" target="_blank" class="btn-wa-chat">
            <i class="fa-brands fa-whatsapp"></i> Chat Penjual via WhatsApp
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
  
  <?php if ($related): ?>
    <div class="related-products">
      <h2>Produk Serupa</h2>
      <div class="products-grid">
        <?php foreach ($related as $r): ?>
          <a href="<?= SITE_URL ?>/marketplace/product_detail.php?id=<?= $r['id'] ?>" class="product-card">
            <?php if ($r['image']): ?>
              <div class="pc-image"><img src="<?= SITE_URL ?>/<?= $r['image'] ?>" alt="<?= sanitize($r['title']) ?>"></div>
            <?php else: ?>
              <div class="pc-no-image">No Image</div>
            <?php endif; ?>
            <div class="pc-info">
              <h3 class="pc-title"><?= sanitize($r['title']) ?></h3>
              <div class="pc-price"><?= formatRupiah($r['price']) ?></div>
              <div class="pc-seller"><?= sanitize($r['seller_name']) ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    let val = parseInt(input.value) + delta;
    val = Math.max(1, Math.min(val, parseInt(input.max)));
    input.value = val;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>