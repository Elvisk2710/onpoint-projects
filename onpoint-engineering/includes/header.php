<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-cogs me-2"></i>Admin Panel - <?php echo $company_info['company_name']; ?>
        </a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">
                <i class="fas fa-user me-1"></i>Welcome, <?php echo $_SESSION['admin_username']; ?>
            </span>
            <a class="btn btn-outline-light btn-sm me-2" href="<?php echo SITE_URL; ?>" target="_blank">
                <i class="fas fa-external-link-alt me-1"></i>View Site
            </a>
            <a class="btn btn-outline-light btn-sm" href="logout.php">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>