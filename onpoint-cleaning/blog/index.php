<?php
/** Hygiene Tips — magazine layout: featured article, thumbnail rows, booking sidebar. */
require __DIR__ . '/../includes/layout.php';

$meta = blog_meta();
$perPage = 8;
$total = public_post_count();
$pages = max(1, (int) ceil($total / $perPage));
$page = min($pages, max(1, (int) ($_GET['page'] ?? 1)));
$posts = public_posts($perPage, ($page - 1) * $perPage);
$base = rtrim(BLOG_PATH, '/') . '/';
$schemaPosts = $posts;
$featured = ($page === 1 && $posts) ? array_shift($posts) : null;

page_head([
    'title' => 'Hygiene Tips & Pest Control Advice | OnPoint Hygiene And Fumigation' . ($page > 1 ? " (Page $page)" : ''),
    'description' => 'Practical cleaning, hygiene and pest-control advice from OnPoint Hygiene And Fumigation Zimbabwe.',
    'path' => $base . ($page > 1 ? "?page=$page" : ''),
    'prev' => $page > 1 ? $base . ($page > 2 ? '?page=' . ($page - 1) : '') : null,
    'next' => $page < $pages ? $base . '?page=' . ($page + 1) : null,
    'nodes' => seo_blog_nodes($schemaPosts, SITE_URL . $base, [['Home', '/'], [blog_meta()['name'], $base]]),
]);
page_header('blog');

function hygiene_sidebar(): void
{
    ?>
    <aside class="mag-aside">
      <div class="aside-card aside-book">
        <div class="card-icon" aria-hidden="true">✨</div>
        <h3>Need a professional clean?</h3>
        <p>Free consultation and quote for homes, offices and industrial sites.</p>
        <a class="btn btn-white btn-block" href="/#contact">Book Appointment →</a>
      </div>
      <div class="aside-card aside-emergency">
        <div class="card-icon" aria-hidden="true">🐜</div>
        <h3>Pest problem?</h3>
        <p>Emergency pest control 24/7</p>
        <p class="emergency-num"><a href="tel:+263782381986">0782381986</a><br><a href="tel:+263712734834">0712734834</a></p>
      </div>
      <div class="aside-card">
        <h4>Our services</h4>
        <ul class="aside-links">
          <li><a href="/#services">Cleaning services</a></li>
          <li><a href="/#hygiene">Restroom &amp; workplace hygiene</a></li>
          <li><a href="/#pest-control">Pest control &amp; fumigation</a></li>
        </ul>
      </div>
    </aside>
    <?php
}
?>
<section class="page-hero">
  <div class="container">
    <span class="tag tag-green"><?= e($meta['eyebrow']) ?></span>
    <h1><?= $meta['h1'] /* trusted markup from layout.php */ ?></h1>
    <p class="lead"><?= e($meta['lead']) ?></p>
  </div>
</section>

<section class="section">
  <div class="container mag">
    <div class="mag-main">
      <?php if (!$featured && !$posts): ?>
        <div class="empty-state"><h2><?= e($meta['empty']) ?></h2><p class="muted">Meanwhile, see <a href="/#services" class="c-green">what we can do for you</a>.</p></div>
      <?php endif; ?>

      <?php if ($featured): $href = post_path($featured['slug']); ?>
        <article class="mag-feature card<?= $featured['cover_image'] ? '' : ' no-img' ?>">
          <?php if ($featured['cover_image']): ?>
            <a href="<?= e($href) ?>" class="mag-feature-img" tabindex="-1" aria-hidden="true"><img src="<?= e(upload_url($featured['cover_image'])) ?>" alt="" fetchpriority="high"></a>
          <?php endif; ?>
          <div class="mag-feature-body">
            <span class="tag tag-orange">LATEST TIP</span>
            <h2><a href="<?= e($href) ?>"><?= e($featured['title']) ?></a></h2>
            <p class="muted"><?= e(post_summary($featured)) ?></p>
            <p class="sm muted"><time datetime="<?= e(date('Y-m-d', strtotime($featured['published_at']))) ?>"><?= e(format_post_date($featured['published_at'])) ?></time> · <?= reading_minutes($featured['body_html']) ?> min read</p>
            <a class="btn btn-primary btn-sm" href="<?= e($href) ?>">Read the tip →</a>
          </div>
        </article>
      <?php endif; ?>

      <?php if ($posts): ?>
        <div class="mag-list">
          <?php foreach ($posts as $p): $href = post_path($p['slug']); ?>
            <article class="mag-row">
              <a href="<?= e($href) ?>" class="mag-thumb" tabindex="-1" aria-hidden="true">
                <?php if ($p['cover_image']): ?><img src="<?= e(upload_url($p['cover_image'])) ?>" alt="" loading="lazy"><?php else: ?><span>🧼</span><?php endif; ?>
              </a>
              <div>
                <p class="sm muted"><time datetime="<?= e(date('Y-m-d', strtotime($p['published_at']))) ?>"><?= e(format_post_date($p['published_at'])) ?></time> · <?= reading_minutes($p['body_html']) ?> min read</p>
                <h3><a href="<?= e($href) ?>"><?= e($p['title']) ?></a></h3>
                <p class="muted"><?= e(post_summary($p)) ?></p>
                <a class="read-more" href="<?= e($href) ?>">Read more →</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($pages > 1): ?>
        <nav class="pager" aria-label="Tip pages">
          <?php if ($page > 1): ?><a href="<?= e($base . ($page > 2 ? '?page=' . ($page - 1) : '')) ?>" rel="prev">← Newer</a><?php endif; ?>
          <span>Page <?= $page ?> of <?= $pages ?></span>
          <?php if ($page < $pages): ?><a href="<?= e($base . '?page=' . ($page + 1)) ?>" rel="next">Older →</a><?php endif; ?>
        </nav>
      <?php endif; ?>
    </div>
    <?php hygiene_sidebar(); ?>
  </div>
</section>
<?php page_footer();
