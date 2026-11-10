<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

/** Names and intro copy for this site's blog section. */
function blog_meta(): array
{
    return [
        'nav' => 'Updates',
        'name' => 'Project Updates',
        'eyebrow' => 'Project Updates',
        'h1' => 'News and progress from <span class="hl">our sites.</span>',
        'lead' => 'Milestones, progress reports and lessons from the roads, water, sewer and building projects we are delivering.',
        'empty' => 'No updates yet. Check back soon for progress from our sites.',
    ];
}

function nav_items(): array
{
    return [
        'home' => ['/', 'Home'],
        'about' => ['/pages/about.php', 'About'],
        'projects' => [rtrim(PROJECTS_PATH, '/') . '/', 'Projects'],
        'blog' => [rtrim(BLOG_PATH, '/') . '/', blog_meta()['nav']],
        'contact' => ['/pages/contact.php', 'Contact'],
    ];
}

/**
 * Opens the HTML document. SEO/social tags and structured data come from seo_head() (src/seo.php).
 *
 * @param array $m title, description, path, image, image_alt, image_w, image_h, type, noindex, nodes, published, modified, section, prev, next, extra_head
 */
function page_head(array $m): void
{
    ?>
<!DOCTYPE html>
<html lang="en-ZW">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($m['title']) ?></title>
<?php seo_head($m); ?>
  <meta name="theme-color" content="#0f1d2e">
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css?v=3">
<?= $m['extra_head'] ?? '' ?>
</head>
<body>
<?php
}

function page_header(string $active = ''): void
{
    ?>
  <header class="site-header">
    <div class="container nav">
      <a href="/" class="logo"><span class="logo-mark">O</span>OnPoint <span class="sub">Civils</span></a>
      <button class="menu-toggle" aria-label="Open menu" aria-expanded="false"><span></span><span></span><span></span></button>
      <ul class="nav-links">
        <?php foreach (nav_items() as $key => [$href, $label]): ?>
          <li><a href="<?= e($href) ?>" <?= $key === $active ? 'class="active" aria-current="page"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
        <li><a href="tel:+263782381986" class="btn">Call +263 78 238 1986</a></li>
      </ul>
    </div>
  </header>
  <main id="main">
<?php
}

function page_footer(): void
{
    ?>
  </main>
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-about">
          <a href="/" class="logo"><span class="logo-mark">O</span>OnPoint <span class="sub">Civils</span></a>
          <p>Civil engineering and construction.</p>
          <p style="margin-top:10px">1 Ambrose, 86 Chinamano Avenue</p>
        </div>
        <div><h5>Pages</h5><ul>
          <?php foreach (nav_items() as [$href, $label]): ?><li><a href="<?= e($href) ?>"><?= e($label) ?></a></li><?php endforeach; ?>
        </ul></div>
        <div><h5>Phone</h5><ul><li><a href="tel:+263782381986">+263 78 238 1986</a></li><li><a href="tel:+263788094724">+263 78 809 4724</a></li><li><a href="tel:+263712734834">+263 71 273 4834</a></li><li><a href="tel:+263242328474">242 328474/5</a></li></ul></div>
        <div><h5>WhatsApp</h5><ul><li><a href="https://wa.me/263782381986" target="_blank" rel="noopener">+263 78 238 1986</a></li><li><a href="https://wa.me/263788094724" target="_blank" rel="noopener">+263 78 809 4724</a></li></ul></div>
        <div><h5>Email</h5><ul><li><a href="mailto:info@onpointcivils.co.zw">info@onpointcivils.co.zw</a></li><li><a href="mailto:richard@onpointcivils.co.zw">richard@onpointcivils.co.zw</a></li><li><a href="mailto:sales@onpointcivils.co.zw">sales@onpointcivils.co.zw</a></li></ul></div>
      </div>
      <div class="footer-bottom">© <span data-year><?= date('Y') ?></span> OnPoint Civils</div>
    </div>
  </footer>
  <script src="/assets/js/main.js?v=2"></script>
</body>
</html>
<?php
}

function format_post_date(?string $d): string
{
    return $d ? date('j F Y', strtotime($d)) : '';
}

function project_status_label(string $s): string
{
    return ['planned' => 'Planned', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On hold'][$s] ?? ucfirst($s);
}
