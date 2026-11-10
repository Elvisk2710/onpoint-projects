<?php
/**
 * Create (or reset) an admin account from the command line.
 *   php seed_admin.php "Full Name" email@example.com
 * You will be prompted for the password.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__ . '/src/bootstrap.php';

[$script, $name, $email] = array_pad($argv, 3, null);
if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php seed_admin.php \"Full Name\" email@example.com\n");
    exit(1);
}

fwrite(STDOUT, 'Password (10+ characters): ');
if (stripos(PHP_OS, 'WIN') !== 0) {
    shell_exec('stty -echo');
}
$password = trim((string) fgets(STDIN));
if (stripos(PHP_OS, 'WIN') !== 0) {
    shell_exec('stty echo');
}
fwrite(STDOUT, "\n");

if ($problem = password_problem($password)) {
    fwrite(STDERR, $problem . "\n");
    exit(1);
}

$email = strtolower($email);
$hash = password_hash($password, PASSWORD_DEFAULT);
db()->prepare(
    'INSERT INTO admins (name, email, password_hash, is_active) VALUES (?, ?, ?, 1)
     ON DUPLICATE KEY UPDATE name = VALUES(name), password_hash = VALUES(password_hash), is_active = 1'
)->execute([$name, $email, $hash]);

fwrite(STDOUT, "Admin $email is ready. Sign in at " . BACKEND_URL . "/admin/\n");
