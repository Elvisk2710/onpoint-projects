<?php
/** /feed.xml — RSS 2.0 with full content. Helps Google, Bing, aggregators and AI crawlers discover and read new posts. */
require __DIR__ . '/src/bootstrap.php';

$p = site_profile();
$posts = public_posts(20);
$x = static fn (string $s): string => htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
$build = $posts ? date(DATE_RSS, strtotime($posts[0]['updated_at'])) : date(DATE_RSS);

header('Content-Type: application/rss+xml; charset=utf-8');
header('Cache-Control: public, max-age=900');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title><?= $x($p['blog_name'] . ' — ' . $p['short_name']) ?></title>
    <link><?= $x(SITE_URL . rtrim(BLOG_PATH, '/') . '/') ?></link>
    <description><?= $x($p['blog_description']) ?></description>
    <language>en-zw</language>
    <lastBuildDate><?= $build ?></lastBuildDate>
    <ttl>60</ttl>
    <atom:link href="<?= $x(SITE_URL . '/feed.xml') ?>" rel="self" type="application/rss+xml"/>
<?php foreach ($posts as $post): $url = SITE_URL . post_path($post['slug']); ?>
    <item>
      <title><?= $x($post['title']) ?></title>
      <link><?= $x($url) ?></link>
      <guid isPermaLink="true"><?= $x($url) ?></guid>
      <pubDate><?= date(DATE_RSS, strtotime($post['published_at'])) ?></pubDate>
      <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/"><?= $x($p['name']) ?></dc:creator>
      <description><?= $x(post_summary($post)) ?></description>
      <content:encoded><![CDATA[<?= str_replace(']]>', ']]]]><![CDATA[>', $post['body_html']) ?>]]></content:encoded>
<?php if ($post['cover_image']): ?>
      <media:content url="<?= $x(upload_url($post['cover_image'])) ?>" medium="image"/>
<?php endif; ?>
    </item>
<?php endforeach; ?>
  </channel>
</rss>
