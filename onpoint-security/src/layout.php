<?php
declare(strict_types=1);

/** Shared admin chrome. Call admin_header() before page content and admin_footer() after. */

function admin_nav_items(): array
{
    $items = [
        'dashboard' => ['index.php', 'Dashboard', 'M3 12l9-8 9 8M5 10v10h14V10'],
        'posts' => ['posts.php', 'Blog posts', 'M4 5h16M4 10h16M4 15h10M4 20h7'],
    ];
    if (FEATURE_PROJECTS) {
        $items['projects'] = ['projects.php', 'Projects', 'M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6'];
    }
    $items['messages'] = ['messages.php', 'Messages', 'M4 6h16v12H4zM4 7l8 6 8-6'];
    $items['settings'] = ['settings.php', 'Settings', 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1.1-1.5 1.7 1.7 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1.1 1.7 1.7 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.8.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.8V9a1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z'];
    return $items;
}

function icon(string $path, int $size = 18): string
{
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' . e($path) . '"/></svg>';
}

function new_message_count(): int
{
    static $n = null;
    return $n ??= (int) db()->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
}

function admin_header(string $title, string $active, array $opts = []): void
{
    $admin = current_admin();
    $site = setting('company_name') ?: SITE_NAME;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex,nofollow">
  <title><?= e($title) ?> · <?= e($site) ?> Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css">
  <style>:root{--brand:<?= e(BRAND_COLOR) ?>}</style>
  <?php foreach ($opts['head'] ?? [] as $tag) echo $tag, "\n"; ?>
</head>
<body>
<div class="shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="index.php">
      <span class="brand-mark"><?= e(mb_substr($site, 0, 1)) ?></span>
      <span><?= e($site) ?><small>Admin</small></span>
    </a>
    <nav>
      <?php foreach (admin_nav_items() as $key => [$href, $label, $path]): ?>
        <a href="<?= $href ?>" class="<?= $key === $active ? 'active' : '' ?>">
          <?= icon($path) ?><span><?= e($label) ?></span>
          <?php if ($key === 'messages' && new_message_count() > 0): ?><em class="pill"><?= new_message_count() ?></em><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
      <a href="<?= e(SITE_URL ?: '/') ?>" target="_blank" rel="noopener"><?= icon('M14 3h7v7M10 14L21 3M21 14v7H3V3h7') ?><span>View website</span></a>
      <div class="me">
        <span class="avatar"><?= e(mb_strtoupper(mb_substr($admin['name'] ?? '?', 0, 1))) ?></span>
        <span class="me-name"><?= e($admin['name'] ?? '') ?><small><?= e($admin['email'] ?? '') ?></small></span>
        <form method="post" action="logout.php"><?= csrf_field() ?><button class="icon-btn" title="Log out" aria-label="Log out"><?= icon('M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9') ?></button></form>
      </div>
    </div>
  </aside>
  <div class="main">
    <header class="topbar">
      <button class="icon-btn menu-btn" aria-label="Open menu" data-toggle-sidebar><?= icon('M4 6h16M4 12h16M4 18h16', 20) ?></button>
      <h1><?= e($title) ?></h1>
      <div class="topbar-actions"><?= $opts['actions'] ?? '' ?></div>
    </header>
    <main class="content">
      <?php foreach (take_flashes() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>" role="status"><?= e($f['message']) ?></div>
      <?php endforeach; ?>
<?php
}

function admin_footer(array $scripts = []): void
{
    ?>
    </main>
  </div>
</div>
<div class="scrim" data-toggle-sidebar></div>
<script src="assets/admin.js"></script>
<?php foreach ($scripts as $s) echo $s, "\n"; ?>
</body>
</html>
<?php
}

function status_badge(string $status): string
{
    $labels = [
        'published' => 'Published', 'draft' => 'Draft', 'scheduled' => 'Scheduled',
        'planned' => 'Planned', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On hold',
        'new' => 'New', 'read' => 'Read', 'archived' => 'Archived',
    ];
    return '<span class="badge badge-' . e($status) . '">' . e($labels[$status] ?? ucfirst($status)) . '</span>';
}

/** Effective status of a post: scheduled = published with a future date. */
function post_state(array $post): string
{
    if ($post['status'] === 'published' && $post['published_at'] && strtotime($post['published_at']) > time()) {
        return 'scheduled';
    }
    return $post['status'];
}

function pagination(int $page, int $pages, array $query = []): string
{
    if ($pages <= 1) {
        return '';
    }
    $html = '<nav class="pager">';
    for ($i = 1; $i <= $pages; $i++) {
        $q = http_build_query(array_merge($query, ['page' => $i]));
        $html .= '<a href="?' . e($q) . '" class="' . ($i === $page ? 'active' : '') . '">' . $i . '</a>';
    }
    return $html . '</nav>';
}

/** Hidden-form delete button with confirmation. */
function delete_button(string $action, int $id, string $what): string
{
    return '<form method="post" action="' . e($action) . '" class="inline" data-confirm="Delete this ' . e($what) . '? This cannot be undone.">'
        . csrf_field()
        . '<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' . $id . '">'
        . '<button class="btn btn-ghost btn-danger btn-sm">Delete</button></form>';
}
