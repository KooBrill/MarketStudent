<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="MarketStudent - Platform Jual Beli Masyarakat">
  <title><?= isset($pageTitle) ? $pageTitle . ' — ' : '' ?><?= SITE_NAME ?></title>
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/style.css">
  <link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <nav class="navbar" id="navbar">
    <div class="container nav-container">
      <a href="<?= SITE_URL ?>" class="logo">Market<span>Student</span></a>

      <div class="nav-search">
        <form action="<?= SITE_URL ?>/marketplace/index.php" method="GET">
          <input type="text" name="q" placeholder="Cari produk terbaik..." value="<?= sanitize($_GET['q'] ?? '') ?>">
          <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        </form>
      </div>

      <!-- Hamburger for mobile -->
      <button class="menu-toggle" id="menuToggle" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>

      <div class="nav-actions" id="navActions">
        <?php if (isLoggedIn()): ?>
          <?php $currentUser = getUser($pdo, $_SESSION['user_id']); ?>
          <a href="<?= SITE_URL ?>/marketplace/cart.php" class="nav-icon" title="Keranjang">
            <i class="fa-solid fa-cart-shopping"></i>
          </a>
          <a href="<?= SITE_URL ?>/marketplace/index.php" class="nav-link hide-mobile">
            <i class="fa-solid fa-shop"></i> Marketplace
          </a>

          <div class="nav-dropdown" id="userDropdown">
            <button class="nav-user-btn" id="dropdownToggleBtn" type="button">
              <i class="fa-solid fa-circle-user nav-avatar-placeholder"></i>
              <span class="nav-username"><?= sanitize($currentUser['name'] ?? 'User') ?></span>
              <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
            </button>
            <div class="dropdown-menu" id="dropdownMenu">
              <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                <a href="<?= SITE_URL ?>/admin/dashboard.php"><i class="fa-solid fa-shield-halved"></i> Admin Panel</a>
                <div class="dropdown-divider"></div>
              <?php endif; ?>
              <a href="<?= SITE_URL ?>/member/dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
              <a href="<?= SITE_URL ?>/member/products.php"><i class="fa-solid fa-box"></i> Produk Saya</a>
              <a href="<?= SITE_URL ?>/member/orders.php"><i class="fa-solid fa-receipt"></i> Pesanan Masuk</a>
              <a href="<?= SITE_URL ?>/member/purchases.php"><i class="fa-solid fa-bag-shopping"></i> Riwayat Belanja</a>
              <a href="<?= SITE_URL ?>/member/profile.php"><i class="fa-solid fa-user-pen"></i> Edit Profil</a>
              <div class="dropdown-divider"></div>
              <a href="<?= SITE_URL ?>/auth/logout.php" class="dropdown-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-right-to-bracket"></i> Masuk</a>
          <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-user-plus"></i> Daftar</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main>
  <?php
  $flash = getFlash();
  if ($flash):
  ?>
    <div class="container" style="margin-top:1rem">
      <div class="alert alert-<?= $flash['type'] ?>" id="flashAlert">
        <?= $flash['message'] ?>
        <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
      </div>
    </div>
  <?php endif; ?>