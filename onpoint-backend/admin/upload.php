<?php
/** Image upload endpoint for the rich-text editor. Returns {"location": url}. */
require __DIR__ . '/../src/bootstrap.php';

start_session();
header('Content-Type: application/json');
header('Cache-Control: no-store');

if (!current_admin()) {
    http_response_code(401);
    exit(json_encode(['error' => 'Sign in again to upload images.']));
}
if (!is_post()) {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}
if (request_too_large()) {
    http_response_code(413);
    exit(json_encode(['error' => oversize_message()]));
}
$sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!hash_equals(csrf_token(), (string) $sent)) {
    http_response_code(419);
    exit(json_encode(['error' => 'Your session expired. Refresh the page.']));
}

$result = store_image_upload($_FILES['file'] ?? [], 'content');
if (!$result['ok']) {
    http_response_code(422);
    exit(json_encode(['error' => $result['error']]));
}
echo json_encode(['location' => $result['url']]);
