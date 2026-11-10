<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

const BRAND = 'Onpoint Hygiene & Fumigation Services';

/** Inline line icons (24px grid, stroke = currentColor). */
function icon(string $name): string
{
    $p = [
        'leaf' => '<path d="M5 19c0-8 5-14 15-15-1 10-7 15-15 15z"/><path d="M5 19l8-8"/>',
        'broom' => '<path d="M14 3l-4 9"/><path d="M6 12h8l2 9H4z"/><path d="M8 16v5M12 16v5"/>',
        'factory' => '<path d="M3 21V10l6 4V10l6 4V5h4v16z"/><path d="M3 21h18"/>',
        'sofa' => '<path d="M4 11V8a2 2 0 012-2h12a2 2 0 012 2v3"/><rect x="2" y="11" width="20" height="6" rx="2"/><path d="M5 17v3M19 17v3"/>',
        'window' => '<rect x="4" y="3" width="16" height="18" rx="1"/><path d="M12 3v18M4 12h16"/>',
        'home' => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
        'sparkle' => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5L18 18M6 18l2.5-2.5M15.5 8.5L18 6"/>',
        'flask' => '<path d="M9 3h6M10 3v6L4 19a1.5 1.5 0 001.3 2h13.4a1.5 1.5 0 001.3-2L14 9V3"/><path d="M7 15h10"/>',
        'bin' => '<path d="M4 7h16M10 3h4M6 7l1 14h10l1-14"/>',
        'car' => '<path d="M5 16l1.5-6h11L19 16"/><rect x="3" y="16" width="18" height="4" rx="1"/><path d="M7 20v1M17 20v1"/>',
        'drop' => '<path d="M12 3s7 7.5 7 12a7 7 0 01-14 0c0-4.5 7-12 7-12z"/>',
        'restroom' => '<circle cx="7" cy="5" r="2"/><circle cx="17" cy="5" r="2"/><path d="M5 9h4v6H8v6H6v-6H5zM15 9h4l1 7h-2v5h-2v-5h-2z"/>',
        'pump' => '<path d="M9 8h6v13H9z"/><path d="M12 8V4h4M16 4v2"/>',
        'gear' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M4.9 19.1L7 17M17 7l2.1-2.1"/>',
        'building' => '<rect x="5" y="3" width="14" height="18" rx="1"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-4h4v4"/>',
        'bug' => '<rect x="8" y="7" width="8" height="12" rx="4"/><path d="M12 7V4M9 4l1.5 2M15 4l-1.5 2M8 11H4M8 15H4M16 11h4M16 15h4"/>',
        'shield' => '<path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
        'team' => '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M16 14c2.8 0 5 2.2 5 5"/>',
        'badge' => '<circle cx="12" cy="10" r="6"/><path d="M9 15l-1 6 4-2 4 2-1-6"/>',
        'check' => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/>',
        'chat' => '<path d="M4 20l1.5-4A8 8 0 1112 20a8 8 0 01-4-1z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'pin' => '<path d="M12 21s-7-6.2-7-12a7 7 0 0114 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    ][$name] ?? '';
    return '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
}

/** Names and intro copy for this site's blog section. */
function blog_meta(): array
{
    return [
        'nav' => 'Hygiene Tips',
        'name' => 'Hygiene Tips',
        'eyebrow' => 'HYGIENE TIPS & ADVICE',
        'h1' => 'Clean spaces, <span class="c-green">healthy</span> people',
        'lead' => 'Practical cleaning, hygiene and pest-control advice from the Onpoint team.',
        'empty' => 'Our first tips are on the way. Check back soon.',
    ];
}

function nav_items(): array
{
    return [
        'about' => ['/#about', 'About'],
        'services' => ['/#services', 'Services'],
        'pest' => ['/#pest-control', 'Pest Control'],
        'clients' => ['/#testimonials', 'Clients'],
        'blog' => [rtrim(BLOG_PATH, '/') . '/', blog_meta()['nav']],
        'contact' => ['/#contact', 'Contact'],
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
  <meta name="theme-color" content="#0f4c3a">
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css?v=5">
<?= $m['extra_head'] ?? '' ?>
</head>
<body>
<?php
}

function logo_block(): string
{
    return '<a href="/" class="logo"><span class="logo-mark" aria-hidden="true">' . icon('leaf') . '</span>'
        . '<span><span class="logo-name">' . e(BRAND) . '</span><span class="logo-tag">Consistency in Excellence</span></span></a>';
}

function page_header(string $active = ''): void
{
    ?>
<a class="skip" href="#main">Skip to content</a>
<header class="site-header">
  <div class="container nav">
    <?= logo_block() ?>
    <nav aria-label="Main">
      <button class="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="nav-links"><span></span><span></span><span></span></button>
      <ul class="nav-links" id="nav-links">
        <?php foreach (nav_items() as $key => [$href, $label]): ?>
          <li><a href="<?= e($href) ?>" <?= $key === $active ? 'class="active" aria-current="page"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
        <li><a href="/#contact" class="btn btn-primary btn-sm">Book a clean</a></li>
      </ul>
    </nav>
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
      <div>
        <?= logo_block() ?>
        <p class="muted">Internationally recognized cleaning and fumigation company providing authentic services, products, and solutions across Africa.</p>
      </div>
      <div>
        <h4>Our Services</h4>
        <ul>
          <li><a href="/#services">Cleaning Services</a></li>
          <li><a href="/#hygiene">Hygienic Services</a></li>
          <li><a href="/#pest-control">Pest Control</a></li>
          <li><a href="/#pest-control">Fumigation</a></li>
          <li><a href="/#services">Sanitary Disposal</a></li>
          <li><a href="/#contact">Emergency Services</a></li>
        </ul>
      </div>
      <div>
        <h4>Quick Links</h4>
        <ul>
          <li><a href="/#about">About Us</a></li>
          <li><a href="<?= e(rtrim(BLOG_PATH, '/')) ?>/">Blog</a></li>
          <li><a href="/#testimonials">Clients</a></li>
          <li><a href="/#contact">Contact</a></li>
          <li><a href="/#contact">Get a Quote</a></li>
        </ul>
      </div>
    </div>
    <hr>
    <div class="copy"><span>© <?= date('Y') ?> Onpoint Hygiene and Fumigation Services. All rights reserved.</span>
      <span class="motto">Authentic services · Authentic products · Authentic solutions</span></div>
  </div>
</footer>
<script src="/assets/js/main.js?v=3" defer></script>
</body>
</html>
<?php
}

function format_post_date(?string $d): string
{
    return $d ? date('j F Y', strtotime($d)) : '';
}
