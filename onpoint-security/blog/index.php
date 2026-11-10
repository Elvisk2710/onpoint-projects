<?php
/** Security Insights — bulletin layout: dark masthead, featured advisory, tagged cards, CTA band. */
require __DIR__ . '/../includes/layout.php';

$meta = blog_meta();
$perPage = 9;
$total = public_post_count();
$pages = max(1, (int) ceil($total / $perPage));
$page = min($pages, max(1, (int) ($_GET['page'] ?? 1)));
$posts = public_posts($perPage, ($page - 1) * $perPage);
$base = rtrim(BLOG_PATH, '/') . '/';
$schemaPosts = $posts;
$featured = ($page === 1 && $posts) ? array_shift($posts) : null;

page_head([
    'title' => 'Security Insights & Advisories | ShieldTrac Security Zimbabwe' . ($page > 1 ? " (Page $page)" : ''),
    'description' => 'Security advisories, practical tips and industry news from ShieldTrac Security Zimbabwe.',
    'path' => $base . ($page > 1 ? "?page=$page" : ''),
    'prev' => $page > 1 ? $base . ($page > 2 ? '?page=' . ($page - 1) : '') : null,
    'next' => $page < $pages ? $base . '?page=' . ($page + 1) : null,
    'nodes' => seo_blog_nodes($schemaPosts, SITE_URL . $base, [['Home', '/'], [blog_meta()['name'], $base]]),
]);
page_header('blog');
?>
<section class="bulletin-head">
  <div class="container">
    <span class="badge"><?= e($meta['eyebrow']) ?></span>
    <h1><?= $meta['h1'] /* trusted markup from layout.php */ ?></h1>
    <p class="lead"><?= e($meta['lead']) ?></p>
  </div>
</section>

<section class="section bulletin">
  <div class="container">
    <?php if (!$featured && !$posts): ?>
      <div class="empty-state"><h2><?= e($meta['empty']) ?></h2><p class="muted">Need security now? <a class="c-blue" href="/#contact">Talk to our team</a>.</p></div>
    <?php endif; ?>

    <?php if ($featured): $href = post_path($featured['slug']); ?>
      <article class="bulletin-feature" <?= $featured['cover_image'] ? 'style="--bg:url(\'' . e(upload_url($featured['cover_image'])) . '\')"' : '' ?>>
        <div class="bulletin-feature-body">
          <span class="tag tag-orange">LATEST ADVISORY</span>
          <h2><a href="<?= e($href) ?>"><?= e($featured['title']) ?></a></h2>
          <p><?= e(post_summary($featured)) ?></p>
          <p class="sm"><time datetime="<?= e(date('Y-m-d', strtotime($featured['published_at']))) ?>"><?= e(format_post_date($featured['published_at'])) ?></time> · <?= reading_minutes($featured['body_html']) ?> min read</p>
          <a class="btn btn-light" href="<?= e($href) ?>">Read advisory →</a>
        </div>
      </article>
    <?php endif; ?>

    <?php if ($posts): ?>
      <div class="cards cards-3">
        <?php foreach ($posts as $p): $href = post_path($p['slug']); ?>
          <article class="card advisory-card">
            <?php if ($p['cover_image']): ?><a href="<?= e($href) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(upload_url($p['cover_image'])) ?>" alt="" loading="lazy"></a><?php endif; ?>
            <div class="advisory-body">
              <span class="advisory-tag">SECURITY INSIGHT</span>
              <h3><a href="<?= e($href) ?>"><?= e($p['title']) ?></a></h3>
              <p class="muted"><?= e(post_summary($p)) ?></p>
              <p class="sm muted advisory-meta"><time datetime="<?= e(date('Y-m-d', strtotime($p['published_at']))) ?>"><?= e(format_post_date($p['published_at'])) ?></time> · <?= reading_minutes($p['body_html']) ?> min</p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($pages > 1): ?>
      <nav class="pager" aria-label="Insight pages">
        <?php if ($page > 1): ?><a href="<?= e($base . ($page > 2 ? '?page=' . ($page - 1) : '')) ?>" rel="prev">← Newer</a><?php endif; ?>
        <span>Page <?= $page ?> of <?= $pages ?></span>
        <?php if ($page < $pages): ?><a href="<?= e($base . '?page=' . ($page + 1)) ?>" rel="next">Older →</a><?php endif; ?>
      </nav>
    <?php endif; ?>
  </div>
</section>

<section class="cta-band">
  <div class="container">
    <h2>Ready to Secure Your Business?</h2>
    <p>Contact us today for a free security assessment and customized quote</p>
    <div class="actions">
      <a class="btn btn-light" href="/#contact">📞 Get Free Quote</a>
      <a class="btn btn-ghost-light" href="tel:+263788094724">Call +263 78 809 4724</a>
    </div>
  </div>
</section>
<?php page_footer();
