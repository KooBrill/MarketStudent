<?php
// ============================================
// Register - MarketStudent
// ============================================
$pageTitle = 'Daftar Akun';
require_once __DIR__ . '/../includes/functions.php';

// Redirect jika sudah login
if (isLoggedIn()) redirect('/marketplace/index.php');

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    $old = ['name' => $name, 'email' => $email];
    
    // Validasi
    if (empty($name)) $errors[] = 'Nama harus diisi';
    if (strlen($name) > 100) $errors[] = 'Nama maksimal 100 karakter';
    
    if (empty($email)) $errors[] = 'Email harus diisi';
    if (!validateEmail($email)) $errors[] = 'Format email tidak valid';
    
    if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter';
    if ($password !== $password_confirm) $errors[] = 'Konfirmasi password tidak cocok';
    
    // Cek Terms & Conditions
    if (empty($_POST['agree_terms'])) $errors[] = 'Kamu harus menyetujui Syarat & Ketentuan';
    
    // Cek email sudah terdaftar
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = 'Email sudah terdaftar';
    }
    
    // Rate limit
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (!checkRateLimit($pdo, $ip, 'register', 3)) {
        $errors[] = 'Terlalu banyak percobaan. Coba lagi nanti.';
    }
    
    // Simpan
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $verifyCode = rand(100000, 999999);
        $verifyExpires = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, verify_code, verify_expires) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $verifyCode, $verifyExpires]);
        
        incrementRateLimit($pdo, $ip, 'register');
        
        // Kirim email verifikasi
        sendVerificationEmail($email, $verifyCode);
        
        // Simpan email di session untuk verify page
        $_SESSION['verify_email'] = $email;
        // verify_code_dev dihapus — kode hanya dikirim via email
        
        logActivity($pdo, null, 'register', "User $email mendaftar");
        
        redirect('/auth/verify_email.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <h1>Daftar Akun</h1>
      <p>Buat akun MarketStudent untuk mulai jual beli online</p>
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
        <label for="name">Nama Lengkap</label>
        <input type="text" id="name" name="name" value="<?= sanitize($old['name']) ?>" placeholder="Masukkan nama lengkap" required>
      </div>
      
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= sanitize($old['email']) ?>" placeholder="contoh@email.com" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required minlength="8">
      </div>
      
      <div class="form-group">
        <label for="password_confirm">Konfirmasi Password</label>
        <input type="password" id="password_confirm" name="password_confirm" placeholder="Ulangi password" required>
      </div>
      
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" name="agree_terms" value="1" <?= !empty($_POST['agree_terms']) ? 'checked' : '' ?> required>
          <span>Saya setuju dengan <a href="<?= SITE_URL ?>/terms.php" target="_blank">Syarat & Ketentuan</a> dan <a href="<?= SITE_URL ?>/privacy.php" target="_blank">Kebijakan Privasi</a> MarketStudent</span>
        </label>
      </div>
      
      <button type="submit" class="btn btn-primary btn-full">Daftar Sekarang</button>
    </form>
    
    <div class="auth-footer">
      <p>Sudah punya akun? <a href="<?= SITE_URL ?>/auth/login.php">Masuk di sini</a></p>
    </div>
  </div>
</div>

<!-- Modal Syarat & Ketentuan -->
<div class="modal-overlay" id="termsModal" onclick="if(event.target===this)this.classList.remove('modal-open')">
  <div class="modal-box">
    <div class="modal-header">
      <h2>Syarat & Ketentuan</h2>
      <button class="modal-close" onclick="document.getElementById('termsModal').classList.remove('modal-open')">x</button>
    </div>
    <div class="modal-body">
      <section class="terms-section">
        <h3>1. Pendahuluan</h3>
        <p>Selamat datang di <strong>MarketStudent</strong>, platform jual beli online khusus bagi mahasiswa dan komunitas kampus. Dengan mendaftar, Anda dianggap telah membaca, memahami, dan menyetujui seluruh syarat & ketentuan ini.</p>
      </section>
      <section class="terms-section">
        <h3>2. Definisi</h3>
        <ul>
          <li><strong>"Platform"</strong> — website dan layanan MarketStudent.</li>
          <li><strong>"Member"</strong> — Pengguna yang telah mendaftar dan memverifikasi akun.</li>
          <li><strong>"Penjual"</strong> — Member yang menjual produk/jasa.</li>
          <li><strong>"Pembeli"</strong> — Member yang membeli produk/jasa.</li>
          <li><strong>"Admin"</strong> — pengelola Platform yang memverifikasi transaksi.</li>
        </ul>
      </section>
      <section class="terms-section">
        <h3>3. Pendaftaran & Akun</h3>
        <ul>
          <li>Wajib mendaftar menggunakan <strong>email valid</strong> dan verifikasi email.</li>
          <li>Satu akun per orang. Bertanggung jawab atas keamanan akun.</li>
          <li>Akun tidak diverifikasi dalam <strong>24 jam</strong> otomatis dihapus.</li>
          <li>Wajib melengkapi profil sebelum transaksi jual-beli.</li>
          <li>Dilarang membuat akun palsu atau spam registrasi.</li>
        </ul>
      </section>
      <section class="terms-section">
        <h3>4. Produk & Penjualan</h3>
        <ul>
          <li>Deskripsi dan foto produk harus <strong>jujur dan akurat</strong>.</li>
          <li><strong>Dilarang menjual</strong> barang ilegal, narkotika, senjata, barang curian, atau konten dewasa.</li>
          <li>Admin berhak menghapus produk yang melanggar tanpa pemberitahuan.</li>
        </ul>
      </section>
      <section class="terms-section">
        <h3>5. Transaksi & Pembayaran</h3>
        <ul>
          <li>Transaksi hanya dilakukan antara Penjual dan Pembeli yang telah sepakat.</li>
          <li>Pembayaran via <strong>QRIS</strong> (Dana, OVO, GoPay, ShopeePay, dll).</li>
          <li>Biaya layanan <strong>Rp 3.500</strong> per transaksi.</li>
          <li>Wajib upload bukti pembayaran, diverifikasi Admin.</li>
        </ul>
      </section>
      <section class="terms-section">
        <h3>6. Pengiriman</h3>
        <ul>
          <li>Penjual wajib mengirim barang setelah pembayaran diverifikasi.</li>
          <li>Pengiriman COD atau langsung di lokasi yang disepakati Penjual dan Pembeli.</li>
          <li>Pembeli wajib konfirmasi penerimaan barang.</li>
        </ul>
      </section>
      <section class="terms-section">
        <h3>7. Larangan & Sanksi</h3>
        <ul>
          <li>Dilarang melakukan penipuan, upload bukti bayar palsu, atau spam.</li>
          <li>Pelanggaran ringan: peringatan. Sedang: banned. Berat: hapus akun permanen.</li>
        </ul>
      </section>
      <section class="terms-section">
        <h3>8. Privasi & Data</h3>
        <ul>
          <li>Data pengguna <strong>tidak dijual</strong> ke pihak ketiga.</li>
          <li>Data aktivitas dicatat untuk keamanan dan pencegahan penipuan.</li>
        </ul>
      </section>
      <section class="terms-section">
        <h3>9. Batasan Tanggung Jawab</h3>
        <p>MarketStudent bertindak sebagai <strong>perantara</strong>. Tidak bertanggung jawab atas kualitas barang atau sengketa antara Penjual dan Pembeli.</p>
      </section>
      <div class="terms-notice">
        <p>Dengan mendaftar, Anda menyatakan telah <strong>membaca, memahami, dan menyetujui</strong> seluruh Syarat & Ketentuan di atas.</p>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary btn-full" onclick="document.getElementById('termsModal').classList.remove('modal-open')">Saya Mengerti</button>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>