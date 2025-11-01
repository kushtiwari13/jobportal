<?php
// Centralized PDO connection and session management

// Start secure session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    session_start();
}

// Session timeout (minutes)
const SESSION_TIMEOUT_MINUTES = 30;
if (!empty($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > (SESSION_TIMEOUT_MINUTES * 60)) {
    // Timeout: destroy and require re-login
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time();

// Return a PDO instance (singleton per request)
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $cfg = require __DIR__ . '/../config/db.php';
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['dbname'], $cfg['charset']);
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], $opts);
    return $pdo;
}

