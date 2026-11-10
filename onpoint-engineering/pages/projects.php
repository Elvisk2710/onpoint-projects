<?php
require __DIR__ . '/../includes/layout.php';

$filters = ['all' => 'All projects', 'completed' => 'Completed', 'ongoing' => 'Ongoing', 'planned' => 'Planned'];
$filter = $_GET['status'] ?? 'all';
if (!isset($filters[$filter])) {
    $filter = 'all';
}
$projects = public_projects(60, false, $filter === 'all' ? '' : $filter);
$base = rtrim(PROJECTS_PATH, '/') . '/';

page_head([
    'title' => 'Projects | OnPoint Civils',
    'description' => 'Roads, structures, water and sewer infrastructure and buildings delivered by OnPoint Civils.',
    'path' => $base,
]);
page_header('projects');
?>
  <section class="page-hero">
    <div class="container">
      <span class="label" style="color:var(--yellow)">Our Portfolio</span>
      <h1>Infrastructure that communities <span class="hl">depend on.</span></h1>
      <p class="lead">A selection of the roads, water and sewer systems, structures and buildings we have constructed, each delivered safely and built to last.</p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <nav class="filters" aria-label="Filter projects by status">
        <?php foreach ($filters as $key => $label): ?>
          <a href="<?= e($base . ($key === 'all' ? '' : '?status=' . $key)) ?>" class="filter <?= $filter === $key ? 'active' : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </nav>

      <?php if (!$projects): ?>
        <div class="empty-state">
          <h2>No projects to show yet</h2>
          <p class="muted">We’re adding our project portfolio. In the meantime, call or WhatsApp us to discuss your project.</p>
          <p><a href="/pages/contact.php" class="btn">Get in touch</a></p>
        </div>
      <?php else: ?>
        <div class="projects-grid">
          <?php foreach ($projects as $p): $href = project_path($p['slug']); ?>
            <a class="project-card" href="<?= e($href) ?>">
              <div class="project-visual">
                <?php if ($p['cover_image']): ?>
                  <img src="<?= e(upload_url($p['cover_image'])) ?>" alt="<?= e($p['cover_alt'] ?: $p['title']) ?>" loading="lazy">
                <?php else: ?>
                  <span class="project-placeholder" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($p['title'], 0, 1))) ?></span>
                <?php endif; ?>
                <span class="status status-<?= e($p['status']) ?>"><i></i><?= e(project_status_label($p['status'])) ?></span>
              </div>
              <div class="project-meta">
                <?php if ($p['category']): ?><span class="cat"><?= e($p['category']) ?></span><?php endif; ?>
                <h3><?= e($p['title']) ?></h3>
                <p class="muted"><?= e($p['summary'] ?: excerpt_from_html($p['body_html'] ?? '', 120)) ?></p>
                <?php if ($p['location']): ?><span class="loc">📍 <?= e($p['location']) ?></span><?php endif; ?>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <div>
        <h2>Let’s discuss how we can bring your project to life.</h2>
        <p>Send us your drawings or call for a quote.</p>
      </div>
      <div style="display:flex;gap:14px;flex-wrap:wrap">
        <a href="/pages/contact.php" class="btn btn-dark">Get in touch →</a>
        <a href="https://wa.me/263782381986" target="_blank" rel="noopener" class="btn btn-dark">WhatsApp us</a>
      </div>
    </div>
  </section>
<?php page_footer();
