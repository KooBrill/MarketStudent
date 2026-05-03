<?php
$pageTitle = 'Kelola Iklan';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

// ── Handle Actions ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Tambah iklan baru
    if ($action === 'add') {
        $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $bg_from = $_POST['bg_color_from'] ?? '#061B45';
        $bg_to = $_POST['bg_color_to'] ?? '#065A82';
        $icon = trim($_POST['icon'] ?? 'fa-solid fa-bullhorn');
        $sort = (int)($_POST['sort_order'] ?? 0);

        if ($title) {
            $stmt = $pdo->prepare("INSERT INTO carousel_ads (product_id, title, description, bg_color_from, bg_color_to, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$product_id, $title, $description, $bg_from, $bg_to, $icon, $sort]);
            setFlash('success', 'Iklan berhasil ditambahkan');
        }
        redirect('/admin/ads.php');
    }

    // Toggle aktif/nonaktif
    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("UPDATE carousel_ads SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
        setFlash('success', 'Status iklan diperbarui');
        redirect('/admin/ads.php');
    }

    // Hapus iklan
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM carousel_ads WHERE id = ?")->execute([$id]);
        setFlash('success', 'Iklan dihapus');
        redirect('/admin/ads.php');
    }
}

// ── Fetch Data ──
$ads = $pdo->query("SELECT a.*, p.title as product_title, p.price as product_price FROM carousel_ads a LEFT JOIN products p ON a.product_id = p.id ORDER BY a.sort_order ASC, a.id DESC")->fetchAll();
$products = $pdo->query("SELECT id, title, price FROM products WHERE is_active = 1 ORDER BY title ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin_nav.php';
?>

<div class="container" style="padding-top:2rem;padding-bottom:3rem">
  <div class="page-header with-action">
    <div>
      <h1><i class="fa-solid fa-rectangle-ad"></i> Kelola Iklan Carousel</h1>
      <p>Tambah dan kelola iklan yang tampil di halaman utama</p>
    </div>
  </div>

  <!-- Form Tambah Iklan -->
  <div class="form-card" style="margin-bottom:2rem">
    <h3 style="margin-bottom:1rem"><i class="fa-solid fa-plus-circle"></i> Tambah Iklan Baru</h3>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="form-row">
        <div class="form-group">
          <label>Judul Iklan *</label>
          <input type="text" name="title" placeholder="Contoh: Promo Spesial!" required>
        </div>
        <div class="form-group">
          <label>Produk (opsional - untuk link ke produk)</label>
          <select name="product_id">
            <option value="">— Tanpa produk —</option>
            <?php foreach ($products as $p): ?>
              <option value="<?= $p['id'] ?>"><?= sanitize($p['title']) ?> (<?= formatRupiah($p['price']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Deskripsi</label>
        <input type="text" name="description" placeholder="Contoh: Dapatkan diskon hingga 50%">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Warna Gradient Dari</label>
          <input type="color" name="bg_color_from" value="#061B45" style="height:40px;width:100%">
        </div>
        <div class="form-group">
          <label>Warna Gradient Ke</label>
          <input type="color" name="bg_color_to" value="#065A82" style="height:40px;width:100%">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Icon (Font Awesome class)</label>
          <input type="text" name="icon" value="fa-solid fa-bullhorn" placeholder="fa-solid fa-tags">
          <span class="form-hint">Contoh: fa-solid fa-tags, fa-solid fa-fire, fa-solid fa-star</span>
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="sort_order" value="0" min="0">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Iklan</button>
      </div>
    </form>
  </div>

  <!-- Daftar Iklan -->
  <div class="form-card">
    <h3 style="margin-bottom:1rem"><i class="fa-solid fa-list"></i> Daftar Iklan (<?= count($ads) ?>)</h3>
    <?php if (empty($ads)): ?>
      <div class="empty-state" style="padding:2rem">
        <div class="empty-icon"><i class="fa-solid fa-rectangle-ad"></i></div>
        <h3>Belum ada iklan</h3>
        <p>Tambahkan iklan pertama menggunakan form di atas</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Preview</th>
              <th>Judul</th>
              <th>Produk</th>
              <th>Urutan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ads as $ad): ?>
              <tr>
                <td>
                  <div style="width:120px;height:50px;border-radius:6px;background:linear-gradient(135deg,<?= sanitize($ad['bg_color_from']) ?>,<?= sanitize($ad['bg_color_to']) ?>);display:flex;align-items:center;justify-content:center;color:white;font-size:.7rem;padding:.3rem">
                    <i class="<?= sanitize($ad['icon']) ?>" style="font-size:1.2rem;opacity:.5"></i>
                  </div>
                </td>
                <td>
                  <strong><?= sanitize($ad['title']) ?></strong>
                  <?php if ($ad['description']): ?>
                    <br><small style="color:var(--text-muted)"><?= sanitize($ad['description']) ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($ad['product_title']): ?>
                    <?= sanitize($ad['product_title']) ?>
                    <br><small><?= formatRupiah($ad['product_price']) ?></small>
                  <?php else: ?>
                    <span style="color:var(--text-light)">—</span>
                  <?php endif; ?>
                </td>
                <td><?= $ad['sort_order'] ?></td>
                <td>
                  <?php if ($ad['is_active']): ?>
                    <span class="badge badge-success">Aktif</span>
                  <?php else: ?>
                    <span class="badge badge-danger">Nonaktif</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display:flex;gap:.4rem">
                    <form method="POST" style="display:inline">
                      <input type="hidden" name="action" value="toggle">
                      <input type="hidden" name="id" value="<?= $ad['id'] ?>">
                      <button type="submit" class="btn btn-outline btn-sm" title="Toggle">
                        <i class="fa-solid fa-<?= $ad['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                      </button>
                    </form>
                    <form method="POST" style="display:inline" data-confirm="Hapus iklan ini?">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $ad['id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
