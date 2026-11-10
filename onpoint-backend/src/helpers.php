<?php
declare(strict_types=1);

/** HTML-escape for output. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function input(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// ---------------------------------------------------------------------------
// Flash messages
// ---------------------------------------------------------------------------
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/** Abort the request unless a valid CSRF token was posted (form field or X-CSRF-Token header). */
function csrf_verify(): void
{
    $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Your session expired. Go back, refresh the page and try again.');
    }
}

// ---------------------------------------------------------------------------
// Slugs
// ---------------------------------------------------------------------------
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    if (function_exists('iconv')) {
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($t !== false) {
            $text = $t;
        }
    }
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    return substr($text !== '' ? $text : 'item', 0, 180);
}

/** Return a slug unique within $table, ignoring row $ignoreId. */
function unique_slug(string $table, string $base, ?int $ignoreId = null): string
{
    $allowed = ['posts', 'projects'];
    if (!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('Bad table');
    }
    $slug = slugify($base);
    $candidate = $slug;
    $i = 2;
    $stmt = db()->prepare("SELECT id FROM `$table` WHERE slug = ? AND id <> ? LIMIT 1");
    while (true) {
        $stmt->execute([$candidate, $ignoreId ?? 0]);
        if (!$stmt->fetch()) {
            return $candidate;
        }
        $candidate = $slug . '-' . $i++;
    }
}

// ---------------------------------------------------------------------------
// Rate limiting (shared by login and the contact API)
// ---------------------------------------------------------------------------
function throttle_hits(string $bucket, string $key, int $windowSeconds): int
{
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM throttle WHERE bucket = ? AND throttle_key = ? AND created_at > (NOW() - INTERVAL ? SECOND)'
    );
    $stmt->execute([$bucket, $key, $windowSeconds]);
    return (int) $stmt->fetchColumn();
}

function throttle_hit(string $bucket, string $key): void
{
    db()->prepare('INSERT INTO throttle (bucket, throttle_key) VALUES (?, ?)')->execute([$bucket, $key]);
    // Opportunistic cleanup
    if (random_int(1, 50) === 1) {
        db()->exec('DELETE FROM throttle WHERE created_at < (NOW() - INTERVAL 1 DAY)');
    }
}

function throttle_clear(string $bucket, string $key): void
{
    db()->prepare('DELETE FROM throttle WHERE bucket = ? AND throttle_key = ?')->execute([$bucket, $key]);
}

// ---------------------------------------------------------------------------
// Site settings (key/value)
// ---------------------------------------------------------------------------
function settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $pdo = db(false);
        if ($pdo) {
            try {
                foreach ($pdo->query('SELECT setting_key, setting_value FROM site_settings') as $row) {
                    $cache[$row['setting_key']] = $row['setting_value'];
                }
            } catch (Throwable $e) {
                error_log('settings: ' . $e->getMessage());
            }
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    return settings()[$key] ?? $default;
}

function save_setting(string $key, string $value): void
{
    db()->prepare(
        'INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    )->execute([$key, $value]);
}

// ---------------------------------------------------------------------------
// Misc
// ---------------------------------------------------------------------------
function format_date(?string $date, string $format = 'j M Y'): string
{
    return $date ? date($format, strtotime($date)) : '—';
}

/** Turn a stored upload path (uploads/...) into an absolute URL. */
function upload_url(?string $path): ?string
{
    if (!$path) {
        return null;
    }
    return str_starts_with($path, 'http') ? $path : BACKEND_URL . '/' . ltrim($path, '/');
}

function excerpt_from_html(string $html, int $length = 180): string
{
    // Put a space between block elements so paragraphs don't run together once tags are stripped
    $spaced = preg_replace('#</?(p|div|h[1-6]|li|ul|ol|br|tr|td|th|blockquote|figure|figcaption)\b[^>]*>#i', ' ', $html) ?? $html;
    $text = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($spaced), ENT_QUOTES, 'UTF-8')) ?? '');
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length - 1)) . '…';
}

function reading_minutes(string $html): int
{
    $words = str_word_count(strip_tags($html));
    return max(1, (int) round($words / 220));
}

/** Convert an HTML datetime-local value to MySQL DATETIME, or null. */
function to_mysql_datetime(string $value): ?string
{
    if ($value === '') {
        return null;
    }
    $ts = strtotime($value);
    return $ts ? date('Y-m-d H:i:s', $ts) : null;
}
