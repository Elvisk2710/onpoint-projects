<?php
require __DIR__ . '/../src/bootstrap.php';

start_session();
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');

/** Only allow redirects back into /admin on this host. */
function safe_next(string $next): string
{
    return preg_match('#^/[A-Za-z0-9_\-/]*admin/[A-Za-z0-9_\-./?=&%]*$#', $next) && !str_contains($next, '//')
        ? $next : 'index.php';
}

if (current_admin()) {
    redirect('index.php');
}

$error = null;
$email = '';
if (is_post()) {
    csrf_verify();
    $email = input('email');
    $error = attempt_login($email, (string) ($_POST['password'] ?? ''));
    if ($error === null) {
        redirect(safe_next((string) ($_GET['next'] ?? '')));
    }
}
$site = setting('company_name') ?: SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex,nofollow">
  <title>Sign in · <?= e($site) ?> Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css">
  <style>:root{--brand:<?= e(BRAND_COLOR) ?>}</style>
</head>
<body>
  <main class="auth">
    <form class="auth-card" method="post" novalidate>
      <div class="brand"><span class="brand-mark"><?= e(mb_substr($site, 0, 1)) ?></span><span><?= e($site) ?><small>Admin</small></span></div>
      <h1>Sign in</h1>
      <p class="hint" style="margin:0 0 20px">Manage blog posts<?= FEATURE_PROJECTS ? ', projects' : '' ?> and messages.</p>
      <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
      <?= csrf_field() ?>
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="username" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
      </div>
      <button class="btn btn-brand" type="submit">Sign in</button>
    </form>
  </main>
</body>
</html>
