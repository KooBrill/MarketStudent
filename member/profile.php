<?php
$pageTitle = 'Profil Saya';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$user = getUser($pdo, $_SESSION['user_id']);
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $kota = sanitize($_POST['kota'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $instansi = sanitize($_POST['instansi'] ?? '');
    
    if (empty($name)) $errors[] = 'Nama harus diisi';
    if (empty($phone)) $errors[] = 'Nomor HP harus diisi';
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) $errors[] = 'Nomor HP tidak valid';
    if (empty($kota)) $errors[] = 'Pilih kota/kabupaten';
    if (empty($address)) $errors[] = 'Detail alamat harus diisi';
    if (empty($instansi)) $errors[] = 'Instansi harus diisi';
    
    // Check phone unique
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
        $stmt->execute([$phone, $_SESSION['user_id']]);
        if ($stmt->fetch()) $errors[] = 'Nomor HP sudah digunakan akun lain';
    }
    
    // Avatar upload (WAJIB — harus foto asli muka)
    $avatarPath = $user['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadFile($_FILES['avatar'], 'uploads/avatars/');
        if ($upload['success']) {
            $avatarPath = $upload['path'];
        } else {
            $errors[] = $upload['error'];
        }
    }
    
    // Foto profil wajib ada
    if (empty($avatarPath)) {
        $errors[] = 'Foto profil wajib diupload! Gunakan foto asli wajah kamu.';
    }
    
    if (empty($errors)) {
        $profileCompleted = (!empty($phone) && !empty($address) && !empty($avatarPath)) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, kota = ?, address = ?, instansi = ?, avatar = ?, profile_completed = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $kota, $address, $instansi, $avatarPath, $profileCompleted, $_SESSION['user_id']]);
        
        logActivity($pdo, $_SESSION['user_id'], 'profile_update', 'Profil diperbarui');
        $success = true;
        $user = getUser($pdo, $_SESSION['user_id']);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <h1>Profil Saya</h1>
  
  <?php if (!$user['profile_completed']): ?>
    <div class="alert alert-warning">
      <strong>⚠️ Profil belum lengkap!</strong> Kamu <strong>wajib upload foto profil asli (foto wajah)</strong>, isi nomor HP, dan alamat sebelum bisa jual atau beli di MarketStudent.
    </div>
  <?php endif; ?>
  
  <?php if ($success): ?>
    <div class="alert alert-success">Profil berhasil diperbarui!</div>
  <?php endif; ?>
  
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
        <h3><i class="fa-solid fa-address-card"></i> Informasi Dasar</h3>
        <div class="form-group">
          <label for="name">Nama Lengkap</label>
          <input type="text" id="name" name="name" value="<?= sanitize($user['name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" value="<?= sanitize($user['email']) ?>" disabled>
          <span class="form-hint">Email tidak bisa diubah</span>
        </div>
        <div class="form-group">
          <label for="phone">Nomor HP</label>
          <input type="text" id="phone" name="phone" value="<?= sanitize($user['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx" required>
        </div>
        <div class="form-group">
          <label>Provinsi</label>
          <input type="text" value="Lampung" disabled>
          <input type="hidden" name="provinsi" value="Lampung">
        </div>
        <div class="form-group">
          <label for="kota">Kota / Kabupaten</label>
          <select id="kota" name="kota" required>
            <option value="">-- Pilih Kota/Kabupaten --</option>
            <?php 
            $kotaList = ['Kota Bandar Lampung','Kota Metro','Kab. Lampung Selatan','Kab. Lampung Tengah','Kab. Lampung Utara','Kab. Lampung Barat','Kab. Lampung Timur','Kab. Tanggamus','Kab. Tulang Bawang','Kab. Tulang Bawang Barat','Kab. Way Kanan','Kab. Pesawaran','Kab. Pringsewu','Kab. Mesuji','Kab. Pesisir Barat'];
            foreach ($kotaList as $k): ?>
              <option value="<?= $k ?>" <?= ($user['kota'] ?? '') === $k ? 'selected' : '' ?>><?= $k ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="address">Detail Alamat</label>
          <textarea id="address" name="address" rows="2" placeholder="Nama jalan, RT/RW, kelurahan, kecamatan" required><?= sanitize($user['address'] ?? '') ?></textarea>
          <span class="form-hint">Contoh: Jl. Prof. Sumantri Brojonegoro No. 1, Gedong Meneng, Rajabasa</span>
        </div>
      </div>
      
      <div class="form-section">
        <h3><i class="fa-solid fa-building"></i> Instansi</h3>
        <div class="form-group">
          <label for="instansi">Nama Instansi / Organisasi</label>
          <input type="text" id="instansi" name="instansi" value="<?= sanitize($user['instansi'] ?? $user['program_studi'] ?? '') ?>" placeholder="Contoh: Universitas Lampung, PT ABC, dll" required>
          <span class="form-hint">Isi dengan nama instansi, kampus, atau organisasi Anda</span>
        </div>
      </div>
      
      <div class="form-section">
        <h3>Foto Profil</h3>
        <div class="avatar-upload">
          <?php if ($user['avatar']): ?>
            <img src="<?= SITE_URL ?>/<?= $user['avatar'] ?>" class="avatar-preview" id="avatarPreview" alt="">
          <?php else: ?>
            <div class="avatar-placeholder" id="avatarPreview">Foto</div>
          <?php endif; ?>
          <div class="form-group">
            <input type="file" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)">
            <span class="form-hint">JPG, PNG, WebP. Maksimal 5MB</span>
          </div>
        </div>
      </div>
      
      <button type="submit" class="btn btn-primary btn-full">Simpan Profil</button>
    </form>
  </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'avatar-preview';
                img.id = 'avatarPreview';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>