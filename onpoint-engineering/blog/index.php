<?php
/** Project Updates list — uses the same visual card grid as the Projects page. */
require __DIR__ . '/../includes/layout.php';

$meta = blog_meta();
$perPage = 9;
$total = public_post_count();
$pages = max(1, (int) ceil($total / $perPage));
$page = min($pages, max(1, (int) ($_GET['page'] ?? 1)));
$posts = public_posts($perPage, ($page - 1) * $perPage);
$base = rtrim(BLOG_PATH, '/') . '/';

page_head([
    'title' => $meta['name'] . ' | OnPoint Civils' . ($page > 1 ? " (Page $page)" : ''),
    'description' => 'Progress reports, milestones and news from OnPoint Civils roads, water, sewer and building projects.',
    'path' => $base . ($page > 1 ? "?page=$page" : ''),
    'prev' => $page > 1 ? $base . ($page > 2 ? '?page=' . ($page - 1) : '') : null,
    'next' => $page < $pages ? $base . '?page=' . ($page + 1) : null,
    'nodes' => seo_blog_nodes($posts, SITE_URL . $base, [['Home', '/'], [blog_meta()['name'], $base]]),
]);
page_header('blog');
?>
  <section class="page-hero">
    <div class="container">
      <span class="label" style="color:var(--yellow)"><?= e($meta['eyebrow']) ?></span>
      <h1><?= $meta['h1'] /* trusted markup from layout.php */ ?></h1>
      <p class="lead"><?= e($meta['lead']) ?></p>
      <p style="margin-top:24px"><a class="btn" href="<?= e(rtrim(PROJECTS_PATH, '/')) ?>/">See our projects →</a></p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <?php if (!$posts): ?>
        <div class="empty-state">
          <h2><?= e($meta['empty']) ?></h2>
          <p><a href="<?= e(rtrim(PROJECTS_PATH, '/')) ?>/" class="btn">Browse projects</a></p>
        </div>
      <?php else: ?>
        <div class="projects-grid">
          <?php foreach ($posts as $i => $p): $href = post_path($p['slug']); $wide = ($page === 1 && $i === 0); ?>
            <a class="project-card<?= $wide ? ' wide' : '' ?>" href="<?= e($href) ?>">
              <div class="project-visual">
                <?php if ($p['cover_image']): ?>
                  <img src="<?= e(upload_url($p['cover_image'])) ?>" alt="<?= e($p['cover_alt'] ?: $p['title']) ?>" loading="<?= $i < 2 ? 'eager' : 'lazy' ?>">
                <?php else: ?>
                  <span class="project-placeholder" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($p['title'], 0, 1))) ?></span>
                <?php endif; ?>
                <span class="status status-update"><i></i><?= e(date('j M Y', strtotime($p['published_at']))) ?></span>
              </div>
              <div class="project-meta">
                <span class="cat"><?= $wide ? 'Latest update' : 'Project update' ?></span>
                <h3><?= e($p['title']) ?></h3>
                <p class="muted"><?= e(post_summary($p)) ?></p>
                <span class="loc">⏱ <?= reading_minutes($p['body_html']) ?> min read</span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>

        <?php if ($pages > 1): ?>
          <nav class="pager" aria-label="Update pages">
            <?php if ($page > 1): ?><a href="<?= e($base . ($page > 2 ? '?page=' . ($page - 1) : '')) ?>" rel="prev">← Newer</a><?php endif; ?>
            <span>Page <?= $page ?> of <?= $pages ?></span>
            <?php if ($page < $pages): ?><a href="<?= e($base . '?page=' . ($page + 1)) ?>" rel="next">Older →</a><?php endif; ?>
          </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <div><h2>Have a project in mind?</h2><p>Send us your drawings or call for a quote.</p></div>
      <div style="display:flex;gap:14px;flex-wrap:wrap">
        <a href="/pages/contact.php" class="btn btn-dark">Get in touch →</a>
        <a href="https://wa.me/263782381986" target="_blank" rel="noopener" class="btn btn-dark">WhatsApp us</a>
      </div>
    </div>
  </section>
<?php page_footer();
