<?php
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: " . SITE_URL . "/login.php");
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: " . SITE_URL . "/admin/dashboard.php");
        exit();
    }
}

function login($username, $password, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];
        return true;
    }
    return false;
}

function getStatusBadge($status) {
    switch($status) {
        case 'completed': return '<span class="badge bg-success">Completed</span>';
        case 'ongoing': return '<span class="badge bg-warning">Ongoing</span>';
        case 'planned': return '<span class="badge bg-info">Planned</span>';
        case 'on_hold': return '<span class="badge bg-secondary">On Hold</span>';
        default: return '<span class="badge bg-secondary">Unknown</span>';
    }
}
?>