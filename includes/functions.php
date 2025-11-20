<?php

function app_name(): string { return 'ProJobs'; }
function base_url(): string {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $proto = $https ? 'https://' : 'http://';
    $rawHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $host = preg_replace('/[^A-Za-z0-9\.\-:]/', '', $rawHost) ?: 'localhost';
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

// Create a short, clean snippet from a (possibly HTML) description
function job_snippet(?string $html, int $max = 220): string {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($html ?? '')));
    if (mb_strlen($text) <= $max) return $text;
    return rtrim(mb_substr($text, 0, $max - 1)) . '…';
}

// Convert a free-form job description into structured markup so all posts share
// a consistent layout (paragraphs, bullet lists, labeled rows, etc.).
function format_job_description(?string $text): string {
    $text = trim((string) $text);
    if ($text === '') {
        return '<p class="desc-empty">Job description coming soon.</p>';
    }

    $lines = preg_split('/\r\n|\r|\n/', $text);
    $blocks = [];
    $listItems = [];

    foreach ($lines as $rawLine) {
        $line = trim($rawLine);
        if ($line === '') {
            if ($listItems) {
                $blocks[] = ['list', $listItems];
                $listItems = [];
            }
            continue;
        }

        if (preg_match('/^[-*•]\s*(.+)$/', $line, $m)) {
            $listItems[] = $m[1];
            continue;
        }

        if ($listItems) {
            $blocks[] = ['list', $listItems];
            $listItems = [];
        }

        if (preg_match('/^([A-Za-z][^:]{0,80}):\s*(.+)$/', $line, $m)) {
            $blocks[] = ['definition', ['label' => $m[1], 'value' => $m[2]]];
        } else {
            $blocks[] = ['paragraph', $line];
        }
    }

    if ($listItems) {
        $blocks[] = ['list', $listItems];
    }

    $html = '';
    foreach ($blocks as $block) {
        [$type, $data] = $block;
        switch ($type) {
            case 'list':
                $html .= '<ul class="desc-list">';
                foreach ($data as $item) {
                    $html .= '<li>' . e($item) . '</li>';
                }
                $html .= '</ul>';
                break;
            case 'definition':
                $label = e($data['label']);
                $value = e($data['value']);
                $html .= "<div class=\"desc-definition\"><span class=\"desc-term\">{$label}</span><span class=\"desc-detail\">{$value}</span></div>";
                break;
            default:
                $html .= '<p>' . e($data) . '</p>';
        }
    }

    return $html;
}

// Build a URL for a job image or fall back to a default placeholder
function job_image_url(?array $job): string {
    $default = base_url() . 'assets/img/default-job.jpg';
    if (!$job || empty($job['image_path'])) {
        return $default;
    }
    $safePath = ltrim(str_replace(['..', '\\'], '', $job['image_path']), '/');
    return base_url() . 'uploads/' . $safePath;
}

// Handle image upload: validate type/size, create uploads dir, return [path, error]
function handle_image_upload(array $file): array {
    if (empty($file['tmp_name'])) return [null, null];
    if (!is_uploaded_file($file['tmp_name'])) {
        return [null, 'Upload failed.'];
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']) ?: '';
    finfo_close($finfo);
    if (!isset($allowed[$mime])) {
        return [null, 'Only JPG, PNG, GIF, or WEBP allowed.'];
    }
    if (!empty($file['size']) && $file['size'] > 3 * 1024 * 1024) {
        return [null, 'Image too large (max 3MB).'];
    }
    $ext = $allowed[$mime];
    $name = uniqid('job_', true) . '.' . $ext;
    $uploadDir = realpath(__DIR__ . '/../uploads') ?: (__DIR__ . '/../uploads');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }
    $dest = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return [null, 'Could not save image.'];
    }
    return [$name, null];
}

// Job queries
function latest_jobs(int $limit = 5): array {
    $stmt = db()->prepare("SELECT id, title, company, location, image_path FROM jobs WHERE is_active = 1 AND is_approved = 1 AND (expires_at IS NULL OR expires_at >= CURDATE()) ORDER BY created_at DESC LIMIT :lim");
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
    $sql = "SELECT id, title, company, location, salary, experience, apply_link, image_path, description, created_at
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
    $stmt = db()->prepare("SELECT * FROM jobs WHERE id = :id AND is_active = 1 AND is_approved = 1");
    $stmt->execute([':id' => $id]);
    $job = $stmt->fetch();
    return $job ?: null;
}
