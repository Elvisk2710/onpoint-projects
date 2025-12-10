<?php
include '../includes/config.php';
include '../includes/auth.php';
redirectIfNotLoggedIn();

// Get statistics
$totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$ongoingProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'ongoing'")->fetchColumn();
$completedProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'completed'")->fetchColumn();
$newMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo $company_info['company_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/admin-header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-external-link-alt me-1"></i>View Website
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Projects</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalProjects; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Completed Projects</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completedProjects; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Ongoing Projects</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $ongoingProjects; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            New Messages</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $newMessages; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="projects.php" class="btn btn-primary w-100">
                                            <i class="fas fa-project-diagram me-2"></i>Manage Projects
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="add_project.php" class="btn btn-success w-100">
                                            <i class="fas fa-plus me-2"></i>Add New Project
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="messages.php" class="btn btn-info w-100">
                                            <i class="fas fa-envelope me-2"></i>View Messages
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-secondary w-100">
                                            <i class="fas fa-eye me-2"></i>View Website
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Projects -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Projects</h6>
                                <a href="projects.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
                                $recentProjects = $stmt->fetchAll();
                                ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Client</th>
                                                <th>Status</th>
                                                <th>Start Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentProjects as $project): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($project['title']); ?></td>
                                                <td><?php echo htmlspecialchars($project['client']); ?></td>
                                                <td><?php echo getStatusBadge($project['status']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($project['start_date'])); ?></td>
                                                <td>
                                                    <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>