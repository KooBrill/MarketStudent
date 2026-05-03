<?php
// ============================================
// Serve Payment Proof - Hanya untuk Admin
// ============================================
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

requireAdmin();

$file = $_GET['file'] ?? '';

// Cegah directory traversal attack
$file = basename($file);

if (empty($file)) {
    http_response_code(404);
    die('File tidak ditemukan');
}

$path = __DIR__ . '/uploads/payments/' . $file;

// Jangan serve file .htaccess atau file non-gambar
$allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed_ext) || !file_exists($path)) {
    http_response_code(404);
    die('File tidak ditemukan');
}

// Set header dan serve file
$mime_types = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'webp' => 'image/webp',
    'gif'  => 'image/gif',
];

header('Content-Type: ' . ($mime_types[$ext] ?? 'application/octet-stream'));
header('Content-Length: ' . filesize($path));
header('Cache-Control: private, max-age=3600');
readfile($path);
exit;
