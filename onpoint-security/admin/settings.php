<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';

$admin = require_admin();
$pdo = db();

$fields = [
    'company_name' => 'Company name',
    'email' => 'Public email',
    'phone' => 'Public phone',
    'address' => 'Address',
    'notify_email' => 'Send new contact messages to',
    'same_as' => 'Social &amp; directory profile links',
];

if (is_post()) {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'company') {
        foreach (array_keys($fields) as $key) {
            $value = input($key);
            if (in_array($key, ['email', 'notify_email'], true) && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                flash('error', $fields[$key] . ' is not a valid email address.');
                redirect('settings.php');
            }
            save_setting($key, $value);
        }
        flash('success', 'Company details saved.');
    }

    if ($action === 'password') {
        $stmt = $pdo->prepare('SELECT password_hash FROM admins WHERE id = ?');
        $stmt->execute([$admin['id']]);
        $new = (string) ($_POST['new_password'] ?? '');
        if (!password_verify((string) ($_POST['current_password'] ?? ''), (string) $stmt->fetchColumn())) {
            flash('error', 'Your current password is incorrect.');
        } elseif ($problem = password_problem($new)) {
            flash('error', $problem);
        } elseif ($new !== ($_POST['confirm_password'] ?? '')) {
            flash('error', 'The new passwords don’t match.');
        } else {
            $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
            session_regenerate_id(true);
            flash('success', 'Password changed.');
        }
    }

    if ($action === 'add_admin') {
        $name = input('name');
        $email = strtolower(input('email'));
        $pass = (string) ($_POST['password'] ?? '');
        $exists = $pdo->prepare('SELECT 1 FROM admins WHERE email = ?');
        $exists->execute([$email]);
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Enter a name and a valid email for the new admin.');
        } elseif ($exists->fetch()) {
            flash('error', 'An admin with that email already exists.');
        } elseif ($problem = password_problem($pass)) {
            flash('error', $problem);
        } else {
            $pdo->prepare('INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)')
                ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
            flash('success', "$name can now sign in. Share the password with them securely.");
        }
    }

    if (in_array($action, ['deactivate', 'activate'], true)) {
        $id = (int) ($_POST['id'] ?? 0);
        $active = (int) $pdo->query('SELECT COUNT(*) FROM admins WHERE is_active = 1')->fetchColumn();
        if ($id === (int) $admin['id']) {
            flash('error', 'You can’t deactivate your own account.');
        } elseif ($action === 'deactivate' && $active <= 1) {
            flash('error', 'At least one admin must stay active.');
        } else {
            $pdo->prepare('UPDATE admins SET is_active = ? WHERE id = ?')->execute([$action === 'activate' ? 1 : 0, $id]);
            flash('success', $action === 'activate' ? 'Admin re-activated.' : 'Admin deactivated. They can no longer sign in.');
        }
    }

    redirect('settings.php');
}

$admins = $pdo->query('SELECT id, name, email, is_active, last_login_at, created_at FROM admins ORDER BY name')->fetchAll();
admin_header('Settings', 'settings');
?>
<div class="grid-2">
  <section class="card">
    <div class="card-head"><h2>Company details</h2></div>
    <form method="post" class="card-body">
      <?= csrf_field() ?><input type="hidden" name="action" value="company">
      <?php foreach ($fields as $key => $label): ?>
        <div class="field">
          <label for="<?= $key ?>"><?= html_entity_decode($label) ?></label>
          <?php if ($key === 'address' || $key === 'same_as'): ?>
            <textarea id="<?= $key ?>" name="<?= $key ?>" rows="<?= $key === 'same_as' ? 4 : 2 ?>" <?= $key === 'same_as' ? 'placeholder="https://www.facebook.com/yourpage&#10;https://www.linkedin.com/company/yourcompany&#10;https://g.page/yourbusiness"' : '' ?>><?= e(setting($key)) ?></textarea>
          <?php else: ?>
            <input type="<?= str_contains($key, 'email') ? 'email' : 'text' ?>" id="<?= $key ?>" name="<?= $key ?>" value="<?= e(setting($key)) ?>">
          <?php endif; ?>
          <?php if ($key === 'same_as'): ?><span class="hint">One web address per line (Facebook, LinkedIn, Google Business Profile, etc.). Helps Google and AI assistants recognise your business as one entity.</span><?php endif; ?>
          <?php if ($key === 'notify_email'): ?><span class="hint">Leave empty to only keep messages in the inbox.</span><?php endif; ?>
        </div>
      <?php endforeach; ?>
      <div class="form-actions" style="margin-top:20px"><button class="btn btn-brand">Save details</button></div>
    </form>
  </section>

  <section class="card">
    <div class="card-head"><h2>Change your password</h2></div>
    <form method="post" class="card-body">
      <?= csrf_field() ?><input type="hidden" name="action" value="password">
      <div class="field"><label for="current_password">Current password</label><input type="password" id="current_password" name="current_password" autocomplete="current-password" required></div>
      <div class="field"><label for="new_password">New password <span class="hint">(10+ characters)</span></label><input type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="10" required></div>
      <div class="field"><label for="confirm_password">Confirm new password</label><input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="10" required></div>
      <div class="form-actions" style="margin-top:20px"><button class="btn">Change password</button></div>
    </form>
  </section>
</div>

<section class="card" style="margin-top:20px">
  <div class="card-head"><h2>Admins</h2></div>
  <div class="table-wrap"><table>
    <thead><tr><th>Name</th><th class="hide-sm">Last sign-in</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($admins as $a): ?>
      <tr>
        <td><strong><?= e($a['name']) ?></strong><?= (int) $a['id'] === (int) $admin['id'] ? ' <span class="sub">(you)</span>' : '' ?><div class="sub"><?= e($a['email']) ?></div></td>
        <td class="hide-sm sub"><?= $a['last_login_at'] ? format_date($a['last_login_at'], 'j M Y, H:i') : 'Never' ?></td>
        <td><?= $a['is_active'] ? '<span class="badge badge-published">Active</span>' : '<span class="badge">Deactivated</span>' ?></td>
        <td class="actions">
          <?php if ((int) $a['id'] !== (int) $admin['id']): ?>
            <form method="post" class="inline" <?= $a['is_active'] ? 'data-confirm="Deactivate ' . e($a['name']) . '? They will no longer be able to sign in."' : '' ?>>
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
              <button class="btn btn-ghost btn-sm <?= $a['is_active'] ? 'btn-danger' : '' ?>" name="action" value="<?= $a['is_active'] ? 'deactivate' : 'activate' ?>"><?= $a['is_active'] ? 'Deactivate' : 'Re-activate' ?></button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <form method="post" class="card-body" style="border-top:1px solid var(--line)">
    <?= csrf_field() ?><input type="hidden" name="action" value="add_admin">
    <h3 style="margin-bottom:12px">Add an admin</h3>
    <div class="row-2">
      <div class="field"><label for="new_name">Name</label><input type="text" id="new_name" name="name" required maxlength="120"></div>
      <div class="field"><label for="new_email">Email</label><input type="email" id="new_email" name="email" required maxlength="190"></div>
    </div>
    <div class="field"><label for="new_admin_password">Temporary password <span class="hint">(10+ characters — they can change it in Settings)</span></label><input type="password" id="new_admin_password" name="password" autocomplete="new-password" minlength="10" required></div>
    <div class="form-actions" style="margin-top:16px"><button class="btn">Add admin</button></div>
  </form>
</section>
<?php admin_footer();
