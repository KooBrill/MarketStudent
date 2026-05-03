<?php
$pageTitle = 'Lupa Password';
require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) redirect('/marketplace/index.php');

$errors = [];
$success = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($email) || !validateEmail($email)) {
        $errors[] = 'Masukkan email yang valid';
    } else {
        $stmt = $pdo->prepare("SELECT id, email_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && $user['email_verified']) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmt->execute([$token, $expires, $email]);
            
            sendResetEmail($email, $token);

            
            logActivity($pdo, $user['id'], 'forgot_password', "Request reset password");
            $success = true;
        } else {
            $success = true;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <h1>Lupa Password</h1>
      <p>Masukkan email untuk menerima link reset password</p>
    </div>
    
    <?php if ($success): ?>
      <div class="alert alert-success">
        Jika email terdaftar, link reset password telah dikirim. Cek inbox kamu.
      </div>

    <?php else: ?>
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $err): ?>
            <div><?= $err ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" class="auth-form">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="contoh@email.com" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Kirim Link Reset</button>
      </form>
    <?php endif; ?>
    
    <div class="auth-footer">
      <p><a href="<?= SITE_URL ?>/auth/login.php">Kembali ke login</a></p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
