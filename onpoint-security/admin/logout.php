<?php
require __DIR__ . '/../src/bootstrap.php';

start_session();
if (is_post()) {
    csrf_verify();
    logout();
}
redirect('login.php');
