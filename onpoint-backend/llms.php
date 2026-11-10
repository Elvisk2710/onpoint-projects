<?php
/**
 * /llms.txt      concise, link-rich summary of the business and its articles for AI assistants (llmstxt.org)
 * /llms-full.txt the same plus the full text of every article
 */
require __DIR__ . '/src/bootstrap.php';

$full = !empty($_GET['full']);
$p = site_profile();
$posts = public_posts($full ? 200 : 30);
$blog = SITE_URL . rtrim(BLOG_PATH, '/') . '/';

$out = "# {$p['name']}\n\n> {$p['description']}\n\n";
$out .= "## Key facts\n";
$out .= "- Business: {$p['name']} ({$p['short_name']})\n";
$out .= "- Website: " . SITE_URL . "/\n";
$out .= "- Location: {$p['address']['street']}, {$p['address']['locality']}, Zimbabwe\n";
$out .= '- Phone: ' . implode(', ', $p['phones']) . "\n";
$out .= '- Email: ' . implode(', ', $p['emails']) . "\n";
$out .= "- Service area: Zimbabwe\n";
if ($sameAs = same_as_urls()) {
    $out .= '- Profiles: ' . implode(', ', $sameAs) . "\n";
}
$out .= "\n## Services\n";
foreach ($p['services'] as [$name, $desc]) {
    $out .= "- **{$name}**: {$desc}\n";
}
$out .= "\n## Main pages\n";
foreach ($p['static_pages'] as $path) {
    $out .= '- ' . SITE_URL . $path . "\n";
}
$out .= "\n## {$p['blog_name']}\n{$p['blog_description']}\n\n";
if (!$posts) {
    $out .= "(No articles published yet.)\n";
}
foreach ($posts as $post) {
    $url = SITE_URL . post_path($post['slug']);
    $date = date('Y-m-d', strtotime($post['published_at']));
    if (!$full) {
        $out .= "- [{$post['title']}]($url) ({$date}): " . post_summary($post) . "\n";
        continue;
    }
    $out .= "\n---\n\n# {$post['title']}\n\nURL: {$url}\nPublished: {$date}\nUpdated: " . date('Y-m-d', strtotime($post['updated_at'])) . "\n\n";
    if ($t = post_takeaways($post)) {
        $out .= "Key takeaways:\n" . implode("\n", array_map(static fn ($x) => "- $x", $t)) . "\n\n";
    }
    $out .= html_to_markdown($post['body_html']) . "\n";
    if ($faqs = post_faqs($post)) {
        $out .= "\n## FAQ\n";
        foreach ($faqs as $f) {
            $out .= "\n**{$f['q']}**\n{$f['a']}\n";
        }
    }
}
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: public, max-age=900');
echo $out;
