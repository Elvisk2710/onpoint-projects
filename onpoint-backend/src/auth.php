<?php
declare(strict_types=1);

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_WINDOW_SECONDS = 900; // 15 minutes
const SESSION_IDLE_SECONDS = 7200; // 2 hours

function current_admin(): ?array
{
    static $admin = false;
    if ($admin !== false) {
        return $admin;
    }
    $admin = null;
    $id = $_SESSION['admin_id'] ?? null;
    if (!$id) {
        return null;
    }
    if (time() - ($_SESSION['last_seen'] ?? 0) > SESSION_IDLE_SECONDS) {
        logout();
        return null;
    }
    $_SESSION['last_seen'] = time();
    $stmt = db()->prepare('SELECT id, name, email FROM admins WHERE id = ? AND is_active = 1');
    $stmt->execute([$id]);
    $admin = $stmt->fetch() ?: null;
    return $admin;
}

/** Gate for every admin page. */
function require_admin(): array
{
    start_session();
    $admin = current_admin();
    if (!$admin) {
        $next = $_SERVER['REQUEST_URI'] ?? '';
        redirect('login.php' . ($next ? '?next=' . urlencode($next) : ''));
    }
    // Admin pages must never be cached or framed
    header('Cache-Control: no-store');
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    return $admin;
}

/**
 * @return string|null Error message, or null on success.
 */
function attempt_login(string $email, string $password): ?string
{
    $key = strtolower($email) . '|' . client_ip();
    if (throttle_hits('login', $key, LOGIN_WINDOW_SECONDS) >= LOGIN_MAX_ATTEMPTS
        || throttle_hits('login-ip', client_ip(), LOGIN_WINDOW_SECONDS) >= LOGIN_MAX_ATTEMPTS * 4) {
        return 'Too many failed attempts. Wait 15 minutes and try again.';
    }

    $stmt = db()->prepare('SELECT id, password_hash FROM admins WHERE email = ? AND is_active = 1');
    $stmt->execute([strtolower($email)]);
    $row = $stmt->fetch();

    // Verify against a dummy hash when the user doesn't exist so timing doesn't leak which emails are valid
    $hash = $row['password_hash'] ?? password_hash(random_bytes(8), PASSWORD_DEFAULT);
    if (!password_verify($password, $hash) || !$row) {
        throttle_hit('login', $key);
        throttle_hit('login-ip', client_ip());
        return 'Incorrect email or password.';
    }

    if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) {
        db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), $row['id']]);
    }

    throttle_clear('login', $key);
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $row['id'];
    $_SESSION['last_seen'] = time();
    unset($_SESSION['csrf']);
    db()->prepare('UPDATE admins SET last_login_at = NOW() WHERE id = ?')->execute([$row['id']]);
    return null;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/** @return string|null Error message, or null if acceptable. */
function password_problem(string $password): ?string
{
    if (strlen($password) < 10) {
        return 'Passwords must be at least 10 characters.';
    }
    return null;
}
