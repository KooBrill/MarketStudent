<?php // Footer ?>
  </main>
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-links">
          <h3>Market<span>Student</span></h3>
          <p>Platform jual beli terpercaya untuk masyarakat di sekitar Unila</p>
        </div>
        <div class="footer-links">
          <h4>Menu</h4>
          <a href="<?= SITE_URL ?>/marketplace/index.php">Marketplace</a>
          <a href="<?= SITE_URL ?>/auth/register.php">Daftar</a>
          <a href="<?= SITE_URL ?>/terms.php">Syarat & Ketentuan</a>
          <a href="<?= SITE_URL ?>/privacy.php">Kebijakan Privasi</a>
        </div>
        <div class="footer-links">
          <h4>Info</h4>
          <p>Online • Indonesia</p>
          <p>admin@marketstudent.com</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <strong>MarketStudent</strong> — Platform Jual Beli Masyarakat</p>
      </div>
    </div>
  </footer>
  <!-- Cookie Consent Banner -->
  <div class="cookie-consent" id="cookieConsent" role="dialog" aria-label="Persetujuan Cookie">
    <div class="cookie-consent-inner">
      <p class="cookie-consent-text">
        Kami menggunakan cookie untuk pengalaman lebih baik dan keamanan. Dengan melanjutkan, Anda setuju penggunaan cookie.
        <a href="<?= SITE_URL ?>/privacy.php">Kebijakan Privasi</a>
      </p>
      <div class="cookie-consent-actions">
        <button type="button" class="btn btn-outline btn-sm cookie-btn" id="cookieDecline">Tolak</button>
        <button type="button" class="btn btn-primary btn-sm cookie-btn" id="cookieAccept">Terima</button>
      </div>
    </div>
  </div>

  <script src="<?= SITE_URL ?>/assets/script.js"></script>
</body>
</html>