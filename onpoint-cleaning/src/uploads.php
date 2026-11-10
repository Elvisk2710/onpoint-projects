<?php
declare(strict_types=1);

/**
 * Validate and store an uploaded image.
 * The type is detected from the file's bytes (finfo + getimagesize), never from the browser.
 *
 * @return array{ok: bool, path?: string, url?: string, error?: string}
 */
function store_image_upload(array $file, string $subdir = 'images'): array
{
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'The file is larger than the server allows.',
        UPLOAD_ERR_FORM_SIZE => 'The file is too large.',
        UPLOAD_ERR_PARTIAL => 'The upload was interrupted. Try again.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
    ];
    $code = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($code !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => $errors[$code] ?? 'Upload failed.'];
    }
    if (!is_uploaded_file($file['tmp_name'] ?? '')) {
        return ['ok' => false, 'error' => 'Upload failed.'];
    }
    if (($file['size'] ?? 0) > UPLOAD_MAX_BYTES) {
        return ['ok' => false, 'error' => 'Images must be ' . (UPLOAD_MAX_BYTES / 1024 / 1024) . ' MB or smaller.'];
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        return ['ok' => false, 'error' => 'Only JPG, PNG, GIF and WebP images are allowed.'];
    }

    // Defence in depth: a valid image header can still carry a script payload (polyglot files)
    $contents = file_get_contents($file['tmp_name']);
    if ($contents === false || preg_match('/<\?(php|=)|<script/i', $contents)) {
        return ['ok' => false, 'error' => 'That file contains code and was rejected. Upload a plain image.'];
    }
    unset($contents);

    $subdir = preg_replace('/[^a-z0-9_-]/', '', $subdir) ?: 'images';
    $relDir = $subdir . '/' . date('Y/m');
    $absDir = UPLOAD_DIR . '/' . $relDir;
    if (!is_dir($absDir) && !mkdir($absDir, 0755, true) && !is_dir($absDir)) {
        error_log("OnPoint backend: cannot create $absDir");
        return ['ok' => false, 'error' => 'The server could not save the file.'];
    }

    $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], "$absDir/$name")) {
        return ['ok' => false, 'error' => 'The server could not save the file.'];
    }
    @chmod("$absDir/$name", 0644);
    optimize_image("$absDir/$name", $mime);

    $path = "uploads/$relDir/$name";
    return ['ok' => true, 'path' => $path, 'url' => upload_url($path)];
}

/** Delete a previously stored upload. Only touches files inside UPLOAD_DIR. */
function delete_upload(?string $path): void
{
    if (!$path || !str_starts_with($path, 'uploads/')) {
        return;
    }
    $abs = realpath(BACKEND_ROOT . '/' . $path);
    $base = realpath(UPLOAD_DIR);
    if ($abs && $base && str_starts_with($abs, $base . DIRECTORY_SEPARATOR) && is_file($abs)) {
        @unlink($abs);
    }
}


/** True when the browser sent a body larger than PHP's post_max_size (PHP then empties $_POST and $_FILES). */
function request_too_large(): bool
{
    return (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0 && empty($_POST) && empty($_FILES);
}

function oversize_message(): string
{
    return 'That upload is too big for the server (limit ' . ini_get('post_max_size')
        . '). Photos are normally shrunk automatically; try a smaller image.';
}

/**
 * If PHP's GD extension is present: fix phone-photo rotation, cap the longest side at 2000px,
 * and re-encode (this also strips EXIF/GPS data). Without GD the file is kept as uploaded.
 */
function optimize_image(string $path, string $mime, int $maxSide = 2000): void
{
    if ($mime === 'image/gif' || !function_exists('imagecreatetruecolor')) {
        return; // animated GIFs would lose frames; no GD = leave the file alone
    }
    $size = @getimagesize($path);
    if (!$size || $size[0] * $size[1] > 40_000_000) {
        return; // unreadable, or too many pixels to safely decode on shared hosting
    }
    $img = match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($path),
        'image/png' => @imagecreatefrompng($path),
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
        default => false,
    };
    if (!$img) {
        return;
    }
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $orientation = @exif_read_data($path)['Orientation'] ?? 1;
        $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
        if ($angle && ($rot = imagerotate($img, $angle, 0))) {
            $img = $rot;
        }
    }
    $w = imagesx($img);
    $h = imagesy($img);
    if (max($w, $h) > $maxSide) {
        $scale = $maxSide / max($w, $h);
        if ($small = imagescale($img, (int) round($w * $scale), (int) round($h * $scale))) {
            $img = $small;
        }
    }
    $tmp = $path . '.tmp';
    $ok = match ($mime) {
        'image/jpeg' => imagejpeg($img, $tmp, 82),
        'image/png' => (function () use ($img, $tmp) { imagesavealpha($img, true); return imagepng($img, $tmp, 6); })(),
        'image/webp' => imagewebp($img, $tmp, 82),
    };
    if ($ok && is_file($tmp) && filesize($tmp) > 0 && filesize($tmp) < filesize($path) * 1.2) {
        rename($tmp, $path);
    } else {
        @unlink($tmp);
    }
}
