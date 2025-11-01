<?php

function app_name(): string { return 'ProJobs'; }
function base_url(): string {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $proto = $https ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : '';
    $appRoot = realpath(__DIR__ . '/..');
    $docRoot = $docRoot ? rtrim(str_replace('\\', '/', $docRoot), '/') : '';
    $appRoot = $appRoot ? rtrim(str_replace('\\', '/', $appRoot), '/') : '';
    $sub = '';
    if ($docRoot && $appRoot && strpos($appRoot, $docRoot) === 0) {
        $sub = trim(substr($appRoot, strlen($docRoot)), '/');
    }
    $path = $sub ? ('/' . $sub . '/') : '/';
    return $proto . $host . $path;
}

// Basic sanitization for output
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }

// CSRF token helpers
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(400);
            exit('Invalid CSRF token.');
        }
    }
}

// Flash messaging
function flash(string $key, ?string $message = null) {
    if ($message === null) {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    $_SESSION['flash'][$key] = $message;
}

// Validation helpers
function valid_url(string $url): bool { return (bool) filter_var($url, FILTER_VALIDATE_URL); }
function slugify(string $text): string {
    $text = preg_replace('~[\p{Z}\s]+~u', '-', trim($text));
    $text = preg_replace('~[^\pL\pN\-]+~u', '', $text);
    $text = trim($text, '-');
    $text = mb_strtolower($text);
    return $text ?: 'job';
}

// Pagination helper returns [limit, offset, total_pages]
function paginate(int $perPage, int $total, int $page): array {
    $pages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $pages));
    $offset = ($page - 1) * $perPage;
    return [$perPage, $offset, $pages, $page];
}

// Job queries
function latest_jobs(int $limit = 5): array {
    $stmt = db()->prepare("SELECT id, title, company, location FROM jobs WHERE is_active = 1 AND is_approved = 1 AND (expires_at IS NULL OR expires_at >= CURDATE()) ORDER BY created_at DESC LIMIT :lim");
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function count_jobs(string $where = '', array $params = []): int {
    $sql = "SELECT COUNT(*) AS c FROM jobs WHERE is_active = 1 AND is_approved = 1 AND (expires_at IS NULL OR expires_at >= CURDATE())";
    if ($where) { $sql .= " AND ($where)"; }
    $stmt = db()->prepare($sql);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    return (int) $stmt->fetchColumn();
}

function search_jobs(string $where = '', array $params = [], int $limit = 10, int $offset = 0): array {
    $sql = "SELECT id, title, company, location, skills, SUBSTRING(description,1,220) AS snippet, created_at
            FROM jobs
            WHERE is_active = 1 AND is_approved = 1 AND (expires_at IS NULL OR expires_at >= CURDATE())";
    if ($where) { $sql .= " AND ($where)"; }
    $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = db()->prepare($sql);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function find_job(int $id): ?array {
    $stmt = db()->prepare("SELECT * FROM jobs WHERE id = :id AND is_active = 1");
    $stmt->execute([':id' => $id]);
    $job = $stmt->fetch();
    return $job ?: null;
}
