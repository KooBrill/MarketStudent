<?php
$pageTitle = 'Marketplace';
require_once __DIR__ . '/../includes/functions.php';

$search = sanitize($_GET['q'] ?? '');
$category = sanitize($_GET['category'] ?? '');

$sql = "SELECT p.*, u.name as seller_name FROM products p JOIN users u ON p.seller_id = u.id WHERE p.is_active = 1";
$params = [];

if ($search) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category) {
    $sql .= " AND p.category = ?";
    $params[] = $category;
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE is_active = 1 AND category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container marketplace-page">
  <div class="marketplace-header">
    <h1>Marketplace</h1>
    <div class="category-filter">
      <a href="<?= SITE_URL ?>/marketplace/index.php" class="btn btn-sm <?= !$category ? 'btn-primary' : 'btn-outline' ?>">Semua</a>
      <?php foreach ($categories as $cat): ?>
        <a href="<?= SITE_URL ?>/marketplace/index.php?category=<?= urlencode($cat) ?>" class="btn btn-sm <?= $category === $cat ? 'btn-primary' : 'btn-outline' ?>"><?= sanitize($cat) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($search): ?>
    <p class="search-result-text">Hasil pencarian "<strong><?= sanitize($search) ?></strong>" — <?= count($products) ?> produk ditemukan</p>
  <?php endif; ?>

  <?php if ($products): ?>
    <div class="products-grid">
      <?php foreach ($products as $p): ?>
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
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon">Kosong</div>
      <h3>Belum ada produk</h3>
      <p>Jadilah yang pertama menjual!</p>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>