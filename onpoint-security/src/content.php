<?php
declare(strict_types=1);

/**
 * Read-side queries for the public website pages (blog list, post page, projects).
 * Every function degrades to "nothing" if the database is unreachable, so the
 * rest of the site keeps working.
 */

const PUBLIC_POST_SQL = "p.status = 'published' AND p.published_at IS NOT NULL AND p.published_at <= NOW()";

function public_post_count(): int
{
    $pdo = db(false);
    if (!$pdo) {
        return 0;
    }
    try {
        return (int) $pdo->query('SELECT COUNT(*) FROM posts p WHERE ' . PUBLIC_POST_SQL)->fetchColumn();
    } catch (Throwable $e) {
        error_log('content: ' . $e->getMessage());
        return 0;
    }
}

function public_posts(int $limit, int $offset = 0): array
{
    $pdo = db(false);
    if (!$pdo) {
        return [];
    }
    try {
        $stmt = $pdo->prepare(
            'SELECT p.*, a.name AS author FROM posts p LEFT JOIN admins a ON a.id = p.author_id
             WHERE ' . PUBLIC_POST_SQL . ' ORDER BY p.published_at DESC, p.id DESC LIMIT ? OFFSET ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log('content: ' . $e->getMessage());
        return [];
    }
}

function public_post(string $slug): ?array
{
    $pdo = db(false);
    if (!$pdo) {
        return null;
    }
    try {
        $stmt = $pdo->prepare(
            'SELECT p.*, a.name AS author FROM posts p LEFT JOIN admins a ON a.id = p.author_id
             WHERE p.slug = ? AND ' . PUBLIC_POST_SQL
        );
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    } catch (Throwable $e) {
        error_log('content: ' . $e->getMessage());
        return null;
    }
}

/** @param '<'|'>' $cmp '<' = older post, '>' = newer post */
function adjacent_post(array $post, string $cmp): ?array
{
    $pdo = db(false);
    if (!$pdo || !in_array($cmp, ['<', '>'], true)) {
        return null;
    }
    $dir = $cmp === '<' ? 'DESC' : 'ASC';
    try {
        $stmt = $pdo->prepare(
            "SELECT p.slug, p.title FROM posts p WHERE " . PUBLIC_POST_SQL . "
             AND (p.published_at $cmp ? OR (p.published_at = ? AND p.id $cmp ?))
             ORDER BY p.published_at $dir, p.id $dir LIMIT 1"
        );
        $stmt->execute([$post['published_at'], $post['published_at'], $post['id']]);
        return $stmt->fetch() ?: null;
    } catch (Throwable $e) {
        error_log('content: ' . $e->getMessage());
        return null;
    }
}

function post_path(string $slug): string
{
    return rtrim(BLOG_PATH, '/') . '/' . rawurlencode($slug);
}

function post_summary(array $p): string
{
    return $p['excerpt'] ?: excerpt_from_html($p['body_html'] ?? '', 180);
}

// ----- Projects (engineering) ---------------------------------------------

function public_projects(int $limit = 50, bool $featuredOnly = false, string $status = ''): array
{
    $pdo = db(false);
    if (!$pdo || !FEATURE_PROJECTS) {
        return [];
    }
    $sql = 'SELECT * FROM projects WHERE is_published = 1';
    $params = [];
    if ($featuredOnly) {
        $sql .= ' AND featured = 1';
    }
    if (in_array($status, ['planned', 'ongoing', 'completed', 'on_hold'], true)) {
        $sql .= ' AND status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY featured DESC, sort_order ASC, COALESCE(end_date, start_date, created_at) DESC LIMIT ' . max(1, min(100, $limit));
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log('content: ' . $e->getMessage());
        return [];
    }
}

function public_project(string $slug): ?array
{
    $pdo = db(false);
    if (!$pdo || !FEATURE_PROJECTS) {
        return null;
    }
    try {
        $stmt = $pdo->prepare('SELECT * FROM projects WHERE slug = ? AND is_published = 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    } catch (Throwable $e) {
        error_log('content: ' . $e->getMessage());
        return null;
    }
}

function latest_posts(int $n): array
{
    return public_posts($n);
}

function project_path(string $slug): string
{
    return rtrim(PROJECTS_PATH, '/') . '/' . rawurlencode($slug);
}

// ----- Redirects & related content (SEO) ----------------------------------

/** Current slug for an old one (after a rename), or null. */
function slug_redirect(string $kind, string $oldSlug): ?string
{
    $pdo = db(false);
    if (!$pdo) {
        return null;
    }
    try {
        $table = $kind === 'project' ? 'projects' : 'posts';
        $stmt = $pdo->prepare("SELECT t.slug FROM redirects r JOIN `$table` t ON t.id = r.target_id WHERE r.kind = ? AND r.old_slug = ?");
        $stmt->execute([$kind, $oldSlug]);
        $slug = $stmt->fetchColumn();
        return $slug ? (string) $slug : null;
    } catch (Throwable $e) {
        error_log('content: ' . $e->getMessage());
        return null;
    }
}

/** Remember that $oldSlug used to belong to this item (admin side; call when a slug changes). */
function remember_old_slug(string $kind, string $oldSlug, int $targetId): void
{
    if ($oldSlug === '') {
        return;
    }
    db()->prepare('INSERT INTO redirects (kind, old_slug, target_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE target_id = VALUES(target_id)')
        ->execute([$kind, $oldSlug, $targetId]);
}

/** A slug now in use must not also be a redirect source. */
function forget_slug_redirect(string $kind, string $slug): void
{
    db()->prepare('DELETE FROM redirects WHERE kind = ? AND old_slug = ?')->execute([$kind, $slug]);
}

/** Issue a 301 to the renamed post/project if one exists. Call before outputting a 404. */
function redirect_if_renamed(string $kind, string $slug): void
{
    if (!preg_match('/^[a-z0-9-]{1,190}$/', $slug)) {
        return;
    }
    if ($new = slug_redirect($kind, $slug)) {
        header('Location: ' . SITE_URL . ($kind === 'project' ? project_path($new) : post_path($new)), true, 301);
        exit;
    }
}

/** Other recent posts for the "keep reading" blocks (internal links help crawling and AI context). */
function related_posts(array $post, int $n = 3): array
{
    $out = [];
    foreach (public_posts($n + 2) as $p) {
        if ((int) $p['id'] !== (int) $post['id']) {
            $out[] = $p;
        }
        if (count($out) >= $n) {
            break;
        }
    }
    return $out;
}

/** Timestamp (MySQL format) of the most recently changed public post, or null. */
function latest_post_update(): ?string
{
    $pdo = db(false);
    if (!$pdo) {
        return null;
    }
    try {
        $v = $pdo->query('SELECT MAX(p.updated_at) FROM posts p WHERE ' . PUBLIC_POST_SQL)->fetchColumn();
        return $v ?: null;
    } catch (Throwable $e) {
        return null;
    }
}
