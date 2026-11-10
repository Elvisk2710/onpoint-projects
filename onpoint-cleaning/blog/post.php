<?php
/** A single hygiene tip: article + sticky booking sidebar + "keep reading" cards. */
require __DIR__ . '/../includes/layout.php';

$slug = (string) ($_GET['slug'] ?? '');
$post = preg_match('/^[a-z0-9-]{1,190}$/', $slug) ? public_post($slug) : null;
$base = rtrim(BLOG_PATH, '/') . '/';

if (!$post) {
    redirect_if_renamed('post', $slug);
    http_response_code(404);
    page_head(['title' => 'Tip not found | OnPoint Hygiene And Fumigation', 'description' => 'This article could not be found.', 'noindex' => true]);
    page_header('blog');
    ?>
    <section class="section"><div class="container narrow center-text">
      <h1>Article not found</h1>
      <p class="muted">It may have been moved or unpublished.</p>
      <p><a class="btn btn-primary" href="<?= e($base) ?>">Back to hygiene tips</a></p>
    </div></section>
    <?php
    page_footer();
    exit;
}

$older = adjacent_post($post, '<');
$newer = adjacent_post($post, '>');
$related = array_slice(array_filter(public_posts(4), fn ($p) => (int) $p['id'] !== (int) $post['id']), 0, 3);
$desc = $post['seo_description'] ?: post_summary($post);
$image = $post['cover_image'] ? upload_url($post['cover_image']) : null;
$url = SITE_URL . post_path($post['slug']);

$crumbs = [['Home', '/'], [blog_meta()['name'], $base], [$post['title'], post_path($post['slug'])]];
$dims = upload_dims($post['cover_image']);
page_head([
    'title' => ($post['seo_title'] ?: $post['title']) . ' | ' . site_profile()['short_name'],
    'description' => $desc,
    'path' => post_path($post['slug']),
    'image' => $image,
    'image_alt' => $post['cover_alt'] ?: $post['title'],
    'image_w' => $dims['w'] ?? null,
    'image_h' => $dims['h'] ?? null,
    'type' => 'article',
    'published' => date(DATE_ATOM, strtotime($post['published_at'])),
    'modified' => date(DATE_ATOM, strtotime($post['updated_at'])),
    'section' => blog_meta()['name'],
    'nodes' => seo_post_nodes($post, $crumbs),
]);
page_header('blog');
?>
<section class="page-hero post-hero">
  <div class="container narrow">
    <?= render_breadcrumbs([['Home', '/'], [blog_meta()['name'], $base], [$post['title'], null]]) ?>
    <h1><?= e($post['title']) ?></h1>
    <p class="post-meta"><time datetime="<?= e(date('Y-m-d', strtotime($post['published_at']))) ?>"><?= e(format_post_date($post['published_at'])) ?></time> · <?= reading_minutes($post['body_html']) ?> min read<?= render_updated($post) ?></p>
  </div>
</section>

<section class="section post">
  <div class="container mag">
    <article class="mag-main">
      <?php if ($image): ?><img class="post-cover" src="<?= e($image) ?>" alt="<?= e($post['cover_alt'] ?: '') ?>" fetchpriority="high"><?php endif; ?>
      <?= render_takeaways($post) ?>
      <div class="prose"><?= $post['body_html'] /* sanitised on save */ ?></div>
      <?= render_faq($post) ?>

      <div class="callout">
        <div class="card-icon" aria-hidden="true">🧹</div>
        <div>
          <h3>Want this done professionally?</h3>
          <p>Our trained, certified team handles cleaning, hygiene and pest control. Get a free consultation and quote.</p>
          <a class="btn btn-primary btn-sm" href="/#contact">Book Appointment →</a>
        </div>
      </div>

      <?php if ($older || $newer): ?>
        <nav class="post-nav" aria-label="More tips">
          <?php if ($older): ?><a class="prev" href="<?= e(post_path($older['slug'])) ?>" rel="prev"><span>Older</span><?= e($older['title']) ?></a><?php endif; ?>
          <?php if ($newer): ?><a class="next" href="<?= e(post_path($newer['slug'])) ?>" rel="next"><span>Newer</span><?= e($newer['title']) ?></a><?php endif; ?>
        </nav>
      <?php endif; ?>
    </article>

    <aside class="mag-aside">
      <div class="aside-card aside-book">
        <div class="card-icon" aria-hidden="true">✨</div>
        <h3>Need a professional clean?</h3>
        <p>Free consultation and quote.</p>
        <a class="btn btn-white btn-block" href="/#contact">Book Appointment →</a>
      </div>
      <div class="aside-card aside-emergency">
        <div class="card-icon" aria-hidden="true">🐜</div>
        <h3>Pest emergency?</h3>
        <p>Available 24/7</p>
        <p class="emergency-num"><a href="tel:+263782381986">0782381986</a><br><a href="tel:+263712734834">0712734834</a></p>
      </div>
      <?php if ($related): ?>
        <div class="aside-card">
          <h4>Keep reading</h4>
          <ul class="aside-links">
            <?php foreach ($related as $r): ?><li><a href="<?= e(post_path($r['slug'])) ?>"><?= e($r['title']) ?></a></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</section>
<?php page_footer();
