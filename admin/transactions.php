<?php
$pageTitle = 'Semua Transaksi';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT o.*, buyer.name as buyer_name FROM orders o JOIN users buyer ON o.buyer_id = buyer.id";
$params = [];

if ($statusFilter) {
    $sql .= " WHERE o.status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin_nav.php';
?>

<div class="container admin-page">
  <h1>Semua Transaksi</h1>
  
  <div class="filter-bar">
    <a href="<?= SITE_URL ?>/admin/transactions.php" class="btn btn-sm <?= !$statusFilter ? 'btn-primary' : 'btn-outline' ?>">Semua</a>
    <?php foreach (['pending', 'paid', 'verified', 'packed', 'shipped', 'done', 'rejected', 'cancelled'] as $s): ?>
      <a href="<?= SITE_URL ?>/admin/transactions.php?status=<?= $s ?>" class="btn btn-sm <?= $statusFilter === $s ? 'btn-primary' : 'btn-outline' ?>"><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
  </div>
  
  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>Order</th>
          <th>Pembeli</th>
          <th>Total</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td><strong><?= $order['order_number'] ?></strong></td>
            <td><?= sanitize($order['buyer_name']) ?></td>
            <td><?= formatRupiah($order['total']) ?></td>
            <td><?= statusBadge($order['status']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
            <td>
              <?php if ($order['status'] === 'paid'): ?>
                <a href="<?= SITE_URL ?>/admin/verify_payment.php" class="btn btn-primary btn-sm">Verifikasi</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
  <?php if (empty($orders)): ?>
    <p class="text-muted" style="text-align:center;padding:2rem">Tidak ada transaksi</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>