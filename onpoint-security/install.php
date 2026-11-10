<?php
/**
 * One-time web installer for the first admin (for hosting without SSH).
 *
 *  1. Import schema.sql with phpMyAdmin.
 *  2. Put a long random INSTALL_KEY in .env.
 *  3. Open /install.php, create the first admin.
 *  4. DELETE this file from the server.
 *
 * It refuses to run once any admin exists, and when INSTALL_KEY is empty.
 */
require __DIR__ . '/src/bootstrap.php';

start_session();
header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
header('X-Robots-Tag: noindex');

$key = (string) env('INSTALL_KEY', '');
try {
    $hasAdmin = (int) db()->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0;
} catch (Throwable $e) {
    http_response_code(500);
    exit('Database tables are missing. Import schema.sql with phpMyAdmin first.');
}
if ($hasAdmin || strlen($key) < 16) {
    http_response_code(404);
    exit('Not found.');
}

$error = null;
$done = false;
if (is_post()) {
    csrf_verify();
    $name = input('name');
    $email = strtolower(input('email'));
    $pass = (string) ($_POST['password'] ?? '');
    if (throttle_hits('install', client_ip(), 900) >= 10) {
        $error = 'Too many attempts. Wait 15 minutes.';
    } elseif (!hash_equals($key, input('install_key'))) {
        throttle_hit('install', client_ip());
        $error = 'The install key is wrong.';
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a name and a valid email.';
    } elseif ($problem = password_problem($pass)) {
        $error = $problem;
    } else {
        db()->prepare('INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)')
            ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex,nofollow">
  <title>Install · <?= e(SITE_NAME) ?></title>
  <link rel="stylesheet" href="admin/assets/admin.css">
  <style>:root{--brand:<?= e(BRAND_COLOR) ?>}</style>
</head>
<body>
  <main class="auth">
    <form class="auth-card" method="post" novalidate>
      <h1>Create the first admin</h1>
      <?php if ($done): ?>
        <div class="alert alert-success">Admin created. <strong>Now delete install.php from the server</strong>, then <a href="admin/login.php">sign in</a>.</div>
      <?php else: ?>
        <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
        <?= csrf_field() ?>
        <div class="field"><label for="install_key">Install key <span class="hint">(INSTALL_KEY in .env)</span></label><input type="password" id="install_key" name="install_key" required></div>
        <div class="field"><label for="name">Name</label><input type="text" id="name" name="name" required maxlength="120"></div>
        <div class="field"><label for="email">Email</label><input type="email" id="email" name="email" required maxlength="190"></div>
        <div class="field"><label for="password">Password <span class="hint">(10+ characters)</span></label><input type="password" id="password" name="password" autocomplete="new-password" minlength="10" required></div>
        <button class="btn btn-brand" type="submit">Create admin</button>
      <?php endif; ?>
    </form>
  </main>
</body>
</html>
