<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

require_admin();
if (!FEATURE_PROJECTS) {
    redirect('index.php');
}
$pdo = db();

if (is_post()) {
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        flash('error', 'That project no longer exists.');
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM projects WHERE id = ?')->execute([$id]);
        delete_upload($project['cover_image']);
        flash('success', '“' . $project['title'] . '” was deleted.');
    } elseif ($action === 'toggle_publish') {
        $pdo->prepare('UPDATE projects SET is_published = 1 - is_published WHERE id = ?')->execute([$id]);
        flash('success', '“' . $project['title'] . '” is now ' . ($project['is_published'] ? 'hidden from' : 'shown on') . ' the website.');
    } elseif ($action === 'toggle_featured') {
        $pdo->prepare('UPDATE projects SET featured = 1 - featured WHERE id = ?')->execute([$id]);
        flash('success', '“' . $project['title'] . '” ' . ($project['featured'] ? 'removed from' : 'added to') . ' featured projects.');
    }
    redirect('projects.php');
}

$filter = $_GET['status'] ?? 'all';
$statuses = ['all' => 'All', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'planned' => 'Planned', 'on_hold' => 'On hold'];
if (!isset($statuses[$filter])) {
    $filter = 'all';
}
$sql = 'SELECT id, title, slug, client, location, status, featured, is_published, cover_image, start_date, end_date FROM projects';
$params = [];
if ($filter !== 'all') {
    $sql .= ' WHERE status = ?';
    $params[] = $filter;
}
$sql .= ' ORDER BY featured DESC, sort_order ASC, COALESCE(end_date, start_date, created_at) DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

admin_header('Projects', 'projects', [
    'actions' => '<a class="btn btn-brand" href="project_edit.php">+ New project</a>',
]);
?>
<div class="toolbar">
  <nav class="tabs" aria-label="Filter by status">
    <?php foreach ($statuses as $key => $label): ?>
      <a href="?status=<?= e($key) ?>" class="<?= $filter === $key ? 'active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
  </nav>
</div>

<section class="card">
  <?php if (!$projects): ?>
    <div class="empty"><strong>No projects<?= $filter !== 'all' ? ' with this status' : ' yet' ?></strong>Projects you add here appear on the website’s Projects page.<br><br><a class="btn btn-brand" href="project_edit.php">Add a project</a></div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th class="hide-sm"></th><th>Project</th><th>Status</th><th class="hide-sm">Website</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($projects as $p): ?>
        <tr>
          <td class="hide-sm" style="width:76px"><?php if ($p['cover_image']): ?><img class="thumb" src="<?= e(upload_url($p['cover_image'])) ?>" alt=""><?php else: ?><div class="thumb"></div><?php endif; ?></td>
          <td>
            <a class="title-link" href="project_edit.php?id=<?= (int) $p['id'] ?>"><?= e($p['title']) ?></a><?= $p['featured'] ? ' <span class="badge badge-ongoing">Featured</span>' : '' ?>
            <div class="sub"><?= e(implode(' · ', array_filter([$p['client'], $p['location']]))) ?></div>
          </td>
          <td><?= status_badge($p['status']) ?></td>
          <td class="hide-sm"><?= $p['is_published'] ? '<span class="badge badge-published">Visible</span>' : '<span class="badge">Hidden</span>' ?></td>
          <td class="actions">
            <form method="post" class="inline">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
              <button class="btn btn-ghost btn-sm" name="action" value="toggle_publish"><?= $p['is_published'] ? 'Hide' : 'Show' ?></button>
              <button class="btn btn-ghost btn-sm hide-sm" name="action" value="toggle_featured"><?= $p['featured'] ? 'Unfeature' : 'Feature' ?></button>
            </form>
            <a class="btn btn-ghost btn-sm" href="project_edit.php?id=<?= (int) $p['id'] ?>">Edit</a>
            <?= delete_button('projects.php', (int) $p['id'], 'project') ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</section>
<?php admin_footer();
