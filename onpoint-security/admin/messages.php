<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

require_admin();
$pdo = db();

if (is_post()) {
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
        flash('success', 'Message deleted.');
    } elseif (in_array($action, ['new', 'read', 'archived'], true)) {
        $pdo->prepare('UPDATE contact_messages SET status = ? WHERE id = ?')->execute([$action, $id]);
    } elseif ($action === 'read_all') {
        $pdo->exec("UPDATE contact_messages SET status = 'read' WHERE status = 'new'");
        flash('success', 'All messages marked as read.');
    }
    redirect('messages.php?' . http_build_query(array_intersect_key($_GET, array_flip(['view', 'page']))));
}

$view = ($_GET['view'] ?? 'inbox') === 'archived' ? 'archived' : 'inbox';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 25;
$where = $view === 'archived' ? "status = 'archived'" : "status <> 'archived'";
$total = (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE $where")->fetchColumn();
$pages = (int) ceil($total / $perPage);
$messages = $pdo->query(
    "SELECT * FROM contact_messages WHERE $where ORDER BY created_at DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage)
)->fetchAll();

$actions = new_message_count() > 0
    ? '<form method="post" class="inline">' . csrf_field() . '<button class="btn btn-ghost" name="action" value="read_all">Mark all as read</button></form>'
    : '';
admin_header('Messages', 'messages', ['actions' => $actions]);
?>
<div class="toolbar">
  <nav class="tabs">
    <a href="?view=inbox" class="<?= $view === 'inbox' ? 'active' : '' ?>">Inbox</a>
    <a href="?view=archived" class="<?= $view === 'archived' ? 'active' : '' ?>">Archived</a>
  </nav>
</div>

<section class="card">
  <?php if (!$messages): ?>
    <div class="empty"><strong><?= $view === 'archived' ? 'Nothing archived' : 'Inbox is empty' ?></strong>Messages sent through the website’s contact form appear here.</div>
  <?php else: ?>
    <div class="msg-list">
      <?php foreach ($messages as $m): ?>
        <article class="msg <?= $m['status'] === 'new' ? 'is-new' : '' ?>">
          <div>
            <strong><?= e($m['name']) ?></strong> <?= $m['status'] === 'new' ? status_badge('new') : '' ?>
            <div class="msg-meta">
              <?php if ($m['email']): ?><a href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . ($m['subject'] ?: 'Your enquiry')) ?>"><?= e($m['email']) ?></a><?php endif; ?>
              <?php if ($m['phone']): ?> · <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $m['phone'])) ?>"><?= e($m['phone']) ?></a><?php endif; ?>
              · <?= format_date($m['created_at'], 'j M Y, H:i') ?>
            </div>
            <?php if ($m['subject']): ?><div style="font-weight:600;margin-top:6px"><?= e($m['subject']) ?></div><?php endif; ?>
          </div>
          <div class="msg-actions">
            <form method="post" class="inline">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
              <?php if ($m['status'] === 'new'): ?>
                <button class="btn btn-ghost btn-sm" name="action" value="read">Mark read</button>
              <?php elseif ($m['status'] === 'read'): ?>
                <button class="btn btn-ghost btn-sm" name="action" value="new">Mark unread</button>
              <?php endif; ?>
              <?php if ($m['status'] !== 'archived'): ?>
                <button class="btn btn-ghost btn-sm" name="action" value="archived">Archive</button>
              <?php else: ?>
                <button class="btn btn-ghost btn-sm" name="action" value="read">Restore</button>
              <?php endif; ?>
            </form>
            <?= delete_button('messages.php?view=' . $view, (int) $m['id'], 'message') ?>
          </div>
          <div class="msg-body"><?= e($m['message']) ?></div>
        </article>
      <?php endforeach; ?>
    </div>
    <?= pagination($page, $pages, ['view' => $view]) ?>
  <?php endif; ?>
</section>
<?php admin_footer();
