<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

$admin = require_admin();
$pdo = db();

$id = (int) ($_GET['id'] ?? 0);
$post = [
    'id' => 0, 'title' => '', 'slug' => '', 'excerpt' => '', 'body_html' => '', 'cover_image' => null, 'cover_alt' => '',
    'status' => 'draft', 'published_at' => null, 'seo_title' => '', 'seo_description' => '', 'takeaways' => '', 'faq_json' => null,
];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) {
        flash('error', 'That post no longer exists.');
        redirect('posts.php');
    }
}

$errors = [];
if (is_post() && request_too_large()) {
    flash('error', oversize_message());
    redirect('post_edit.php' . ($id ? '?id=' . $id : ''));
}
if (is_post()) {
    csrf_verify();

    $post['title'] = input('title');
    $post['excerpt'] = input('excerpt');
    $post['body_html'] = sanitize_html((string) ($_POST['body_html'] ?? ''));
    $post['cover_alt'] = input('cover_alt');
    $post['seo_title'] = input('seo_title');
    $post['seo_description'] = input('seo_description');
    $requestedSlug = input('slug');

    // "Answer-ready" content: key takeaways (one per line) and FAQs
    $lines = array_filter(array_map('trim', preg_split('/\R/', (string) ($_POST['takeaways'] ?? '')) ?: []));
    $post['takeaways'] = implode("\n", array_map(static fn ($l) => mb_substr($l, 0, 300), array_slice($lines, 0, 8)));
    $faqs = [];
    $qs = $_POST['faq_q'] ?? [];
    $as = $_POST['faq_a'] ?? [];
    if (is_array($qs) && is_array($as)) {
        foreach ($qs as $i => $q) {
            $q = trim((string) $q);
            $a = trim((string) ($as[$i] ?? ''));
            if ($q !== '' && $a !== '') {
                $faqs[] = ['q' => mb_substr($q, 0, 250), 'a' => mb_substr($a, 0, 1500)];
            }
            if (count($faqs) >= 10) {
                break;
            }
        }
    }
    $post['faq_json'] = $faqs ? json_encode($faqs, JSON_UNESCAPED_UNICODE) : null;
    $action = $_POST['action'] ?? 'save';

    if ($post['title'] === '') {
        $errors[] = 'Give the post a title.';
    }
    if (mb_strlen($post['title']) > 255) {
        $errors[] = 'The title is too long (255 characters max).';
    }
    if (mb_strlen($post['excerpt']) > 500) {
        $errors[] = 'The summary is too long (500 characters max).';
    }

    // Status / date
    if ($action === 'publish') {
        $post['status'] = 'published';
    } elseif ($action === 'unpublish') {
        $post['status'] = 'draft';
    }
    $chosenDate = to_mysql_datetime(input('published_at'));
    if ($post['status'] === 'published') {
        $post['published_at'] = $chosenDate ?? $post['published_at'] ?? date('Y-m-d H:i:s');
        if (trim(strip_tags($post['body_html'])) === '' && !str_contains($post['body_html'], '<img')) {
            $errors[] = 'A published post needs some content.';
        }
    } else {
        $post['published_at'] = $chosenDate;
    }

    // Cover image
    if (!empty($_POST['remove_cover'])) {
        $oldCover = $post['cover_image'];
        $post['cover_image'] = null;
    }
    if (!empty($_FILES['cover']['name'])) {
        $up = store_image_upload($_FILES['cover'], 'posts');
        if ($up['ok']) {
            $oldCover = $post['cover_image'];
            $post['cover_image'] = $up['path'];
        } else {
            $errors[] = 'Cover image: ' . $up['error'];
        }
    }

    if (!$errors) {
        $oldSlug = (string) $post['slug']; // what the DB has now (empty for a new post)
        $post['slug'] = unique_slug('posts', $requestedSlug !== '' ? $requestedSlug : $post['title'], $post['id'] ?: null);
        if (empty($post['excerpt'])) {
            $post['excerpt'] = excerpt_from_html($post['body_html'], 200) ?: null;
        }
        $fields = [
            $post['title'], $post['slug'], $post['excerpt'] ?: null, $post['body_html'], $post['cover_image'], $post['cover_alt'] ?: null,
            $post['status'], $post['published_at'], $post['seo_title'] ?: null, $post['seo_description'] ?: null,
            $post['takeaways'] ?: null, $post['faq_json'],
        ];
        if ($post['id']) {
            $pdo->prepare(
                'UPDATE posts SET title=?, slug=?, excerpt=?, body_html=?, cover_image=?, cover_alt=?, status=?, published_at=?, seo_title=?, seo_description=?, takeaways=?, faq_json=? WHERE id=?'
            )->execute([...$fields, $post['id']]);
        } else {
            $pdo->prepare(
                'INSERT INTO posts (title, slug, excerpt, body_html, cover_image, cover_alt, status, published_at, seo_title, seo_description, takeaways, faq_json, author_id)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)'
            )->execute([...$fields, $admin['id']]);
            $post['id'] = (int) $pdo->lastInsertId();
        }
        if (!empty($oldCover) && $oldCover !== $post['cover_image']) {
            delete_upload($oldCover);
        }

        // Keep old links alive (301) when the slug changed, and tell search engines about live posts
        if ($oldSlug !== '' && $oldSlug !== $post['slug']) {
            remember_old_slug('post', $oldSlug, (int) $post['id']);
        }
        forget_slug_redirect('post', $post['slug']);
        if ($post['status'] === 'published' && strtotime((string) $post['published_at']) <= time()) {
            indexnow_ping([SITE_URL . post_path($post['slug'])]);
        }

        $messages = [
            'publish' => strtotime((string) $post['published_at']) > time() ? 'Post scheduled for ' . format_date($post['published_at'], 'j M Y, H:i') . '.' : 'Post published.',
            'unpublish' => 'Post moved to drafts.',
            'save' => 'Changes saved.',
        ];
        flash('success', $messages[$action] ?? 'Changes saved.');
        redirect('post_edit.php?id=' . $post['id']);
    } elseif (!empty($up['ok'])) {
        // Don't keep an orphaned upload when validation failed
        delete_upload($up['path']);
        $post['cover_image'] = $oldCover ?? $post['cover_image'];
    }
}

$isNew = !$post['id'];
$state = $isNew ? 'draft' : post_state($post);
$publicUrl = SITE_URL . rtrim(BLOG_PATH, '/') . '/' . ($post['slug'] ?: 'your-post');
$tinymce = '<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js" referrerpolicy="origin"></script>';

admin_header($isNew ? 'New post' : 'Edit post', 'posts', [
    'actions' => ($isNew ? '' : status_badge($state)) . '<a class="btn btn-ghost" href="posts.php">Back to posts</a>',
]);
?>
<?php foreach ($errors as $err): ?><div class="alert alert-error" role="alert"><?= e($err) ?></div><?php endforeach; ?>

<form method="post" enctype="multipart/form-data" data-dirty-guard id="post-form">
  <?= csrf_field() ?>
  <div class="form-grid">
    <div class="stack">
      <section class="card card-body">
        <div class="field">
          <label for="title" class="sr-only">Title</label>
          <input type="text" id="title" name="title" class="title-input" value="<?= e($post['title']) ?>" placeholder="Post title" required maxlength="255" data-slug-source>
        </div>
        <div class="field">
          <label for="body_html">Content</label>
          <div><button type="button" class="btn btn-ghost btn-sm" data-add-image>📷 Add image</button> <span class="hint">Upload a photo from your computer or phone — it is stored on this website. You can also paste or drag images into the text.</span></div>
          <textarea id="body_html" name="body_html" rows="20"><?= e($post['body_html']) ?></textarea>
        </div>
      </section>

      <section class="card card-body">
        <h2 style="margin-bottom:16px">Summary &amp; search</h2>
        <div class="field">
          <label for="excerpt">Summary <span class="hint">— shown on the blog list. Leave empty to use the first lines of the post.</span></label>
          <textarea id="excerpt" name="excerpt" rows="3" maxlength="500" data-count="200"><?= e($post['excerpt']) ?></textarea>
        </div>
        <div class="row-2" style="margin-top:16px">
          <div class="field">
            <label for="seo_title">Search title <span class="hint">(optional)</span></label>
            <input type="text" id="seo_title" name="seo_title" value="<?= e($post['seo_title']) ?>" maxlength="255" data-count="60" placeholder="Defaults to the post title">
          </div>
          <div class="field">
            <label for="seo_description">Search description <span class="hint">(optional)</span></label>
            <input type="text" id="seo_description" name="seo_description" value="<?= e($post['seo_description']) ?>" maxlength="320" data-count="160" placeholder="Defaults to the summary">
          </div>
        </div>
        <div class="field">
          <label>Google preview</label>
          <div class="serp" aria-live="polite">
            <div class="serp-url" id="serp-url"><?= e($publicUrl) ?></div>
            <div class="serp-title" id="serp-title"><?= e($post['seo_title'] ?: $post['title'] ?: 'Post title') ?></div>
            <div class="serp-desc" id="serp-desc"><?= e($post['seo_description'] ?: $post['excerpt'] ?: 'Your summary will appear here.') ?></div>
          </div>
        </div>
      </section>
      <section class="card card-body">
        <h2 style="margin-bottom:6px">Answer-ready content</h2>
        <p class="hint" style="margin:0 0 16px">Short, direct answers are what Google’s snippets and AI assistants (ChatGPT, Gemini, Perplexity) quote. Only write things that are true for your business.</p>
        <div class="field">
          <label for="takeaways">Key takeaways <span class="hint">— 2 to 5 short points, one per line</span></label>
          <textarea id="takeaways" name="takeaways" rows="4" placeholder="Example: Clean restaurant floors daily with a slip-resistant degreaser."><?= e($post['takeaways']) ?></textarea>
        </div>
        <div class="field">
          <label>Frequently asked questions <span class="hint">— real questions customers ask, with a clear answer</span></label>
          <div id="faq-rows">
            <?php $faqRows = post_faqs($post); if (!$faqRows) { $faqRows = [['q' => '', 'a' => '']]; } ?>
            <?php foreach ($faqRows as $f): ?>
              <div class="faq-row">
                <input type="text" name="faq_q[]" value="<?= e($f['q']) ?>" placeholder="Question" maxlength="250">
                <textarea name="faq_a[]" rows="2" placeholder="Answer" maxlength="1500"><?= e($f['a']) ?></textarea>
              </div>
            <?php endforeach; ?>
          </div>
          <div><button type="button" class="btn btn-ghost btn-sm" id="add-faq">+ Add another question</button></div>
        </div>
      </section>
    </div>

    <aside class="stack">
      <section class="card card-body">
        <h2 style="margin-bottom:16px">Publish</h2>
        <div class="field">
          <label for="published_at">Publish date <span class="hint">— set a future date to schedule</span></label>
          <input type="datetime-local" id="published_at" name="published_at" value="<?= $post['published_at'] ? e(date('Y-m-d\TH:i', strtotime($post['published_at']))) : '' ?>">
        </div>
        <div class="form-actions" style="margin-top:20px">
          <?php if ($post['status'] === 'published'): ?>
            <button class="btn btn-brand" name="action" value="publish">Update</button>
            <button class="btn btn-ghost" name="action" value="unpublish">Move to drafts</button>
          <?php else: ?>
            <button class="btn btn-brand" name="action" value="publish">Publish</button>
            <button class="btn btn-ghost" name="action" value="save">Save draft</button>
          <?php endif; ?>
        </div>
        <?php if ($state === 'published'): ?>
          <p class="sub" style="margin:14px 0 0">Live at <a href="<?= e($publicUrl) ?>" target="_blank" rel="noopener"><?= e($publicUrl) ?></a></p>
        <?php endif; ?>
      </section>

      <section class="card card-body">
        <h2 style="margin-bottom:16px">Cover image</h2>
        <div class="cover-preview" id="cover-preview" <?= $post['cover_image'] ? '' : 'hidden' ?>>
          <?php if ($post['cover_image']): ?><img src="<?= e(upload_url($post['cover_image'])) ?>" alt=""><?php endif; ?>
        </div>
        <label class="dropzone">
          <span>Choose an image<br><span class="hint">JPG, PNG or WebP · up to <?= UPLOAD_MAX_BYTES / 1024 / 1024 ?> MB · 1600×900 works best</span></span>
          <input type="file" name="cover" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="#cover-preview">
        </label>
        <?php if ($post['cover_image']): ?>
          <label class="check" style="margin-top:10px"><input type="checkbox" name="remove_cover" value="1"> Remove cover image</label>
        <?php endif; ?>
        <div class="field" style="margin-top:16px">
          <label for="cover_alt">Image description <span class="hint">(for screen readers)</span></label>
          <input type="text" id="cover_alt" name="cover_alt" value="<?= e($post['cover_alt']) ?>" maxlength="255">
        </div>
      </section>

      <section class="card card-body">
        <h2 style="margin-bottom:16px">Link</h2>
        <div class="field">
          <label for="slug">URL slug</label>
          <input type="text" id="slug" name="slug" value="<?= e($post['slug']) ?>" maxlength="180" pattern="[a-z0-9\-]*" data-slug-target placeholder="generated-from-title">
          <span class="slug-preview"><?= e(SITE_URL . rtrim(BLOG_PATH, '/')) ?>/<strong id="slug-out"><?= e($post['slug'] ?: '…') ?></strong></span>
        </div>
      </section>

      <section class="card card-body">
        <h2 style="margin-bottom:6px">SEO checklist <span class="hint" id="seo-score"></span></h2>
        <ul class="checklist" id="seo-checklist" aria-live="polite"></ul>
      </section>

      <?php if (!$isNew): ?>
      <section class="card card-body">
        <p class="sub" style="margin:0 0 12px">Created <?= format_date($post['created_at'], 'j M Y, H:i') ?><br>Last edited <?= format_date($post['updated_at'], 'j M Y, H:i') ?></p>
      </section>
      <?php endif; ?>
    </aside>
  </div>
</form>

<?php if (!$isNew): ?>
  <div style="margin-top:20px"><?= delete_button('posts.php', (int) $post['id'], 'post') ?></div>
<?php endif; ?>

<?php
$csrf = json_encode(csrf_token());
$base = json_encode(SITE_URL . rtrim(BLOG_PATH, '/') . '/');
admin_footer([$tinymce, <<<HTML
<script>
tinymce.init({
  selector: '#body_html',
  license_key: 'gpl',
  height: 560,
  menubar: false,
  toolbar_mode: 'wrap',
  branding: false,
  promotion: false,
  plugins: 'lists link image table code autoresize media wordcount',
  toolbar: 'blocks | bold italic underline | bullist numlist blockquote | image link media table | removeformat code',
  block_formats: 'Paragraph=p; Heading=h2; Subheading=h3; Small heading=h4',
  content_style: 'body{font-family:Manrope,Arial,sans-serif;font-size:16px;line-height:1.7;max-width:760px;margin:16px auto;color:#1a2330} img{max-width:100%;height:auto}',
  link_default_target: '_blank',
  image_caption: true,
  image_dimensions: false,
  convert_urls: false,
  automatic_uploads: true,
  paste_data_images: true,
  images_file_types: 'jpg,jpeg,png,webp,gif',
  images_upload_handler: function (blobInfo) {
    var blob = blobInfo.blob();
    return OPImage.upload(new File([blob], blobInfo.filename(), { type: blob.type }), $csrf);
  },
  setup: function (ed) { ed.on('change input', function () { ed.save(); }); }
});

// Add FAQ rows
(function () {
  var wrap = document.getElementById('faq-rows'), add = document.getElementById('add-faq');
  if (!wrap || !add) return;
  add.addEventListener('click', function () {
    var row = document.createElement('div');
    row.className = 'faq-row';
    row.innerHTML = '<input type="text" name="faq_q[]" placeholder="Question" maxlength="250"><textarea name="faq_a[]" rows="2" placeholder="Answer" maxlength="1500"></textarea>';
    wrap.appendChild(row);
    row.querySelector('input').focus();
  });
})();

// Live SEO checklist
(function () {
  var list = document.getElementById('seo-checklist'), score = document.getElementById('seo-score');
  if (!list) return;
  function val(id) { var el = document.getElementById(id); return el ? el.value.trim() : ''; }
  function bodyHtml() { return window.tinymce && tinymce.activeEditor ? tinymce.activeEditor.getContent() : ''; }
  function plain(h) { var d = document.createElement('div'); d.innerHTML = h; return d.textContent || ''; }
  function run() {
    var html = bodyHtml(), words = (plain(html).trim().match(/\S+/g) || []).length;
    var title = val('seo_title') || val('title'), desc = val('seo_description') || val('excerpt');
    var cover = document.getElementById('cover-preview');
    var hasCover = !!(cover && !cover.hidden && cover.querySelector('img'));
    var faqs = 0;
    document.querySelectorAll('[name="faq_q[]"]').forEach(function (i) { if (i.value.trim()) faqs++; });
    var take = val('takeaways').split(String.fromCharCode(10)).filter(function (x) { return x.trim(); }).length;
    var items = [
      [title.length >= 30 && title.length <= 65, 'Search title is 30–65 characters (' + title.length + ')'],
      [desc.length >= 70 && desc.length <= 160, 'Search description is 70–160 characters (' + desc.length + ')'],
      [words >= 300, 'At least 300 words (' + words + ')'],
      [/<h[23][ >]/i.test(html), 'Has subheadings'],
      [/<a [^>]*href=/i.test(html), 'Links to another page or article'],
      [hasCover && val('cover_alt').length > 0, 'Cover image with a description'],
      [take >= 2, 'Key takeaways added (2 or more)'],
      [faqs >= 2, 'At least 2 FAQs']
    ];
    var ok = 0;
    list.innerHTML = '';
    items.forEach(function (it) {
      var li = document.createElement('li');
      li.className = it[0] ? 'ok' : '';
      li.textContent = it[1];
      list.appendChild(li);
      if (it[0]) ok++;
    });
    score.textContent = '· ' + ok + '/' + items.length;
  }
  var t;
  function later() { clearTimeout(t); t = setTimeout(run, 250); }
  document.getElementById('post-form').addEventListener('input', later);
  document.getElementById('post-form').addEventListener('change', later);
  if (window.tinymce) tinymce.on('AddEditor', function (e) { e.editor.on('keyup change SetContent', later); });
  setTimeout(run, 1200);
})();

// Live search preview + slug preview
(function () {
  var t = document.getElementById('title'), st = document.getElementById('seo_title'),
      sd = document.getElementById('seo_description'), ex = document.getElementById('excerpt'),
      slug = document.getElementById('slug'), base = $base;
  function sync() {
    var s = slug.value || slug.placeholder || 'your-post';
    document.getElementById('serp-title').textContent = st.value || t.value || 'Post title';
    document.getElementById('serp-desc').textContent = sd.value || ex.value || 'Your summary will appear here.';
    document.getElementById('serp-url').textContent = base + s;
    document.getElementById('slug-out').textContent = s;
  }
  [t, st, sd, ex, slug].forEach(function (el) { el.addEventListener('input', function () { setTimeout(sync, 0); }); });
})();
</script>
HTML]);
