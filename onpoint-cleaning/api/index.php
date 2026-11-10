<?php
/**
 * Public JSON API.
 *
 *   GET  /api/posts?page=1&limit=10     published posts, newest first
 *   GET  /api/posts/{slug}              one published post (with body_html, prev/next)
 *   GET  /api/sitemap.xml               blog (and project) URLs for this site
 *   GET  /api/projects?featured=1       published projects       (FEATURE_PROJECTS only)
 *   GET  /api/projects/{slug}           one project              (FEATURE_PROJECTS only)
 *   GET  /api/site                      public company details
 *   POST /api/contact                   contact form → admin inbox
 */
require __DIR__ . '/../src/bootstrap.php';

header('X-Content-Type-Options: nosniff');

// ---------------------------------------------------------------------------
// CORS: same-origin needs nothing. CORS_ORIGINS lets e.g. a local Vite dev server call the API.
// ---------------------------------------------------------------------------
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = array_filter(array_map('trim', explode(',', (string) env('CORS_ORIGINS', ''))));
if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 86400');
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------
function json_out(mixed $data, int $status = 200, int $cacheSeconds = 0): never
{
    $body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    send_cacheable($body, $status, $cacheSeconds);
}

/** Emit body with Cache-Control + ETag; answers 304 when the client already has it. */
function send_cacheable(string $body, int $status, int $cacheSeconds): never
{
    if ($cacheSeconds > 0 && $status === 200) {
        $etag = '"' . md5($body) . '"';
        header("Cache-Control: public, max-age=$cacheSeconds");
        header("ETag: $etag");
        if (trim($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
            http_response_code(304);
            exit;
        }
    } else {
        header('Cache-Control: no-store');
    }
    echo $body;
    exit;
}

function api_error(string $message, int $status, array $extra = []): never
{
    json_out(['error' => $message] + $extra, $status);
}

function iso(?string $mysql): ?string
{
    return $mysql ? date(DATE_ATOM, strtotime($mysql)) : null;
}

function post_url(string $slug): string
{
    return SITE_URL . rtrim(BLOG_PATH, '/') . '/' . rawurlencode($slug);
}

function post_resource(array $p, bool $full = false): array
{
    $out = [
        'id' => (int) $p['id'],
        'slug' => $p['slug'],
        'title' => $p['title'],
        'excerpt' => $p['excerpt'] ?: excerpt_from_html($p['body_html'] ?? ''),
        'cover_image' => $p['cover_image'] ? ['url' => upload_url($p['cover_image']), 'alt' => $p['cover_alt'] ?: ''] : null,
        'author' => $p['author'] ?? null,
        'published_at' => iso($p['published_at']),
        'updated_at' => iso($p['updated_at']),
        'reading_minutes' => reading_minutes($p['body_html'] ?? ''),
        'url' => post_url($p['slug']),
    ];
    if ($full) {
        $out['body_html'] = $p['body_html'];
        $out['seo'] = [
            'title' => $p['seo_title'] ?: $p['title'],
            'description' => $p['seo_description'] ?: $out['excerpt'],
        ];
    }
    return $out;
}

function project_resource(array $p, bool $full = false): array
{
    $out = [
        'id' => (int) $p['id'],
        'slug' => $p['slug'],
        'title' => $p['title'],
        'client' => $p['client'],
        'location' => $p['location'],
        'category' => $p['category'],
        'status' => $p['status'],
        'start_date' => $p['start_date'],
        'end_date' => $p['end_date'],
        'summary' => $p['summary'] ?: excerpt_from_html($p['body_html'] ?? ''),
        'featured' => (bool) $p['featured'],
        'cover_image' => $p['cover_image'] ? ['url' => upload_url($p['cover_image']), 'alt' => $p['cover_alt'] ?: ''] : null,
    ];
    if ($full) {
        $out['body_html'] = $p['body_html'] ?? '';
    }
    return $out;
}

function query_int(string $key, int $default, int $min, int $max): int
{
    $v = filter_var($_GET[$key] ?? $default, FILTER_VALIDATE_INT);
    return $v === false ? $default : max($min, min($max, $v));
}

// ---------------------------------------------------------------------------
// Routing: path relative to this script's directory, e.g. "posts/my-slug"
// ---------------------------------------------------------------------------
$uriPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/api/index.php')), '/');
$path = $uriPath;
foreach ([$base . '/index.php', $base] as $prefix) {
    if ($prefix !== '' && str_starts_with($path, $prefix)) {
        $path = substr($path, strlen($prefix));
        break;
    }
}
$path = trim($path, '/');
$seg = $path === '' ? [] : explode('/', $path);

$PUBLIC_POST = "p.status = 'published' AND p.published_at IS NOT NULL AND p.published_at <= NOW()";
$pdo = db();

try {
    // GET /posts
    if ($method === 'GET' && $seg === ['posts']) {
        $page = query_int('page', 1, 1, 10000);
        $limit = query_int('limit', 10, 1, 50);
        $total = (int) $pdo->query("SELECT COUNT(*) FROM posts p WHERE $PUBLIC_POST")->fetchColumn();
        $stmt = $pdo->prepare(
            "SELECT p.*, a.name AS author FROM posts p LEFT JOIN admins a ON a.id = p.author_id
             WHERE $PUBLIC_POST ORDER BY p.published_at DESC, p.id DESC LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, ($page - 1) * $limit, PDO::PARAM_INT);
        $stmt->execute();
        json_out([
            'data' => array_map('post_resource', $stmt->fetchAll()),
            'meta' => ['page' => $page, 'per_page' => $limit, 'total' => $total, 'pages' => (int) ceil($total / $limit)],
        ], 200, 60);
    }

    // GET /posts/{slug}
    if ($method === 'GET' && count($seg) === 2 && $seg[0] === 'posts') {
        $stmt = $pdo->prepare(
            "SELECT p.*, a.name AS author FROM posts p LEFT JOIN admins a ON a.id = p.author_id WHERE p.slug = ? AND $PUBLIC_POST"
        );
        $stmt->execute([$seg[1]]);
        $post = $stmt->fetch();
        if (!$post) {
            api_error('Post not found', 404);
        }
        $adjacent = function (string $cmp, string $dir) use ($pdo, $post, $PUBLIC_POST): ?array {
            $s = $pdo->prepare(
                "SELECT p.slug, p.title FROM posts p WHERE $PUBLIC_POST
                 AND (p.published_at $cmp ? OR (p.published_at = ? AND p.id $cmp ?))
                 ORDER BY p.published_at $dir, p.id $dir LIMIT 1"
            );
            $s->execute([$post['published_at'], $post['published_at'], $post['id']]);
            $row = $s->fetch();
            return $row ? ['slug' => $row['slug'], 'title' => $row['title'], 'url' => post_url($row['slug'])] : null;
        };
        json_out(['data' => post_resource($post, true) + [
            'previous' => $adjacent('<', 'DESC'),
            'next' => $adjacent('>', 'ASC'),
        ]], 200, 60);
    }

    // GET /sitemap.xml
    if ($method === 'GET' && $seg === ['sitemap.xml']) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= '  <url><loc>' . e(SITE_URL . rtrim(BLOG_PATH, '/')) . '</loc><changefreq>weekly</changefreq></url>' . "\n";
        foreach ($pdo->query("SELECT slug, updated_at FROM posts p WHERE $PUBLIC_POST ORDER BY published_at DESC") as $r) {
            $xml .= '  <url><loc>' . e(post_url($r['slug'])) . '</loc><lastmod>' . date('Y-m-d', strtotime($r['updated_at'])) . '</lastmod></url>' . "\n";
        }
        if (FEATURE_PROJECTS) {
            foreach ($pdo->query('SELECT slug, updated_at FROM projects WHERE is_published = 1') as $r) {
                $xml .= '  <url><loc>' . e(SITE_URL . project_path($r['slug'])) . '</loc><lastmod>' . date('Y-m-d', strtotime($r['updated_at'])) . '</lastmod></url>' . "\n";
            }
        }
        $xml .= '</urlset>' . "\n";
        header('Content-Type: application/xml; charset=utf-8');
        send_cacheable($xml, 200, 3600);
    }

    // GET /site
    if ($method === 'GET' && $seg === ['site']) {
        json_out(['data' => [
            'name' => setting('company_name') ?: SITE_NAME,
            'email' => setting('email') ?: null,
            'phone' => setting('phone') ?: null,
            'address' => setting('address') ?: null,
        ]], 200, 300);
    }

    // GET /projects, /projects/{slug}
    if ($method === 'GET' && ($seg[0] ?? '') === 'projects' && count($seg) <= 2) {
        if (!FEATURE_PROJECTS) {
            api_error('Not found', 404);
        }
        if (count($seg) === 2) {
            $stmt = $pdo->prepare('SELECT * FROM projects WHERE slug = ? AND is_published = 1');
            $stmt->execute([$seg[1]]);
            $project = $stmt->fetch();
            if (!$project) {
                api_error('Project not found', 404);
            }
            json_out(['data' => project_resource($project, true)], 200, 60);
        }
        $sql = 'SELECT * FROM projects WHERE is_published = 1';
        $params = [];
        if (!empty($_GET['featured'])) {
            $sql .= ' AND featured = 1';
        }
        $status = (string) ($_GET['status'] ?? '');
        if (in_array($status, ['planned', 'ongoing', 'completed', 'on_hold'], true)) {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY featured DESC, sort_order ASC, COALESCE(end_date, start_date, created_at) DESC LIMIT ' . query_int('limit', 50, 1, 100);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        json_out(['data' => array_map('project_resource', $stmt->fetchAll())], 200, 60);
    }

    // POST /contact
    if ($method === 'POST' && $seg === ['contact']) {
        $ctype = $_SERVER['CONTENT_TYPE'] ?? '';
        $in = str_contains($ctype, 'application/json')
            ? (json_decode((string) file_get_contents('php://input', false, null, 0, 20000), true) ?: [])
            : $_POST;
        $get = static fn (string $k, int $max): string => mb_substr(trim(is_string($in[$k] ?? null) ? $in[$k] : ''), 0, $max);

        // Honeypot: real visitors never fill the hidden "website" field. Pretend success.
        if ($get('website', 200) !== '') {
            json_out(['ok' => true], 201);
        }

        $ip = client_ip();
        if (throttle_hits('contact', $ip, 3600) >= 5) {
            api_error('Too many messages from your connection. Please try again later or call us.', 429);
        }

        $data = [
            'name' => $get('name', 120),
            'email' => strtolower($get('email', 190)),
            'phone' => $get('phone', 40),
            'subject' => $get('subject', 190),
            'message' => $get('message', 5000),
        ];
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Please enter your name.';
        }
        if ($data['message'] === '') {
            $errors['message'] = 'Please enter a message.';
        }
        if ($data['email'] === '' && $data['phone'] === '') {
            $errors['email'] = 'Please give an email address or phone number so we can reply.';
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if ($errors) {
            api_error('Please check the form.', 422, ['fields' => $errors]);
        }

        throttle_hit('contact', $ip);
        $pdo->prepare('INSERT INTO contact_messages (name, email, phone, subject, message, ip) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$data['name'], $data['email'] ?: null, $data['phone'] ?: null, $data['subject'] ?: null, $data['message'], $ip]);

        $notify = setting('notify_email');
        if ($notify !== '' && function_exists('mail')) {
            $site = setting('company_name') ?: SITE_NAME;
            $clean = static fn (string $s): string => str_replace(["\r", "\n"], ' ', $s);
            $headers = ['Content-Type: text/plain; charset=UTF-8'];
            $from = env('MAIL_FROM');
            if ($from) {
                $headers[] = 'From: ' . $clean($from);
            }
            if ($data['email'] !== '') {
                $headers[] = 'Reply-To: ' . $clean($data['email']);
            }
            $body = "New message from the $site website\n\n"
                . "Name: {$data['name']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nSubject: {$data['subject']}\n\n{$data['message']}\n\n"
                . 'Inbox: ' . BACKEND_URL . "/admin/messages.php\n";
            @mail($notify, '=?UTF-8?B?' . base64_encode($clean("New enquiry: " . ($data['subject'] ?: $data['name']))) . '?=', $body, implode("\r\n", $headers));
        }

        json_out(['ok' => true], 201);
    }

    if (in_array($path, ['posts', 'contact', 'site', 'projects', 'sitemap.xml'], true)) {
        api_error('Method not allowed', 405);
    }
    api_error('Not found', 404);
} catch (Throwable $e) {
    error_log('OnPoint API error: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    api_error('Something went wrong. Please try again.', 500);
}
