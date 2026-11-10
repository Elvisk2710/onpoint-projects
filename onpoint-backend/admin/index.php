<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

$admin = require_admin();
$pdo = db();

$postCounts = ['published' => 0, 'draft' => 0, 'scheduled' => 0];
foreach ($pdo->query("SELECT
        SUM(status = 'published' AND (published_at IS NULL OR published_at <= NOW())) AS published,
        SUM(status = 'published' AND published_at > NOW()) AS scheduled,
        SUM(status = 'draft') AS draft
    FROM posts")->fetch() as $k => $v) {
    $postCounts[$k] = (int) $v;
}

$messages = [
    'new' => new_message_count(),
    'week' => (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE created_at > NOW() - INTERVAL 7 DAY')->fetchColumn(),
];

$projects = null;
if (FEATURE_PROJECTS) {
    $projects = $pdo->query("SELECT COUNT(*) AS total, SUM(status = 'ongoing') AS ongoing, SUM(status = 'completed') AS completed FROM projects")->fetch();
}

$recentPosts = $pdo->query('SELECT id, title, status, published_at, updated_at FROM posts ORDER BY updated_at DESC LIMIT 6')->fetchAll();
$recentMessages = $pdo->query("SELECT id, name, subject, message, status, created_at FROM contact_messages WHERE status <> 'archived' ORDER BY created_at DESC LIMIT 5")->fetchAll();

admin_header('Dashboard', 'dashboard', [
    'actions' => '<a class="btn btn-brand" href="post_edit.php">+ New post</a>',
]);
?>
<p class="sub" style="margin:0 0 20px">Welcome back, <?= e(explode(' ', $admin['name'])[0]) ?>.</p>

<section class="stats">
  <div class="card stat">
    <div class="stat-label">Published posts</div>
    <div class="stat-value"><?= $postCounts['published'] ?></div>
    <div class="stat-sub"><?= $postCounts['scheduled'] ?> scheduled</div>
  </div>
  <div class="card stat">
    <div class="stat-label">Drafts</div>
    <div class="stat-value"><?= $postCounts['draft'] ?></div>
    <div class="stat-sub"><a href="posts.php?status=draft">View drafts</a></div>
  </div>
  <?php if ($projects): ?>
  <div class="card stat">
    <div class="stat-label">Projects</div>
    <div class="stat-value"><?= (int) $projects['total'] ?></div>
    <div class="stat-sub"><?= (int) $projects['ongoing'] ?> ongoing · <?= (int) $projects['completed'] ?> completed</div>
  </div>
  <?php endif; ?>
  <div class="card stat">
    <div class="stat-label">New messages</div>
    <div class="stat-value"><?= $messages['new'] ?></div>
    <div class="stat-sub"><?= $messages['week'] ?> in the last 7 days</div>
  </div>
</section>

<div class="grid-2">
  <section class="card">
    <div class="card-head"><h2>Recently edited posts</h2><a class="btn btn-ghost btn-sm" href="posts.php">All posts</a></div>
    <?php if (!$recentPosts): ?>
      <div class="empty"><strong>No posts yet</strong>Write your first post to get the blog going.<br><br><a class="btn btn-brand" href="post_edit.php">Write a post</a></div>
    <?php else: ?>
      <div class="table-wrap"><table>
        <tbody>
        <?php foreach ($recentPosts as $p): ?>
          <tr>
            <td><a class="title-link" href="post_edit.php?id=<?= (int) $p['id'] ?>"><?= e($p['title']) ?></a><div class="sub">Edited <?= format_date($p['updated_at'], 'j M, H:i') ?></div></td>
            <td class="actions"><?= status_badge(post_state($p)) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    <?php endif; ?>
  </section>

  <section class="card">
    <div class="card-head"><h2>Latest messages</h2><a class="btn btn-ghost btn-sm" href="messages.php">Inbox</a></div>
    <?php if (!$recentMessages): ?>
      <div class="empty"><strong>No messages</strong>Contact form submissions appear here.</div>
    <?php else: ?>
      <div class="table-wrap"><table>
        <tbody>
        <?php foreach ($recentMessages as $m): ?>
          <tr>
            <td><strong><?= e($m['name']) ?></strong><div class="sub"><?= e(excerpt_from_html($m['subject'] ?: $m['message'], 70)) ?></div></td>
            <td class="actions"><?= status_badge($m['status']) ?><div class="sub"><?= format_date($m['created_at'], 'j M') ?></div></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    <?php endif; ?>
  </section>
</div>
<?php admin_footer();
