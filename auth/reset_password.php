<?php
$pageTitle = 'Reset Password';
require_once __DIR__ . '/../includes/functions.php';

$token = $_GET['token'] ?? '';
$errors = [];
$valid = false;

if ($token) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if ($user) $valid = true;
}

if (!$valid && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlash('danger', 'Link reset tidak valid atau sudah kadaluarsa.');
    redirect('/auth/forgot_password.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $errors[] = 'Token tidak valid';
    } else {
        if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter';
        if ($password !== $password_confirm) $errors[] = 'Konfirmasi password tidak cocok';
        
        if (empty($errors)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->execute([$hashed, $user['id']]);
            
            logActivity($pdo, $user['id'], 'reset_password', 'Password berhasil direset');
            setFlash('success', 'Password berhasil direset! Silakan login.');
            redirect('/auth/login.php');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <h1>Reset Password</h1>
      <p>Buat password baru untuk akun kamu</p>
    </div>
    
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?>
          <div><?= $err ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" class="auth-form">
      <input type="hidden" name="token" value="<?= sanitize($token) ?>">
      <div class="form-group">
        <label for="password">Password Baru</label>
        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required minlength="8">
      </div>
      <div class="form-group">
        <label for="password_confirm">Konfirmasi Password</label>
        <input type="password" id="password_confirm" name="password_confirm" placeholder="Ulangi password baru" required>
      </div>
      <button type="submit" class="btn btn-primary btn-full">Simpan Password Baru</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>