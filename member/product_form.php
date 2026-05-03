<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
requireProfileComplete();

$editId = (int)($_GET['id'] ?? 0);
$product = null;
$old = ['title' => '', 'description' => '', 'price' => '', 'stock' => 1, 'category' => ''];

if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
    $stmt->execute([$editId, $_SESSION['user_id']]);
    $product = $stmt->fetch();
    if (!$product) { redirect('/member/products.php'); }
    $old = $product;
    $pageTitle = 'Edit Produk';
} else {
    $pageTitle = 'Tambah Produk';
}

$errors = [];
$categories = ['Elektronik', 'Buku & Alat Tulis', 'Fashion', 'Makanan & Minuman', 'Jasa', 'Kos & Kontrakan', 'Kendaraan', 'Lainnya'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = (int)($_POST['price'] ?? 0);
    $stock = max(1, (int)($_POST['stock'] ?? 1));
    $category = sanitize($_POST['category'] ?? '');
    
    $old = ['title' => $title, 'description' => $description, 'price' => $price, 'stock' => $stock, 'category' => $category];
    
    if (empty($title)) $errors[] = 'Judul produk harus diisi';
    if ($price < 1000) $errors[] = 'Harga minimal Rp 1.000';
    if ($stock < 1) $errors[] = 'Stok minimal 1';
    if (empty($category)) $errors[] = 'Pilih kategori';
    
    $imagePath = $product['image'] ?? null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($_FILES['image']);
        if ($upload['success']) {
            $imagePath = $upload['path'];
        } else {
            $errors[] = $upload['error'];
        }
    }
    
    if (empty($errors)) {
        if ($editId) {
            $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, stock = ?, category = ?, image = ? WHERE id = ? AND seller_id = ?");
            $stmt->execute([$title, $description, $price, $stock, $category, $imagePath, $editId, $_SESSION['user_id']]);
            logActivity($pdo, $_SESSION['user_id'], 'product_update', "Updated product #$editId");
            setFlash('success', 'Produk berhasil diperbarui');
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (seller_id, title, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $price, $stock, $category, $imagePath]);
            logActivity($pdo, $_SESSION['user_id'], 'product_create', "Created product: $title");
            setFlash('success', 'Produk berhasil ditambahkan');
        }
        redirect('/member/products.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <h1><?= $editId ? 'Edit Produk' : 'Tambah Produk' ?></h1>
  
  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $err): ?>
        <div><?= $err ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  
  <div class="form-card">
    <form method="POST" enctype="multipart/form-data">
      <div class="form-section">
        <h3>Informasi Produk</h3>
        <div class="form-group">
          <label for="title">Judul Produk</label>
          <input type="text" id="title" name="title" value="<?= sanitize($old['title']) ?>" placeholder="Nama produk kamu" required>
        </div>
        <div class="form-group">
          <label for="description">Deskripsi</label>
          <textarea id="description" name="description" rows="4" placeholder="Jelaskan kondisi, spesifikasi, dll"><?= sanitize($old['description']) ?></textarea>
        </div>
      </div>
      
      <div class="form-section">
        <h3>Harga & Stok</h3>
        <div class="form-row">
          <div class="form-group">
            <label for="price">Harga (Rp)</label>
            <input type="number" id="price" name="price" value="<?= $old['price'] ?>" min="1000" placeholder="Contoh: 50000" required>
          </div>
          <div class="form-group">
            <label for="stock">Stok</label>
            <input type="number" id="stock" name="stock" value="<?= $old['stock'] ?>" min="1" required>
          </div>
        </div>
        <div class="form-group">
          <label for="category">Kategori</label>
          <select id="category" name="category" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>" <?= ($old['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      
      <div class="form-section">
        <h3>Foto Produk</h3>
        <?php if ($product && $product['image']): ?>
          <div class="current-image">
            <img src="<?= SITE_URL ?>/<?= $product['image'] ?>" alt="" style="max-width:200px;border-radius:8px">
            <p class="form-hint">Upload baru untuk mengganti foto.</p>
          </div>
        <?php endif; ?>
        <div class="form-group">
          <input type="file" id="image" name="image" accept="image/*">
          <span class="form-hint">Format: JPG, PNG, WebP. Maksimal 5MB</span>
        </div>
      </div>
      
      <div class="form-actions">
        <a href="<?= SITE_URL ?>/member/products.php" class="btn btn-outline">Batal</a>
        <button type="submit" class="btn btn-primary"><?= $editId ? 'Simpan Perubahan' : 'Tambah Produk' ?></button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>