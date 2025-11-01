<?php
// Legacy file kept for backward compatibility.
// Use central connection at includes/db.php
require_once __DIR__ . '/includes/db.php';
// Expose $conn-like variable for extremely old snippets if needed.
// Prefer calling db() instead.
if (!isset($conn)) {
    try { $conn = db(); } catch (Throwable $e) { /* silent */ }
}
?>
