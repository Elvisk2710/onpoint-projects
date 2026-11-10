<?php
/** A single project update — same two-column layout as a project page. */
require __DIR__ . '/../includes/layout.php';

$meta = blog_meta();
$slug = (string) ($_GET['slug'] ?? '');
$post = preg_match('/^[a-z0-9-]{1,190}$/', $slug) ? public_post($slug) : null;
$base = rtrim(BLOG_PATH, '/') . '/';

if (!$post) {
    redirect_if_renamed('post', $slug);
    http_response_code(404);
    page_head(['title' => 'Update not found | OnPoint Civils', 'description' => 'This update could not be found.', 'noindex' => true]);
    page_header('blog');
    ?>
  <section class="page-hero"><div class="container">
    <h1>Update not found</h1>
    <p class="lead">It may have been moved or removed.</p>
    <p style="margin-top:24px"><a class="btn" href="<?= e($base) ?>">All project updates</a></p>
  </div></section>
    <?php
    page_footer();
    exit;
}

$older = adjacent_post($post, '<');
$newer = adjacent_post($post, '>');
$more = array_slice(array_filter(public_posts(5), fn ($p) => (int) $p['id'] !== (int) $post['id']), 0, 3);
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
$waText = rawurlencode($post['title'] . ' — ' . $url);
?>
  <section class="page-hero post-hero">
    <div class="container">
      <?= render_breadcrumbs([['Home', '/'], [blog_meta()['name'], $base], [$post['title'], null]]) ?>
      <h1><?= e($post['title']) ?></h1>
      <p class="lead"><?= e($desc) ?></p>
      <p class="post-meta"><time datetime="<?= e(date('Y-m-d', strtotime($post['published_at']))) ?>"><?= e(format_post_date($post['published_at'])) ?></time> · <?= reading_minutes($post['body_html']) ?> min read<?= render_updated($post) ?></p>
    </div>
  </section>

  <section class="section">
    <div class="container project-detail">
      <article>
        <?php if ($image): ?><img class="post-cover" src="<?= e($image) ?>" alt="<?= e($post['cover_alt'] ?: $post['title']) ?>" fetchpriority="high"><?php endif; ?>
        <?= render_takeaways($post) ?>
      <div class="prose"><?= $post['body_html'] /* sanitised on save */ ?></div>
      <?= render_faq($post) ?>

        <?php if ($older || $newer): ?>
          <nav class="post-nav" aria-label="More updates">
            <?php if ($older): ?><a class="prev" href="<?= e(post_path($older['slug'])) ?>" rel="prev"><span>Older update</span><?= e($older['title']) ?></a><?php endif; ?>
            <?php if ($newer): ?><a class="next" href="<?= e(post_path($newer['slug'])) ?>" rel="next"><span>Newer update</span><?= e($newer['title']) ?></a><?php endif; ?>
          </nav>
        <?php endif; ?>
      </article>

      <aside class="project-facts">
        <h4>Update details</h4>
        <dl>
          <dt>Published</dt><dd><?= e(format_post_date($post['published_at'])) ?></dd>
          <dt>Reading time</dt><dd><?= reading_minutes($post['body_html']) ?> min</dd>
          <?php if (!empty($post['author'])): ?><dt>Posted by</dt><dd><?= e($post['author']) ?></dd><?php endif; ?>
        </dl>
        <a href="/pages/contact.php" class="btn btn-dark">Discuss a similar project</a>
        <p style="margin:14px 0 0"><a class="link-arrow" href="https://wa.me/?text=<?= $waText ?>" target="_blank" rel="noopener">Share on WhatsApp <span>→</span></a></p>

        <?php if ($more): ?>
          <h4 style="margin-top:32px">More updates</h4>
          <ul class="facts-list">
            <?php foreach ($more as $m): ?>
              <li><a href="<?= e(post_path($m['slug'])) ?>"><?= e($m['title']) ?></a><span><?= e(format_post_date($m['published_at'])) ?></span></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <p style="margin:24px 0 0"><a class="link-arrow" href="<?= e(rtrim(PROJECTS_PATH, '/')) ?>/">View all projects <span>→</span></a></p>
      </aside>
    </div>
  </section>
<?php page_footer();
