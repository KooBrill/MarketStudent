<?php
// ============================================
// Functions - MarketStudent
// ============================================
require_once __DIR__ . '/../config/database.php';

// ── Helper Functions ──
function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header("Location: " . SITE_URL . $path);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) redirect('/auth/login.php');
}

function requireAdmin() {
    requireLogin();
    global $pdo;
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user || $user['role'] !== 'admin') redirect('/');
}

function requireProfileComplete() {
    requireLogin();
    global $pdo;
    $stmt = $pdo->prepare("SELECT profile_completed FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user || !$user['profile_completed']) redirect('/member/profile.php');
}

function getUser($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . ' tahun lalu';
    if ($diff->m > 0) return $diff->m . ' bulan lalu';
    if ($diff->d > 0) return $diff->d . ' hari lalu';
    if ($diff->h > 0) return $diff->h . ' jam lalu';
    if ($diff->i > 0) return $diff->i . ' menit lalu';
    return 'Baru saja';
}

function generateOrderNumber() {
    return 'MS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

// ── Flash Messages ──
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ── Rate Limiting ──
function checkRateLimit($pdo, $ip, $action, $maxAttempts = 5, $windowMinutes = 15) {
    $stmt = $pdo->prepare("SELECT attempts, first_attempt FROM rate_limits WHERE ip_address = ? AND action = ?");
    $stmt->execute([$ip, $action]);
    $record = $stmt->fetch();
    
    if (!$record) return true;
    
    $windowStart = new DateTime($record['first_attempt']);
    $now = new DateTime();
    $diff = $now->getTimestamp() - $windowStart->getTimestamp();
    
    if ($diff > ($windowMinutes * 60)) {
        $stmt = $pdo->prepare("DELETE FROM rate_limits WHERE ip_address = ? AND action = ?");
        $stmt->execute([$ip, $action]);
        return true;
    }
    
    return $record['attempts'] < $maxAttempts;
}

function incrementRateLimit($pdo, $ip, $action) {
    $stmt = $pdo->prepare("INSERT INTO rate_limits (ip_address, action) VALUES (?, ?) ON DUPLICATE KEY UPDATE attempts = attempts + 1");
    $stmt->execute([$ip, $action]);
}

// ── Activity Logging ──
function logActivity($pdo, $userId, $action, $details = '') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $action, $details, $ip]);
}

// ── File Upload ──
function uploadFile($file, $directory = 'uploads/products/', $maxSize = 5242880) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload gagal'];
    }
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File terlalu besar (max 5MB)'];
    }
    
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        return ['success' => false, 'error' => 'Format file tidak didukung'];
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $targetDir = __DIR__ . '/../' . $directory;
    
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    
    $targetPath = $targetDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $directory . $filename];
    }
    return ['success' => false, 'error' => 'Gagal menyimpan file'];
}

// ── Email Functions ──
/**
 * Kirim email. Jika SMTP_ENABLED dan PHPMailer ada → pakai SMTP; else pakai mail().
 * From diambil dari SMTP_FROM_* atau fallback noreply.
 */
function sendMail($to, $subject, $bodyPlain) {
    $fromEmail = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'minad@marstud.shop';
    $fromName  = defined('SMTP_FROM_NAME')  ? SMTP_FROM_NAME  : SITE_NAME;

    if (defined('SMTP_ENABLED') && SMTP_ENABLED && !empty(SMTP_HOST) && !empty(SMTP_USERNAME)) {
        $vendor = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($vendor)) {
            require_once $vendor;
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USERNAME;
                $mail->Password   = SMTP_PASSWORD;
                $mail->SMTPSecure = (defined('SMTP_SECURE') && SMTP_SECURE === 'ssl') ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = defined('SMTP_PORT') ? (int) SMTP_PORT : 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($to);
                $mail->Subject = $subject;
                $mail->Body    = $bodyPlain;
                $mail->isHTML(false);

                return $mail->send();
            } catch (\Exception $e) {
                error_log('MarketStudent sendMail SMTP: ' . $e->getMessage());
                return false;
            }
        }
    }

    $headers = "From: $fromName <$fromEmail>\r\nContent-Type: text/plain; charset=UTF-8";
    return @mail($to, $subject, $bodyPlain, $headers);
}

function sendVerificationEmail($email, $code) {
    $subject = SITE_NAME . " - Kode Verifikasi";
    $message = "Kode verifikasi kamu: $code\n\nKode berlaku selama 5 menit.\nJangan bagikan kode ini kepada siapapun.";
    return sendMail($email, $subject, $message);
}

function sendResetEmail($email, $token) {
    $resetUrl = SITE_URL . "/auth/reset_password.php?token=" . $token;
    $subject = SITE_NAME . " - Reset Password";
    $message = "Klik link berikut untuk reset password:\n$resetUrl\n\nLink berlaku 1 jam.";
    return sendMail($email, $subject, $message);
}

// ── Status Badge ──
function statusBadge($status) {
    $map = [
        'pending'   => ['Menunggu', 'warning', 'fa-solid fa-clock'],
        'paid'      => ['Sudah Bayar', 'info', 'fa-solid fa-money-bill-transfer'],
        'verified'  => ['Terverifikasi', 'success', 'fa-solid fa-circle-check'],
        'packed'    => ['Dikemas', 'info', 'fa-solid fa-box-archive'],
        'shipped'   => ['Dikirim', 'info', 'fa-solid fa-truck-fast'],
        'done'      => ['Selesai', 'success', 'fa-solid fa-circle-check'],
        'rejected'  => ['Ditolak', 'danger', 'fa-solid fa-circle-xmark'],
        'cancelled' => ['Dibatalkan', 'danger', 'fa-solid fa-ban'],
    ];
    $info = $map[$status] ?? ['Unknown', 'secondary', 'fa-solid fa-question'];
    return '<span class="badge badge-' . $info[1] . '"><i class="' . $info[2] . '"></i> ' . $info[0] . '</span>';
}