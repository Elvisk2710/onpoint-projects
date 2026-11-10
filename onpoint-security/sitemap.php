<?php
/** /sitemap.xml — static pages from the site profile, every public post and project, with cover images. */
require __DIR__ . '/src/bootstrap.php';

$p = site_profile();
$latest = latest_post_update();
$rows = [];
foreach ($p['static_pages'] as $path) {
    $isHome = $path === '/';
    $isBlog = $path === rtrim(BLOG_PATH, '/') . '/';
    $rows[] = ['loc' => SITE_URL . $path, 'lastmod' => ($isHome || $isBlog) ? $latest : null, 'images' => []];
}
foreach (public_posts(5000) as $post) {
    $rows[] = ['loc' => SITE_URL . post_path($post['slug']), 'lastmod' => $post['updated_at'],
        'images' => $post['cover_image'] ? [upload_url($post['cover_image'])] : []];
}
foreach (public_projects(100) as $pr) {
    $rows[] = ['loc' => SITE_URL . project_path($pr['slug']), 'lastmod' => $pr['updated_at'],
        'images' => $pr['cover_image'] ? [upload_url($pr['cover_image'])] : []];
}

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
    . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
foreach ($rows as $r) {
    $xml .= '  <url><loc>' . e($r['loc']) . '</loc>';
    if ($r['lastmod']) {
        $xml .= '<lastmod>' . date(DATE_ATOM, strtotime($r['lastmod'])) . '</lastmod>';
    }
    foreach ($r['images'] as $img) {
        $xml .= '<image:image><image:loc>' . e($img) . '</image:loc></image:image>';
    }
    $xml .= "</url>\n";
}
$xml .= "</urlset>\n";

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=900');
echo $xml;
