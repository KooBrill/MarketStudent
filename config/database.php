<?php
// Config - MarketStudent
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

session_start();

// Ambil dari Environment Variables (di-set di Dokploy)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'marketstudent');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('SITE_NAME', 'MarketStudent');
define('SITE_URL', getenv('SITE_URL') ?: 'https://market.muimsa.me');
define('SERVICE_FEE', 3500);

// ── SMTP (kosongkan untuk pakai mail() atau simulasi) ──
define('SMTP_ENABLED', getenv('SMTP_ENABLED') === 'true');
define('SMTP_HOST',       getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT',       (int)(getenv('SMTP_PORT') ?: 587));
define('SMTP_SECURE',     getenv('SMTP_SECURE') ?: 'tls');
define('SMTP_USERNAME',   getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD',   getenv('SMTP_PASSWORD') ?: '');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'noreply@marketstudent.com');
define('SMTP_FROM_NAME',  getenv('SMTP_FROM_NAME') ?: 'MarketStudent');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed");
}