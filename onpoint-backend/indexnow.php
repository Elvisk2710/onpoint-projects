<?php
/** /indexnow.txt — proves site ownership to IndexNow (set INDEXNOW_KEY in .env). */
require __DIR__ . '/src/bootstrap.php';

$key = (string) env('INDEXNOW_KEY', '');
if ($key === '') {
    http_response_code(404);
    exit;
}
header('Content-Type: text/plain; charset=utf-8');
echo $key;
