<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

require_admin();
$pdo = db();

if (is_post()) {
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) {
        flash('error', 'That post no longer exists.');
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
        delete_upload($post['cover_image']);
        flash('success', '“' . $post['title'] . '” was deleted.');
    } elseif ($action === 'publish') {
        $pdo->prepare("UPDATE posts SET status = 'published', published_at = COALESCE(published_at, NOW()) WHERE id = ?")->execute([$id]);
        flash('success', '“' . $post['title'] . '” is now live.');
    } elseif ($action === 'unpublish') {
        $pdo->prepare("UPDATE posts SET status = 'draft' WHERE id = ?")->execute([$id]);
        flash('success', '“' . $post['title'] . '” was moved to drafts.');
    }
    redirect('posts.php?' . http_build_query(array_intersect_key($_GET, array_flip(['status', 'q', 'page']))));
}

$status = $_GET['status'] ?? 'all';
$q = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;

$where = [];
$params = [];
if ($status === 'published') {
    $where[] = "status = 'published' AND (published_at IS NULL OR published_at <= NOW())";
} elseif ($status === 'scheduled') {
    $where[] = "status = 'published' AND published_at > NOW()";
} elseif ($status === 'draft') {
    $where[] = "status = 'draft'";
}
if ($q !== '') {
    $where[] = '(title LIKE ? OR excerpt LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$count = $pdo->prepare("SELECT COUNT(*) FROM posts $whereSql");
$count->execute($params);
$total = (int) $count->fetchColumn();
$pages = (int) ceil($total / $perPage);

$stmt = $pdo->prepare(
    "SELECT p.id, p.title, p.slug, p.status, p.published_at, p.updated_at, p.cover_image, a.name AS author
     FROM posts p LEFT JOIN admins a ON a.id = p.author_id
     $whereSql ORDER BY COALESCE(p.published_at, p.updated_at) DESC
     LIMIT $perPage OFFSET " . (($page - 1) * $perPage)
);
$stmt->execute($params);
$posts = $stmt->fetchAll();

$tabs = ['all' => 'All', 'published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Drafts'];

admin_header('Blog posts', 'posts', [
    'actions' => '<a class="btn btn-brand" href="post_edit.php">+ New post</a>',
]);
?>
<div class="toolbar">
  <nav class="tabs" aria-label="Filter by status">
    <?php foreach ($tabs as $key => $label): ?>
      <a href="?<?= e(http_build_query(['status' => $key, 'q' => $q])) ?>" class="<?= $status === $key ? 'active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
  </nav>
  <form class="search" method="get">
    <input type="hidden" name="status" value="<?= e($status) ?>">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search posts" aria-label="Search posts">
    <button class="btn btn-ghost">Search</button>
  </form>
</div>

<section class="card">
  <?php if (!$posts): ?>
    <div class="empty">
      <?php if ($q !== '' || $status !== 'all'): ?>
        <strong>No posts match</strong>Try a different filter or search.
      <?php else: ?>
        <strong>No posts yet</strong>Posts you publish here appear on the website’s blog.<br><br><a class="btn btn-brand" href="post_edit.php">Write your first post</a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th class="hide-sm"></th><th>Title</th><th>Status</th><th class="hide-sm">Date</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($posts as $p): $state = post_state($p); ?>
        <tr>
          <td class="hide-sm" style="width:76px"><?php if ($p['cover_image']): ?><img class="thumb" src="<?= e(upload_url($p['cover_image'])) ?>" alt=""><?php else: ?><div class="thumb"></div><?php endif; ?></td>
          <td>
            <a class="title-link" href="post_edit.php?id=<?= (int) $p['id'] ?>"><?= e($p['title']) ?></a>
            <div class="sub">/<?= e($p['slug']) ?><?= $p['author'] ? ' · ' . e($p['author']) : '' ?></div>
          </td>
          <td><?= status_badge($state) ?></td>
          <td class="hide-sm sub"><?= $state === 'draft' ? 'Edited ' . format_date($p['updated_at']) : format_date($p['published_at']) ?></td>
          <td class="actions">
            <form method="post" class="inline">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
              <?php if ($p['status'] === 'draft'): ?>
                <button class="btn btn-ghost btn-sm" name="action" value="publish">Publish</button>
              <?php else: ?>
                <button class="btn btn-ghost btn-sm" name="action" value="unpublish">Unpublish</button>
              <?php endif; ?>
            </form>
            <a class="btn btn-ghost btn-sm" href="post_edit.php?id=<?= (int) $p['id'] ?>">Edit</a>
            <?= delete_button('posts.php', (int) $p['id'], 'post') ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?= pagination($page, $pages, ['status' => $status, 'q' => $q]) ?>
  <?php endif; ?>
</section>
<?php admin_footer();
