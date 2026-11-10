<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

require_admin();
if (!FEATURE_PROJECTS) {
    redirect('index.php');
}
$pdo = db();

$statuses = ['planned' => 'Planned', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On hold'];
$id = (int) ($_GET['id'] ?? 0);
$project = [
    'id' => 0, 'title' => '', 'slug' => '', 'client' => '', 'location' => '', 'category' => '', 'start_date' => null, 'end_date' => null,
    'status' => 'ongoing', 'summary' => '', 'body_html' => '', 'cover_image' => null, 'cover_alt' => '', 'featured' => 0,
    'is_published' => 1, 'sort_order' => 0,
];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        flash('error', 'That project no longer exists.');
        redirect('projects.php');
    }
}

$errors = [];
if (is_post() && request_too_large()) {
    flash('error', oversize_message());
    redirect('project_edit.php' . ($id ? '?id=' . $id : ''));
}
if (is_post()) {
    csrf_verify();
    foreach (['title', 'client', 'location', 'category', 'summary', 'cover_alt'] as $f) {
        $project[$f] = input($f);
    }
    $project['body_html'] = sanitize_html((string) ($_POST['body_html'] ?? ''));
    $project['status'] = isset($statuses[input('status')]) ? input('status') : 'ongoing';
    $project['start_date'] = input('start_date') ?: null;
    $project['end_date'] = input('end_date') ?: null;
    $project['featured'] = !empty($_POST['featured']) ? 1 : 0;
    $project['is_published'] = !empty($_POST['is_published']) ? 1 : 0;
    $project['sort_order'] = (int) input('sort_order', '0');

    if ($project['title'] === '') {
        $errors[] = 'Give the project a title.';
    }
    foreach (['start_date', 'end_date'] as $f) {
        if ($project[$f] && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $project[$f])) {
            $errors[] = 'Use a valid date.';
            $project[$f] = null;
        }
    }
    if ($project['start_date'] && $project['end_date'] && $project['end_date'] < $project['start_date']) {
        $errors[] = 'The end date can’t be before the start date.';
    }
    if (mb_strlen($project['summary']) > 500) {
        $errors[] = 'The summary is too long (500 characters max).';
    }

    if (!empty($_POST['remove_cover'])) {
        $oldCover = $project['cover_image'];
        $project['cover_image'] = null;
    }
    if (!empty($_FILES['cover']['name'])) {
        $up = store_image_upload($_FILES['cover'], 'projects');
        if ($up['ok']) {
            $oldCover = $project['cover_image'];
            $project['cover_image'] = $up['path'];
        } else {
            $errors[] = 'Cover image: ' . $up['error'];
        }
    }

    if (!$errors) {
        $oldSlug = (string) $project['slug'];
        $project['slug'] = unique_slug('projects', input('slug') ?: $project['title'], $project['id'] ?: null);
        $fields = [
            $project['title'], $project['slug'], $project['client'] ?: null, $project['location'] ?: null, $project['category'] ?: null,
            $project['start_date'], $project['end_date'], $project['status'], $project['summary'] ?: null, $project['body_html'],
            $project['cover_image'], $project['cover_alt'] ?: null, $project['featured'], $project['is_published'], $project['sort_order'],
        ];
        if ($project['id']) {
            $pdo->prepare(
                'UPDATE projects SET title=?, slug=?, client=?, location=?, category=?, start_date=?, end_date=?, status=?, summary=?, body_html=?,
                 cover_image=?, cover_alt=?, featured=?, is_published=?, sort_order=? WHERE id=?'
            )->execute([...$fields, $project['id']]);
        } else {
            $pdo->prepare(
                'INSERT INTO projects (title, slug, client, location, category, start_date, end_date, status, summary, body_html,
                 cover_image, cover_alt, featured, is_published, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            )->execute($fields);
            $project['id'] = (int) $pdo->lastInsertId();
        }
        if (!empty($oldCover) && $oldCover !== $project['cover_image']) {
            delete_upload($oldCover);
        }
        if ($oldSlug !== '' && $oldSlug !== $project['slug']) {
            remember_old_slug('project', $oldSlug, (int) $project['id']);
        }
        forget_slug_redirect('project', $project['slug']);
        if ($project['is_published']) {
            indexnow_ping([SITE_URL . project_path($project['slug'])]);
        }
        flash('success', 'Project saved.');
        redirect('project_edit.php?id=' . $project['id']);
    } elseif (!empty($up['ok'])) {
        delete_upload($up['path']);
        $project['cover_image'] = $oldCover ?? $project['cover_image'];
    }
}

$isNew = !$project['id'];
admin_header($isNew ? 'New project' : 'Edit project', 'projects', [
    'actions' => '<a class="btn btn-ghost" href="projects.php">Back to projects</a>',
]);
?>
<?php foreach ($errors as $err): ?><div class="alert alert-error" role="alert"><?= e($err) ?></div><?php endforeach; ?>

<form method="post" enctype="multipart/form-data" data-dirty-guard>
  <?= csrf_field() ?>
  <div class="form-grid">
    <div class="stack">
      <section class="card card-body">
        <div class="field">
          <label for="title" class="sr-only">Project title</label>
          <input type="text" id="title" name="title" class="title-input" value="<?= e($project['title']) ?>" placeholder="Project title" required maxlength="255" data-slug-source>
        </div>
        <div class="row-2" style="margin-top:16px">
          <div class="field"><label for="client">Client</label><input type="text" id="client" name="client" value="<?= e($project['client']) ?>" maxlength="190"></div>
          <div class="field"><label for="location">Location</label><input type="text" id="location" name="location" value="<?= e($project['location']) ?>" maxlength="190"></div>
        </div>
        <div class="field">
          <label for="summary">Summary <span class="hint">— shown on project cards</span></label>
          <textarea id="summary" name="summary" rows="3" maxlength="500" data-count="200"><?= e($project['summary']) ?></textarea>
        </div>
        <div class="field">
          <label for="body_html">Full description</label>
          <div><button type="button" class="btn btn-ghost btn-sm" data-add-image>📷 Add image</button> <span class="hint">Photos are stored on this website.</span></div>
          <textarea id="body_html" name="body_html" rows="14"><?= e($project['body_html']) ?></textarea>
        </div>
      </section>
    </div>

    <aside class="stack">
      <section class="card card-body">
        <h2 style="margin-bottom:16px">Status</h2>
        <div class="field">
          <label for="status">Project status</label>
          <select id="status" name="status">
            <?php foreach ($statuses as $k => $label): ?><option value="<?= $k ?>" <?= $project['status'] === $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="row-2" style="margin-top:16px">
          <div class="field"><label for="start_date">Start</label><input type="date" id="start_date" name="start_date" value="<?= e($project['start_date']) ?>"></div>
          <div class="field"><label for="end_date">End</label><input type="date" id="end_date" name="end_date" value="<?= e($project['end_date']) ?>"></div>
        </div>
        <div class="field">
          <label for="category">Category <span class="hint">(e.g. Roads, Water)</span></label>
          <input type="text" id="category" name="category" value="<?= e($project['category']) ?>" maxlength="120">
        </div>
        <div class="field" style="gap:10px">
          <label class="check"><input type="checkbox" name="is_published" value="1" <?= $project['is_published'] ? 'checked' : '' ?>> Show on website</label>
          <label class="check"><input type="checkbox" name="featured" value="1" <?= $project['featured'] ? 'checked' : '' ?>> Feature on homepage</label>
        </div>
        <div class="field">
          <label for="sort_order">Order <span class="hint">— lower shows first</span></label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $project['sort_order'] ?>">
        </div>
        <div class="form-actions" style="margin-top:20px"><button class="btn btn-brand">Save project</button></div>
      </section>

      <section class="card card-body">
        <h2 style="margin-bottom:16px">Cover image</h2>
        <div class="cover-preview" id="cover-preview" <?= $project['cover_image'] ? '' : 'hidden' ?>>
          <?php if ($project['cover_image']): ?><img src="<?= e(upload_url($project['cover_image'])) ?>" alt=""><?php endif; ?>
        </div>
        <label class="dropzone">
          <span>Choose an image<br><span class="hint">JPG, PNG or WebP · up to <?= UPLOAD_MAX_BYTES / 1024 / 1024 ?> MB</span></span>
          <input type="file" name="cover" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="#cover-preview">
        </label>
        <?php if ($project['cover_image']): ?>
          <label class="check" style="margin-top:10px"><input type="checkbox" name="remove_cover" value="1"> Remove cover image</label>
        <?php endif; ?>
        <div class="field" style="margin-top:16px">
          <label for="cover_alt">Image description</label>
          <input type="text" id="cover_alt" name="cover_alt" value="<?= e($project['cover_alt']) ?>" maxlength="255">
        </div>
      </section>

      <section class="card card-body">
        <div class="field">
          <label for="slug">URL slug</label>
          <input type="text" id="slug" name="slug" value="<?= e($project['slug']) ?>" maxlength="180" pattern="[a-z0-9\-]*" data-slug-target placeholder="generated-from-title">
        </div>
      </section>
    </aside>
  </div>
</form>
<?php if (!$isNew): ?><div style="margin-top:20px"><?= delete_button('projects.php', (int) $project['id'], 'project') ?></div><?php endif; ?>

<?php
$csrf = json_encode(csrf_token());
admin_footer(['<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js" referrerpolicy="origin"></script>', <<<HTML
<script>
tinymce.init({
  selector: '#body_html', license_key: 'gpl', height: 420, menubar: false,
  toolbar_mode: 'wrap', branding: false, promotion: false,
  plugins: 'lists link image table autoresize', toolbar: 'blocks | bold italic | bullist numlist | image link table | removeformat',
  block_formats: 'Paragraph=p; Heading=h2; Subheading=h3', convert_urls: false, image_dimensions: false, automatic_uploads: true, paste_data_images: true,
  content_style: 'body{font-family:Manrope,Arial,sans-serif;font-size:16px;line-height:1.7;color:#1a2330} img{max-width:100%;height:auto}',
  images_upload_handler: function (blobInfo) {
    var blob = blobInfo.blob();
    return OPImage.upload(new File([blob], blobInfo.filename(), { type: blob.type }), $csrf);
  },
  setup: function (ed) { ed.on('change input', function () { ed.save(); }); }
});
</script>
HTML]);
