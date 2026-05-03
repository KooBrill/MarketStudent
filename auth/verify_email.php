<?php
$pageTitle = 'Verifikasi Email';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['verify_email'])) redirect('/auth/register.php');

$errors = [];
$email = $_SESSION['verify_email'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = sanitize($_POST['code'] ?? '');
    
    $stmt = $pdo->prepare("SELECT id, verify_code, verify_expires FROM users WHERE email = ? AND email_verified = 0");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $errors[] = 'Akun tidak ditemukan';
    } elseif ($user['verify_code'] !== $code) {
        $errors[] = 'Kode verifikasi salah';
    } elseif (strtotime($user['verify_expires']) < time()) {
        $errors[] = 'Kode sudah kadaluarsa. Silakan daftar ulang.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, verify_code = NULL, verify_expires = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        unset($_SESSION['verify_email']);
        
        logActivity($pdo, $user['id'], 'verify_email', 'Email berhasil diverifikasi');
        setFlash('success', 'Email berhasil diverifikasi! Silakan login.');
        redirect('/auth/login.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <h1>Verifikasi Email</h1>
      <p>Masukkan kode 6 digit yang dikirim ke <strong><?= sanitize($email) ?></strong></p>
    </div>
    

    
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?>
          <div><?= $err ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" class="auth-form">
      <div class="form-group">
        <label for="code">Kode Verifikasi</label>
        <input type="text" id="code" name="code" placeholder="Masukkan 6 digit kode" maxlength="6" required autofocus style="text-align:center;font-size:1.5rem;letter-spacing:8px">
      </div>
      <button type="submit" class="btn btn-primary btn-full">Verifikasi</button>
    </form>
    
    <div class="auth-footer">
      <p><a href="<?= SITE_URL ?>/auth/register.php">Kembali ke halaman daftar</a></p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>