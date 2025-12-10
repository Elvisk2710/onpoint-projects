<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-5">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['projects.php', 'add_project.php', 'edit_project.php']) ? 'active' : ''; ?>" href="projects.php">
                    <i class="fas fa-project-diagram me-2"></i>
                    Manage Projects
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_project.php' ? 'active' : ''; ?>" href="add_project.php">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add Project
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                    <i class="fas fa-envelope me-2"></i>
                    Contact Messages
                    <span class="badge bg-danger float-end">
                        <?php
                        $new_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
                        echo $new_messages > 0 ? $new_messages : '';
                        ?>
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog me-2"></i>
                    Settings
                </a>
            </li>
        </ul>

        <hr>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-primary" href="<?php echo SITE_URL; ?>" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>
                    Visit Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>