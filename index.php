<?php
$pageTitle = '';
require_once __DIR__ . '/includes/functions.php';

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE email_verified = 1")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
$totalTransactions = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'done'")->fetchColumn();

$latestProducts = $pdo->query("SELECT p.*, u.name as seller_name FROM products p JOIN users u ON p.seller_id = u.id WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT 8")->fetchAll();

// Fetch carousel ads
try {
    $carouselAds = $pdo->query("SELECT a.*, p.title as product_title, p.price as product_price, p.image as product_image, p.id as pid FROM carousel_ads a LEFT JOIN products p ON a.product_id = p.id WHERE a.is_active = 1 ORDER BY a.sort_order ASC, a.id DESC LIMIT 5")->fetchAll();
} catch (Exception $e) {
    $carouselAds = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero-landing">
  <div class="container hero-grid">
    <div class="hero-content">
      <span class="hero-badge">Platform Jual Beli Masyarakat</span>
      <h1>Jual Beli Mudah<br>di <span class="highlight">MarketStudent</span></h1>
      <p class="hero-subtitle">Platform marketplace terpercaya untuk masyarakat di sekitar Unila.<br>Beli dan jual barang dengan mudah, aman, dan cepat.</p>
      <div class="hero-buttons">
        <a href="<?= SITE_URL ?>/marketplace/index.php" class="btn btn-primary btn-lg">Lihat Marketplace</a>
        <?php if (!isLoggedIn()): ?>
          <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-outline btn-lg">Daftar Gratis</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="hero-carousel-wrapper">
      <div class="carousel-wrapper" id="heroCarousel">
        <div class="carousel-track">
          <?php if (!empty($carouselAds)): ?>
            <?php foreach ($carouselAds as $i => $ad): ?>
              <div class="carousel-slide <?= $i === 0 ? 'active' : '' ?>">
                <a href="<?= $ad['pid'] ? SITE_URL . '/marketplace/product_detail.php?id=' . $ad['pid'] : '#' ?>" class="carousel-slide-content <?= !empty($ad['product_image']) ? 'has-image' : '' ?>" style="background: linear-gradient(135deg, <?= sanitize($ad['bg_color_from']) ?>, <?= sanitize($ad['bg_color_to']) ?>);">
                  <div class="carousel-text">
                    <h2><?= sanitize($ad['title']) ?></h2>
                    <p><?= sanitize($ad['description']) ?></p>
                    <?php if ($ad['product_title']): ?>
                      <span class="carousel-product-badge"><?= sanitize($ad['product_title']) ?> — <?= formatRupiah($ad['product_price']) ?></span>
                    <?php endif; ?>
                  </div>
                  <?php if (!empty($ad['product_image'])): ?>
                    <img src="<?= SITE_URL ?>/<?= $ad['product_image'] ?>" alt="<?= sanitize($ad['product_title']) ?>" class="carousel-product-img">
                  <?php else: ?>
                    <div class="carousel-icon"><i class="<?= sanitize($ad['icon']) ?>"></i></div>
                  <?php endif; ?>
                </a>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Default slides jika belum ada iklan di database -->
            <div class="carousel-slide active">
              <div class="carousel-slide-content" style="background: linear-gradient(135deg, #061B45, #065A82);">
                <div class="carousel-text">
                  <h2>Selamat Datang!</h2>
                  <p>Temukan berbagai produk terbaik dari penjual terpercaya</p>
                </div>
                <div class="carousel-icon"><i class="fa-solid fa-store"></i></div>
              </div>
            </div>
            <div class="carousel-slide">
              <div class="carousel-slide-content" style="background: linear-gradient(135deg, #065A82, #0789A3);">
                <div class="carousel-text">
                  <h2>Sistem COD</h2>
                  <p>Transaksi langsung antara penjual dan pembeli, tanpa ribet!</p>
                </div>
                <div class="carousel-icon"><i class="fa-solid fa-handshake"></i></div>
              </div>
            </div>
            <div class="carousel-slide">
              <div class="carousel-slide-content" style="background: linear-gradient(135deg, #0789A3, #061B45);">
                <div class="carousel-text">
                  <h2>Jual Produkmu</h2>
                  <p>Daftar sekarang dan mulai jual produk ke masyarakat</p>
                </div>
                <div class="carousel-icon"><i class="fa-solid fa-shop"></i></div>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <?php 
        $slideCount = !empty($carouselAds) ? count($carouselAds) : 3;
        if ($slideCount > 1): ?>
          <div class="carousel-dots">
            <?php for ($d = 0; $d < $slideCount; $d++): ?>
              <button class="carousel-dot <?= $d === 0 ? 'active' : '' ?>" data-slide="<?= $d ?>"></button>
            <?php endfor; ?>
          </div>
          <button class="carousel-btn carousel-prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="carousel-btn carousel-next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number"><?= $totalUsers ?>+</div>
        <div class="stat-label">Member Aktif</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalProducts ?>+</div>
        <div class="stat-label">Produk Tersedia</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $totalTransactions ?>+</div>
        <div class="stat-label">Transaksi Selesai</div>
      </div>
    </div>
  </div>
</section>

<?php if ($latestProducts): ?>
<section class="latest-products">
  <div class="container">
    <h2>Produk Terbaru</h2>
    <div class="products-grid">
      <?php foreach ($latestProducts as $p): ?>
        <a href="<?= SITE_URL ?>/marketplace/product_detail.php?id=<?= $p['id'] ?>" class="product-card">
          <?php if ($p['image']): ?>
            <div class="pc-image"><img src="<?= SITE_URL ?>/<?= $p['image'] ?>" alt="<?= sanitize($p['title']) ?>"></div>
          <?php else: ?>
            <div class="pc-no-image">No Image</div>
          <?php endif; ?>
          <div class="pc-info">
            <h3 class="pc-title"><?= sanitize($p['title']) ?></h3>
            <div class="pc-price"><?= formatRupiah($p['price']) ?></div>
            <div class="pc-seller"><?= sanitize($p['seller_name']) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:2rem">
      <a href="<?= SITE_URL ?>/marketplace/index.php" class="btn btn-outline btn-lg">Lihat Semua Produk</a>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="features-section">
  <div class="container">
    <h2>Kenapa MarketStudent?</h2>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-lock"></i></div>
        <h3>Transaksi Aman</h3>
        <p>Pembayaran diverifikasi admin sebelum barang dikirim</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
        <h3>Mudah Digunakan</h3>
        <p>Interface yang simpel dan intuitif untuk jual beli</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-users"></i></div>
        <h3>Untuk Semua Masyarakat</h3>
        <p>Komunitas terpercaya untuk seluruh masyarakat di sekitar Unila</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-handshake"></i></div>
        <h3>Sistem COD</h3>
        <p>Transaksi langsung antara penjual dan pembeli, tanpa ongkir</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

