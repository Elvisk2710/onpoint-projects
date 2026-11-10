<?php
require __DIR__ . '/../includes/layout.php';

$slug = (string) ($_GET['slug'] ?? '');
$project = preg_match('/^[a-z0-9-]{1,190}$/', $slug) ? public_project($slug) : null;
$base = rtrim(PROJECTS_PATH, '/') . '/';

if (!$project) {
    http_response_code(404);
    page_head(['title' => 'Project not found | OnPoint Civils', 'description' => 'This project could not be found.', 'noindex' => true]);
    page_header('projects');
    ?>
  <section class="page-hero"><div class="container">
    <h1>Project not found</h1>
    <p class="lead">It may have been moved or removed.</p>
    <p style="margin-top:24px"><a class="btn" href="<?= e($base) ?>">See all projects</a></p>
  </div></section>
    <?php
    page_footer();
    exit;
}

$summary = $project['summary'] ?: excerpt_from_html($project['body_html'] ?? '', 160);
$image = $project['cover_image'] ? upload_url($project['cover_image']) : null;
$facts = array_filter([
    'Client' => $project['client'],
    'Location' => $project['location'],
    'Category' => $project['category'],
    'Status' => project_status_label($project['status']),
    'Started' => $project['start_date'] ? date('F Y', strtotime($project['start_date'])) : null,
    'Completed' => $project['end_date'] ? date('F Y', strtotime($project['end_date'])) : null,
]);

page_head([
    'title' => $project['title'] . ' | OnPoint Civils',
    'description' => $summary,
    'path' => project_path($project['slug']),
    'image' => $image,
]);
page_header('projects');
?>
  <section class="page-hero">
    <div class="container">
      <p class="crumbs"><a href="<?= e($base) ?>">← All projects</a></p>
      <h1><?= e($project['title']) ?></h1>
      <?php if ($summary): ?><p class="lead"><?= e($summary) ?></p><?php endif; ?>
    </div>
  </section>

  <section class="section">
    <div class="container project-detail">
      <div>
        <?php if ($image): ?><img class="post-cover" src="<?= e($image) ?>" alt="<?= e($project['cover_alt'] ?: $project['title']) ?>" fetchpriority="high"><?php endif; ?>
        <div class="prose"><?= $project['body_html'] ?? '' /* sanitised on save */ ?></div>
      </div>
      <aside class="project-facts">
        <h4>Project details</h4>
        <dl>
          <?php foreach ($facts as $k => $v): ?><dt><?= e($k) ?></dt><dd><?= e((string) $v) ?></dd><?php endforeach; ?>
        </dl>
        <a href="/pages/contact.php" class="btn btn-dark">Discuss a similar project</a>
      </aside>
    </div>
  </section>
<?php page_footer();
