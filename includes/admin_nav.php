<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-subnav-container">
  <div class="container">
    <div class="admin-subnav">
      <div class="admin-subnav-label">
        <i class="fa-solid fa-shield-halved"></i> <span>Admin Panel</span>
      </div>
      <div class="admin-subnav-links">
        <a href="<?= SITE_URL ?>/admin/dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <a href="<?= SITE_URL ?>/admin/transactions.php" class="<?= $current_page === 'transactions.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-list-check"></i> Transaksi
        </a>
        <a href="<?= SITE_URL ?>/admin/verify_payment.php" class="<?= $current_page === 'verify_payment.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-certificate"></i> Verifikasi
        </a>
        <a href="<?= SITE_URL ?>/admin/members.php" class="<?= $current_page === 'members.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-users-gear"></i> Member
        </a>
        <a href="<?= SITE_URL ?>/admin/ads.php" class="<?= $current_page === 'ads.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-rectangle-ad"></i> Iklan
        </a>
      </div>
      <div class="admin-subnav-exit">
        <a href="<?= SITE_URL ?>/marketplace/index.php" class="btn btn-outline btn-sm">
          <i class="fa-solid fa-shop"></i> Ke Toko
        </a>
      </div>
    </div>
  </div>
</div>
