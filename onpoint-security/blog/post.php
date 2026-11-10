<?php
/** A single security insight: dark masthead, article, action panel, "more insights". */
require __DIR__ . '/../includes/layout.php';

$slug = (string) ($_GET['slug'] ?? '');
$post = preg_match('/^[a-z0-9-]{1,190}$/', $slug) ? public_post($slug) : null;
$base = rtrim(BLOG_PATH, '/') . '/';

if (!$post) {
    redirect_if_renamed('post', $slug);
    http_response_code(404);
    page_head(['title' => 'Insight not found | ShieldTrac Security', 'description' => 'This article could not be found.', 'noindex' => true]);
    page_header('blog');
    ?>
    <section class="section"><div class="container narrow center-text">
      <h1>Article not found</h1>
      <p class="muted">It may have been moved or unpublished.</p>
      <p><a class="btn btn-primary" href="<?= e($base) ?>">Back to security insights</a></p>
    </div></section>
    <?php
    page_footer();
    exit;
}

$older = adjacent_post($post, '<');
$newer = adjacent_post($post, '>');
$more = array_slice(array_filter(public_posts(4), fn ($p) => (int) $p['id'] !== (int) $post['id']), 0, 3);
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
<section class="bulletin-head post-masthead">
  <div class="container narrow">
    <?= render_breadcrumbs([['Home', '/'], [blog_meta()['name'], $base], [$post['title'], null]]) ?>
    <span class="badge">SECURITY INSIGHT</span>
    <h1><?= e($post['title']) ?></h1>
    <p class="lead post-meta-light"><time datetime="<?= e(date('Y-m-d', strtotime($post['published_at']))) ?>"><?= e(format_post_date($post['published_at'])) ?></time> · <?= reading_minutes($post['body_html']) ?> min read<?= render_updated($post) ?></p>
  </div>
</section>

<article class="section post">
  <div class="container narrow">
    <?php if ($image): ?><img class="post-cover" src="<?= e($image) ?>" alt="<?= e($post['cover_alt'] ?: '') ?>" fetchpriority="high"><?php endif; ?>
    <?= render_takeaways($post) ?>
      <div class="prose"><?= $post['body_html'] /* sanitised on save */ ?></div>
      <?= render_faq($post) ?>

    <aside class="action-panel">
      <div>
        <h3>Protect your people, property and assets</h3>
        <p>Free security assessment and customised quote from a Zimbabwe-approved private security company.</p>
      </div>
      <div class="action-links">
        <a class="btn btn-primary" href="/#contact">Get a Free Quote</a>
        <a class="btn btn-outline" href="tel:+263788094724">📞 +263 78 809 4724</a>
      </div>
    </aside>

    <?php if ($older || $newer): ?>
      <nav class="post-nav" aria-label="More insights">
        <?php if ($older): ?><a class="prev" href="<?= e(post_path($older['slug'])) ?>" rel="prev"><span>Older</span><?= e($older['title']) ?></a><?php endif; ?>
        <?php if ($newer): ?><a class="next" href="<?= e(post_path($newer['slug'])) ?>" rel="next"><span>Newer</span><?= e($newer['title']) ?></a><?php endif; ?>
      </nav>
    <?php endif; ?>
  </div>
</article>

<?php if ($more): ?>
<section class="section bg-light">
  <div class="container">
    <div class="section-head"><h2>More security insights</h2></div>
    <div class="cards cards-3">
      <?php foreach ($more as $p): $href = post_path($p['slug']); ?>
        <article class="card advisory-card">
          <?php if ($p['cover_image']): ?><a href="<?= e($href) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(upload_url($p['cover_image'])) ?>" alt="" loading="lazy"></a><?php endif; ?>
          <div class="advisory-body">
            <span class="advisory-tag">SECURITY INSIGHT</span>
            <h3><a href="<?= e($href) ?>"><?= e($p['title']) ?></a></h3>
            <p class="muted"><?= e(post_summary($p)) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?php page_footer();
