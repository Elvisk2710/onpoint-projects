<?php
declare(strict_types=1);

/**
 * Search + AI-engine optimisation helpers, shared by all sites.
 *
 * Each site provides `site_profile()` (includes/profile.php) with its business facts. From that we build
 * one consistent schema.org @graph (Organization + WebSite + page nodes) and the head tags crawlers use.
 */

function org_id(): string { return SITE_URL . '/#organization'; }
function website_id(): string { return SITE_URL . '/#website'; }
function blog_id(): string { return SITE_URL . rtrim(BLOG_PATH, '/') . '/#blog'; }

/** @return string[] social/directory profile URLs saved in Admin → Settings. */
function same_as_urls(): array
{
    $urls = [];
    foreach (preg_split('/\R/', setting('same_as')) ?: [] as $line) {
        $line = trim($line);
        if ($line !== '' && preg_match('#^https?://#i', $line)) {
            $urls[] = $line;
        }
    }
    return array_values(array_unique($urls));
}

/** Width/height of an uploaded image (path like "uploads/posts/2026/11/x.jpg"), or null. */
function upload_dims(?string $path): ?array
{
    static $cache = [];
    if (!$path || !str_starts_with($path, 'uploads/')) {
        return null;
    }
    if (!array_key_exists($path, $cache)) {
        $abs = BACKEND_ROOT . '/' . $path;
        $info = is_file($abs) ? @getimagesize($abs) : false;
        $cache[$path] = $info ? ['w' => (int) $info[0], 'h' => (int) $info[1]] : null;
    }
    return $cache[$path];
}

function image_object(?string $path, string $alt = ''): ?array
{
    if (!$path) {
        return null;
    }
    $url = upload_url($path);
    $d = upload_dims($path);
    $o = ['@type' => 'ImageObject', 'url' => $url, 'contentUrl' => $url];
    if ($alt !== '') {
        $o['caption'] = $alt;
    }
    if ($d) {
        $o['width'] = $d['w'];
        $o['height'] = $d['h'];
    }
    return $o;
}

// ---------------------------------------------------------------------------
// schema.org nodes
// ---------------------------------------------------------------------------

function seo_org_node(): array
{
    $p = site_profile();
    $addr = ['@type' => 'PostalAddress', 'streetAddress' => $p['address']['street'], 'addressLocality' => $p['address']['locality'], 'addressCountry' => 'ZW'];
    if (!empty($p['address']['region'])) {
        $addr['addressRegion'] = $p['address']['region'];
    }
    $node = [
        '@type' => $p['org_type'],
        '@id' => org_id(),
        'name' => $p['name'],
        'alternateName' => $p['short_name'],
        'url' => SITE_URL . '/',
        'description' => $p['description'],
        'logo' => ['@type' => 'ImageObject', '@id' => SITE_URL . '/#logo', 'url' => SITE_URL . $p['logo'], 'caption' => $p['name']],
        'image' => SITE_URL . $p['image'],
        'email' => $p['emails'][0],
        'telephone' => $p['phones'][0],
        'address' => $addr,
        'areaServed' => ['@type' => 'Country', 'name' => 'Zimbabwe'],
        'knowsAbout' => $p['knows_about'],
        'contactPoint' => array_map(
            static fn (string $tel): array => ['@type' => 'ContactPoint', 'telephone' => $tel, 'contactType' => 'customer service', 'areaServed' => 'ZW', 'availableLanguage' => ['en']],
            $p['phones']
        ),
    ];
    if ($sameAs = same_as_urls()) {
        $node['sameAs'] = $sameAs;
    }
    if (!empty($p['services'])) {
        $node['hasOfferCatalog'] = [
            '@type' => 'OfferCatalog',
            'name' => $p['name'] . ' services',
            'itemListElement' => array_map(
                static fn (array $s): array => ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => $s[0], 'description' => $s[1], 'provider' => ['@id' => org_id()], 'areaServed' => ['@type' => 'Country', 'name' => 'Zimbabwe']]],
                $p['services']
            ),
        ];
    }
    return $node;
}

function seo_website_node(): array
{
    $p = site_profile();
    return [
        '@type' => 'WebSite', '@id' => website_id(), 'url' => SITE_URL . '/', 'name' => $p['name'],
        'description' => $p['description'], 'inLanguage' => 'en-ZW', 'publisher' => ['@id' => org_id()],
    ];
}

/** @param array<int,array{0:string,1:string}> $items [name, path] pairs, home first */
function seo_breadcrumb_node(array $items, string $pageUrl): array
{
    $list = [];
    foreach ($items as $i => [$name, $path]) {
        $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => SITE_URL . $path];
    }
    return ['@type' => 'BreadcrumbList', '@id' => $pageUrl . '#breadcrumb', 'itemListElement' => $list];
}

function seo_webpage_node(string $url, string $name, string $desc, string $type = 'WebPage', ?array $extra = null): array
{
    return array_filter([
        '@type' => $type, '@id' => $url . '#webpage', 'url' => $url, 'name' => $name, 'description' => $desc,
        'inLanguage' => 'en-ZW', 'isPartOf' => ['@id' => website_id()], 'about' => ['@id' => org_id()],
        'breadcrumb' => ['@id' => $url . '#breadcrumb'],
    ] + ($extra ?? []));
}

/** @return array<int,array{q:string,a:string}> */
function post_faqs(array $post): array
{
    $raw = json_decode((string) ($post['faq_json'] ?? ''), true);
    $out = [];
    foreach (is_array($raw) ? $raw : [] as $row) {
        $q = trim((string) ($row['q'] ?? ''));
        $a = trim((string) ($row['a'] ?? ''));
        if ($q !== '' && $a !== '') {
            $out[] = ['q' => $q, 'a' => $a];
        }
    }
    return $out;
}

/** @return string[] */
function post_takeaways(array $post): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) ($post['takeaways'] ?? '')) ?: [])));
}

function seo_post_nodes(array $post, array $crumbs): array
{
    $p = site_profile();
    $url = SITE_URL . post_path($post['slug']);
    $desc = $post['seo_description'] ?: post_summary($post);
    $img = image_object($post['cover_image'] ?? null, (string) ($post['cover_alt'] ?? ''));
    $words = str_word_count(strip_tags((string) $post['body_html']));
    $article = array_filter([
        '@type' => 'BlogPosting',
        '@id' => $url . '#article',
        'headline' => mb_substr($post['title'], 0, 110),
        'name' => $post['title'],
        'description' => $desc,
        'abstract' => post_takeaways($post) ? implode(' ', post_takeaways($post)) : null,
        'image' => $img ? [$img] : null,
        'datePublished' => date(DATE_ATOM, strtotime($post['published_at'])),
        'dateModified' => date(DATE_ATOM, strtotime($post['updated_at'] ?: $post['published_at'])),
        'author' => ['@id' => org_id()],
        'publisher' => ['@id' => org_id()],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
        'isPartOf' => ['@id' => blog_id()],
        'inLanguage' => 'en-ZW',
        'wordCount' => $words,
        'timeRequired' => 'PT' . reading_minutes((string) $post['body_html']) . 'M',
        'articleSection' => $p['blog_name'],
    ]);
    $nodes = [
        [
            '@type' => 'Blog', '@id' => blog_id(), 'name' => $p['blog_name'], 'url' => SITE_URL . rtrim(BLOG_PATH, '/') . '/',
            'publisher' => ['@id' => org_id()], 'isPartOf' => ['@id' => website_id()], 'inLanguage' => 'en-ZW',
        ],
        seo_webpage_node($url, $post['title'], $desc, 'WebPage', array_filter(['primaryImageOfPage' => $img, 'datePublished' => $article['datePublished'], 'dateModified' => $article['dateModified']])),
        $article,
        seo_breadcrumb_node($crumbs, $url),
    ];
    if ($faqs = post_faqs($post)) {
        $nodes[] = [
            '@type' => 'FAQPage', '@id' => $url . '#faq', 'mainEntity' => array_map(
                static fn (array $f): array => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]],
                $faqs
            ),
        ];
    }
    return $nodes;
}

function seo_blog_nodes(array $posts, string $url, array $crumbs): array
{
    $p = site_profile();
    return [
        seo_webpage_node($url, $p['blog_name'] . ' | ' . $p['short_name'], $p['blog_description'], 'CollectionPage'),
        [
            '@type' => 'Blog', '@id' => blog_id(), 'name' => $p['blog_name'], 'description' => $p['blog_description'], 'url' => SITE_URL . rtrim(BLOG_PATH, '/') . '/',
            'inLanguage' => 'en-ZW', 'publisher' => ['@id' => org_id()], 'isPartOf' => ['@id' => website_id()],
            'blogPost' => array_map(static fn (array $x): array => array_filter([
                '@type' => 'BlogPosting', '@id' => SITE_URL . post_path($x['slug']) . '#article', 'headline' => mb_substr($x['title'], 0, 110),
                'url' => SITE_URL . post_path($x['slug']), 'datePublished' => date(DATE_ATOM, strtotime($x['published_at'])),
                'image' => $x['cover_image'] ? upload_url($x['cover_image']) : null,
            ]), $posts),
        ],
        seo_breadcrumb_node($crumbs, $url),
    ];
}

function seo_home_nodes(): array
{
    $p = site_profile();
    $url = SITE_URL . '/';
    return [
        seo_webpage_node($url, $p['home_title'], $p['home_description']),
        seo_breadcrumb_node([[$p['short_name'], '/']], $url),
    ];
}

function jsonld(array $nodes): string
{
    $graph = ['@context' => 'https://schema.org', '@graph' => array_values(array_merge([seo_org_node(), seo_website_node()], $nodes))];
    return '<script type="application/ld+json">' . json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) . '</script>';
}

// ---------------------------------------------------------------------------
// <head> tags
// ---------------------------------------------------------------------------

/**
 * Prints every SEO/social tag except <title>. Called from each site's page_head().
 *
 * @param array{title:string, description:string, path?:string, image?:string, image_alt?:string, image_w?:int, image_h?:int,
 *              type?:string, noindex?:bool, nodes?:array, published?:string, modified?:string, section?:string,
 *              prev?:string, next?:string} $m
 */
function seo_head(array $m): void
{
    $p = site_profile();
    $url = SITE_URL . ($m['path'] ?? '/');
    $image = $m['image'] ?? (SITE_URL . $p['image']);
    $desc = $m['description'];
    $robots = !empty($m['noindex'])
        ? 'noindex,follow'
        : 'index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1';
    $e = 'e';
    echo '  <meta name="description" content="', $e($desc), "\">\n";
    echo '  <meta name="robots" content="', $robots, "\">\n";
    echo '  <link rel="canonical" href="', $e($url), "\">\n";
    echo '  <link rel="alternate" hreflang="en-zw" href="', $e($url), "\">\n";
    echo '  <link rel="alternate" hreflang="x-default" href="', $e($url), "\">\n";
    echo '  <link rel="alternate" type="application/rss+xml" title="', $e($p['blog_name'] . ' — ' . $p['short_name']), '" href="', $e(SITE_URL . '/feed.xml'), "\">\n";
    if (!empty($m['prev'])) {
        echo '  <link rel="prev" href="', $e(SITE_URL . $m['prev']), "\">\n";
    }
    if (!empty($m['next'])) {
        echo '  <link rel="next" href="', $e(SITE_URL . $m['next']), "\">\n";
    }
    $type = $m['type'] ?? 'website';
    echo '  <meta property="og:type" content="', $e($type), "\">\n";
    echo '  <meta property="og:site_name" content="', $e($p['short_name']), "\">\n";
    echo '  <meta property="og:locale" content="en_ZW">', "\n";
    echo '  <meta property="og:title" content="', $e($m['title']), "\">\n";
    echo '  <meta property="og:description" content="', $e($desc), "\">\n";
    echo '  <meta property="og:url" content="', $e($url), "\">\n";
    echo '  <meta property="og:image" content="', $e($image), "\">\n";
    if (!empty($m['image_w']) && !empty($m['image_h'])) {
        echo '  <meta property="og:image:width" content="', (int) $m['image_w'], "\">\n";
        echo '  <meta property="og:image:height" content="', (int) $m['image_h'], "\">\n";
    }
    if (!empty($m['image_alt'])) {
        echo '  <meta property="og:image:alt" content="', $e($m['image_alt']), "\">\n";
        echo '  <meta name="twitter:image:alt" content="', $e($m['image_alt']), "\">\n";
    }
    if ($type === 'article') {
        if (!empty($m['published'])) {
            echo '  <meta property="article:published_time" content="', $e($m['published']), "\">\n";
        }
        if (!empty($m['modified'])) {
            echo '  <meta property="article:modified_time" content="', $e($m['modified']), "\">\n";
        }
        echo '  <meta property="article:author" content="', $e($p['name']), "\">\n";
        if (!empty($m['section'])) {
            echo '  <meta property="article:section" content="', $e($m['section']), "\">\n";
        }
    }
    echo '  <meta name="twitter:card" content="summary_large_image">', "\n";
    echo '  <meta name="twitter:title" content="', $e($m['title']), "\">\n";
    echo '  <meta name="twitter:description" content="', $e($desc), "\">\n";
    echo '  <meta name="twitter:image" content="', $e($image), "\">\n";
    echo '  ', jsonld($m['nodes'] ?? []), "\n";
}

// ---------------------------------------------------------------------------
// Visible, crawlable answer blocks
// ---------------------------------------------------------------------------

/** @param array<int,array{0:string,1:?string}> $items [label, path|null] — last item has null path */
function render_breadcrumbs(array $items): string
{
    $h = '<nav class="breadcrumbs" aria-label="Breadcrumb"><ol>';
    foreach ($items as [$label, $path]) {
        $h .= '<li>' . ($path ? '<a href="' . e($path) . '">' . e($label) . '</a>' : '<span aria-current="page">' . e($label) . '</span>') . '</li>';
    }
    return $h . '</ol></nav>';
}

function render_takeaways(array $post): string
{
    $items = post_takeaways($post);
    if (!$items) {
        return '';
    }
    $h = '<aside class="takeaways" aria-label="Key takeaways"><h2>Key takeaways</h2><ul>';
    foreach ($items as $t) {
        $h .= '<li>' . e($t) . '</li>';
    }
    return $h . '</ul></aside>';
}

function render_faq(array $post): string
{
    $faqs = post_faqs($post);
    if (!$faqs) {
        return '';
    }
    $h = '<section class="post-faq" aria-labelledby="faq-title"><h2 id="faq-title">Frequently asked questions</h2>';
    foreach ($faqs as $i => $f) {
        $h .= '<details' . ($i === 0 ? ' open' : '') . '><summary>' . e($f['q']) . '</summary><p>' . nl2br(e($f['a'])) . '</p></details>';
    }
    return $h . '</section>';
}

/** "Updated 3 March 2026" — only when materially later than the publish date (freshness signal). */
function render_updated(array $post): string
{
    $pub = strtotime($post['published_at']);
    $mod = strtotime($post['updated_at'] ?: $post['published_at']);
    if ($mod - $pub < 86400) {
        return '';
    }
    return ' · Updated <time datetime="' . e(date('Y-m-d', $mod)) . '">' . e(date('j F Y', $mod)) . '</time>';
}

// ---------------------------------------------------------------------------
// Text conversion for llms.txt and the feed
// ---------------------------------------------------------------------------

function html_to_markdown(string $html): string
{
    $h = preg_replace('#<(script|style)\b.*?</\1>#is', '', $html) ?? $html;
    $h = preg_replace_callback('#<h([1-6])[^>]*>(.*?)</h\1>#is', static fn (array $m): string => "\n\n" . str_repeat('#', min(6, (int) $m[1] + 1)) . ' ' . trim(strip_tags($m[2])) . "\n\n", $h) ?? $h;
    $h = preg_replace_callback('#<img\b([^>]*)>#i', static function (array $m): string {
        preg_match('#src="([^"]*)"#i', $m[1], $s);
        preg_match('#alt="([^"]*)"#i', $m[1], $a);
        return isset($s[1]) ? '![' . ($a[1] ?? '') . '](' . $s[1] . ')' : '';
    }, $h) ?? $h;
    $h = preg_replace('#<a\b[^>]*href="([^"]+)"[^>]*>(.*?)</a>#is', '[$2]($1)', $h) ?? $h;
    $h = preg_replace('#<(strong|b)>(.*?)</\1>#is', '**$2**', $h) ?? $h;
    $h = preg_replace('#<(em|i)>(.*?)</\1>#is', '*$2*', $h) ?? $h;
    $h = preg_replace('#<li[^>]*>#i', "\n- ", $h) ?? $h;
    $h = preg_replace('#<br\s*/?>#i', "\n", $h) ?? $h;
    $h = preg_replace('#</(p|div|ul|ol|blockquote|tr|table|figure)>#i', "\n\n", $h) ?? $h;
    $h = html_entity_decode(strip_tags($h), ENT_QUOTES, 'UTF-8');
    return trim(preg_replace("/\n{3,}/", "\n\n", $h) ?? $h);
}

// ---------------------------------------------------------------------------
// IndexNow: tell Bing (which also feeds ChatGPT search, Copilot, DuckDuckGo) and Yandex about new/changed URLs
// ---------------------------------------------------------------------------

function indexnow_ping(array $urls): void
{
    $key = (string) env('INDEXNOW_KEY', '');
    $host = (string) parse_url(SITE_URL, PHP_URL_HOST);
    if ($key === '' || $urls === [] || !str_contains($host, '.') || preg_match('/^\d+\.\d+\.\d+\.\d+$/', $host)) {
        return; // not configured, or running on localhost
    }
    $body = json_encode(['host' => $host, 'key' => $key, 'keyLocation' => SITE_URL . '/indexnow.txt', 'urlList' => array_values($urls)]);
    try {
        if (function_exists('curl_init')) {
            $ch = curl_init('https://api.indexnow.org/indexnow');
            curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'], CURLOPT_TIMEOUT => 4, CURLOPT_CONNECTTIMEOUT => 3]);
            curl_exec($ch);
            curl_close($ch);
        } elseif (ini_get('allow_url_fopen')) {
            @file_get_contents('https://api.indexnow.org/indexnow', false, stream_context_create(['http' => [
                'method' => 'POST', 'header' => 'Content-Type: application/json; charset=utf-8', 'content' => $body, 'timeout' => 4,
            ]]));
        }
    } catch (Throwable $e) {
        error_log('IndexNow: ' . $e->getMessage()); // never block publishing
    }
}
