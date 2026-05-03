<?php
$pageTitle = 'Masuk';
require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) redirect('/marketplace/index.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $errors[] = 'Email dan password harus diisi';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if (!$user['email_verified']) {
                $errors[] = 'Email belum diverifikasi. Cek inbox kamu.';
            } elseif ($user['is_banned']) {
                $errors[] = 'Akun kamu telah diblokir. Hubungi admin.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                logActivity($pdo, $user['id'], 'login', 'Login berhasil');
                
                if ($user['role'] === 'admin') {
                    redirect('/admin/dashboard.php');
                } elseif (!$user['profile_completed']) {
                    redirect('/member/profile.php');
                } else {
                    redirect('/member/dashboard.php');
                }
            }
        } else {
            $errors[] = 'Email atau password salah';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <h1>Masuk</h1>
      <p>Login ke akun MarketStudent kamu</p>
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
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="contoh@email.com" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
        <a href="<?= SITE_URL ?>/auth/forgot_password.php" class="form-link">Lupa password?</a>
      </div>
      <button type="submit" class="btn btn-primary btn-full">Masuk</button>
    </form>
    
    <div class="auth-footer">
      <p>Belum punya akun? <a href="<?= SITE_URL ?>/auth/register.php">Daftar di sini</a></p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>