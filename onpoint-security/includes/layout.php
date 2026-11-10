<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

const BRAND = 'ShieldTrac Security';
const SHIELD_SVG = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l8 3v6c0 5-3.4 9.2-8 11-4.6-1.8-8-6-8-11V5z"/></svg>';

/** Inline line icons (24px grid, stroke = currentColor). */
function icon(string $name): string
{
    $p = [
        'guard' => '<path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
        'cash' => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 10v4M18 10v4"/>',
        'search' => '<circle cx="11" cy="11" r="6"/><path d="M20 20l-4.3-4.3"/>',
        'alarm' => '<path d="M6 16V11a6 6 0 0112 0v5"/><path d="M4 16h16M10 20h4M12 2v2M3.5 6.5l1.4 1.4M20.5 6.5l-1.4 1.4"/>',
        'cctv' => '<path d="M3 7l13-3 2 6-13 3z"/><path d="M7 12l1 4H4v4"/><path d="M18 8l3-1"/>',
        'gate' => '<rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="15" cy="12" r="1"/><path d="M4 21h16"/>',
        'team' => '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M16 14c2.8 0 5 2.2 5 5"/>',
        'medal' => '<circle cx="12" cy="15" r="5"/><path d="M8.5 11L6 3h4l2 4 2-4h4l-2.5 8"/>',
        'star' => '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
        'layers' => '<path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/>',
        'check' => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/>',
        'chat' => '<path d="M4 20l1.5-4A8 8 0 1112 20a8 8 0 01-4-1z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'pin' => '<path d="M12 21s-7-6.2-7-12a7 7 0 0114 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    ][$name] ?? '';
    return '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
}

/** Names and intro copy for this site's blog section. */
function blog_meta(): array
{
    return [
        'nav' => 'Security Insights',
        'name' => 'Security Insights',
        'eyebrow' => 'SECURITY INSIGHTS',
        'h1' => 'Stay one step <span class="c-orange">ahead</span> of risk',
        'lead' => 'Advisories, practical tips and industry news from the ShieldTrac security team.',
        'empty' => 'Our first security insights are on the way. Check back soon.',
    ];
}

function nav_items(): array
{
    return [
        'about' => ['/#about', 'About'],
        'services' => ['/#services', 'Services'],
        'why' => ['/#why-us', 'Why Choose Us'],
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
  <meta name="theme-color" content="#0b2545">
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css?v=5">
<?= $m['extra_head'] ?? '' ?>
</head>
<body>
<?php
}

function logo_block(): string
{
    return '<a href="/" class="logo"><span class="logo-mark">' . SHIELD_SVG . '</span>'
        . '<span><span class="logo-name">' . e(BRAND) . '</span><span class="logo-tag">Protection You Can Trust</span></span></a>';
}

function page_header(string $active = ''): void
{
    ?>
<a class="skip" href="#main">Skip to content</a>
<div class="topline"><div class="container"><span>24/7 support</span><a href="tel:+263788094724">+263 78 809 4724</a></div></div>
<header class="site-header">
  <div class="container nav">
    <?= logo_block() ?>
    <nav aria-label="Main">
      <button class="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="nav-links"><span></span><span></span><span></span></button>
      <ul class="nav-links" id="nav-links">
        <?php foreach (nav_items() as $key => [$href, $label]): ?>
          <li><a href="<?= e($href) ?>" <?= $key === $active ? 'class="active" aria-current="page"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
        <li><a href="/#contact" class="btn btn-primary btn-sm">Get a Quote</a></li>
      </ul>
    </nav>
  </div>
</header>
<main id="main">
<?php
}

function page_footer(): void
{
    $services = ['Manned Security', 'Cash Management', 'Private Investigations', 'Alarm Systems', 'CCTV Security', 'Access Solutions'];
    ?>
</main>
<footer class="site-footer">
  <div class="container">
    <div class="footer-cta">
      <div><span class="eyebrow">Free security assessment</span><h3>Ready to secure your business?</h3></div>
      <a class="btn btn-primary" href="/#contact">Book an assessment <?= icon('arrow') ?></a>
    </div>
    <div class="footer-grid">
      <div>
        <?= logo_block() ?>
        <p class="muted">Zimbabwe's leading private security company providing comprehensive security solutions for businesses of all sizes.</p>
      </div>
      <div>
        <h4>Quick Links</h4>
        <ul>
          <li><a href="/#about">About Us</a></li>
          <li><a href="/#services">Services</a></li>
          <li><a href="/#why-us">Why Choose Us</a></li>
          <li><a href="/#testimonials">Testimonials</a></li>
          <li><a href="<?= e(rtrim(BLOG_PATH, '/')) ?>/">Blog</a></li>
          <li><a href="/#contact">Contact</a></li>
        </ul>
      </div>
      <div>
        <h4>Our Services</h4>
        <ul>
          <?php foreach ($services as $s): ?><li><a href="/#services"><?= e($s) ?></a></li><?php endforeach; ?>
        </ul>
      </div>
    </div>
    <hr>
    <p class="copy">© <?= date('Y') ?> ShieldTrac Security Services Limited. All rights reserved.</p>
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
