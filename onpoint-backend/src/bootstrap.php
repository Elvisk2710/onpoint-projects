<?php
/**
 * OnPoint backend bootstrap.
 * Loads .env, opens the database, starts a hardened session and pulls in helpers.
 * Every admin page and the API include this file first.
 */

declare(strict_types=1);

define('BACKEND_ROOT', dirname(__DIR__));

// ---------------------------------------------------------------------------
// .env loader (KEY=value per line, # comments, optional quotes)
// ---------------------------------------------------------------------------
function load_env(string $path): void
{
    if (!is_readable($path)) {
        http_response_code(500);
        error_log("OnPoint backend: missing .env at $path");
        exit('Backend is not configured.');
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && $value[-1] === $value[0]) {
            $value = substr($value, 1, -1);
        }
        $_ENV[$key] = $value;
    }
}

function env(string $key, ?string $default = null): ?string
{
    return $_ENV[$key] ?? $default;
}

function env_bool(string $key, bool $default = false): bool
{
    $v = env($key);
    return $v === null ? $default : in_array(strtolower($v), ['1', 'true', 'yes', 'on'], true);
}

load_env(BACKEND_ROOT . '/.env');

// ---------------------------------------------------------------------------
// Errors: log, never display in production
// ---------------------------------------------------------------------------
$debug = env_bool('APP_DEBUG');
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set(env('APP_TIMEZONE', 'Africa/Harare'));

// ---------------------------------------------------------------------------
// Site config
// ---------------------------------------------------------------------------
define('SITE_KEY', env('SITE_KEY', 'site'));             // engineering | cleaning | security
define('SITE_NAME', env('SITE_NAME', 'OnPoint'));
define('SITE_URL', rtrim(env('SITE_URL', ''), '/'));      // public site, e.g. https://www.onpointcivils.co.zw
define('BACKEND_URL', rtrim(env('BACKEND_URL', SITE_URL), '/')); // where /admin, /api, /uploads live
define('BLOG_PATH', env('BLOG_PATH', '/blog'));          // public URL prefix for posts
define('PROJECTS_PATH', env('PROJECTS_PATH', '/projects')); // public URL prefix for projects (engineering)
define('BRAND_COLOR', env('BRAND_COLOR', '#f2b705'));
define('FEATURE_PROJECTS', env_bool('FEATURE_PROJECTS'));
define('UPLOAD_DIR', BACKEND_ROOT . '/uploads');
define('UPLOAD_URL', BACKEND_URL . '/uploads');
define('UPLOAD_MAX_BYTES', (int) env('UPLOAD_MAX_MB', '15') * 1024 * 1024);

// ---------------------------------------------------------------------------
// Database
// ---------------------------------------------------------------------------
/**
 * Shared PDO connection. Admin/API callers use db() and get a 503 if MySQL is down.
 * Public pages call db(false) and get null instead, so the site still renders without the blog.
 */
function db(bool $fatal = true): ?PDO
{
    static $pdo = null;
    static $failed = false;
    if ($failed && !$fatal) {
        return null;
    }
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            env('DB_HOST', 'localhost'),
            env('DB_PORT', '3306'),
            env('DB_NAME', '')
        );
        try {
            $pdo = new PDO($dsn, env('DB_USER', ''), env('DB_PASS', ''), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $pdo->exec("SET time_zone = '" . (new DateTime())->format('P') . "'");
        } catch (PDOException $e) {
            error_log('OnPoint backend DB error: ' . $e->getMessage());
            if (!$fatal) {
                $failed = true;
                return null;
            }
            http_response_code(503);
            exit('Service temporarily unavailable.');
        }
    }
    return $pdo;
}

// ---------------------------------------------------------------------------
// Session (only started by admin pages; the API is stateless)
// ---------------------------------------------------------------------------
function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('onpoint_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    session_start();
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/uploads.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/content.php';
require_once __DIR__ . '/seo.php';
// Each public site defines its business facts in includes/profile.php (not present in the backend source folder)
if (is_file(BACKEND_ROOT . '/includes/profile.php')) {
    require_once BACKEND_ROOT . '/includes/profile.php';
}
