<?php
include '../includes/config.php';
include '../includes/auth.php';
redirectIfNotLoggedIn();

// Handle project deletion
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Get image path before deletion
    $stmt = $pdo->prepare("SELECT image_path FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    // Delete project
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    if ($stmt->execute([$id])) {
        // Delete associated image
        if ($project['image_path'] && file_exists('../' . $project['image_path'])) {
            unlink('../' . $project['image_path']);
        }
        $_SESSION['success'] = "Project deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting project!";
    }
    header("Location: projects.php");
    exit();
}

// Handle featured toggle
if (isset($_GET['toggle_featured'])) {
    $id = $_GET['toggle_featured'];
    $stmt = $pdo->prepare("UPDATE projects SET featured = NOT featured WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "Project featured status updated!";
    } else {
        $_SESSION['error'] = "Error updating project!";
    }
    header("Location: projects.php");
    exit();
}

// Get all projects
$stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - <?php echo $company_info['company_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .status-badge {
            font-size: 0.75em;
        }

        .action-buttons .btn {
            margin: 2px;
        }
    </style>
</head>

<body>
    <?php include '../includes/admin-header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Projects</h1>
                    <a href="add_project.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Project
                    </a>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Projects Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-project-diagram me-2"></i>All Projects
                            <span class="badge bg-secondary ms-2"><?php echo count($projects); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($projects)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Projects Found</h5>
                                <p class="text-muted">Get started by adding your first project.</p>
                                <a href="add_project.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Your First Project
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Client</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Featured</th>
                                            <th>Budget</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($projects as $project): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($project['image_path']): ?>
                                                        <img src="../<?php echo $project['image_path']; ?>"
                                                            alt="<?php echo htmlspecialchars($project['title']); ?>"
                                                            class="table-img">
                                                    <?php else: ?>
                                                        <div class="table-img bg-secondary d-flex align-items-center justify-content-center text-white">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                                    <?php if ($project['short_description']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($project['short_description'], 0, 50)); ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($project['client']); ?></td>
                                                <td><?php echo htmlspecialchars($project['location']); ?></td>
                                                <td>
                                                    <span class="badge status-badge bg-<?php
                                                                                        switch ($project['status']) {
                                                                                            case 'completed':
                                                                                                echo 'success';
                                                                                                break;
                                                                                            case 'ongoing':
                                                                                                echo 'warning';
                                                                                                break;
                                                                                            case 'planned':
                                                                                                echo 'info';
                                                                                                break;
                                                                                            case 'on_hold':
                                                                                                echo 'secondary';
                                                                                                break;
                                                                                            default:
                                                                                                echo 'secondary';
                                                                                        }
                                                                                        ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="projects.php?toggle_featured=<?php echo $project['id']; ?>"
                                                        class="btn btn-sm <?php echo $project['featured'] ? 'btn-warning' : 'btn-outline-warning'; ?>">
                                                        <i class="fas fa-star"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if ($project['budget']): ?>
                                                        $<?php echo number_format($project['budget'], 2); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php echo date('M d, Y', strtotime($project['created_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="edit_project.php?id=<?php echo $project['id']; ?>"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="Edit Project">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="projects.php?delete_id=<?php echo $project['id']; ?>"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Delete Project"
                                                            onclick="return confirm('Are you sure you want to delete this project? This action cannot be undone.')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                        <a href="../pages/projects.php#project-<?php echo $project['id']; ?>"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-info"
                                                            title="View on Website">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo count(array_filter($projects, fn($p) => $p['status'] === 'completed')); ?></h4>
                                        <span>Completed</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo count(array_filter($projects, fn($p) => $p['status'] === 'ongoing')); ?></h4>
                                        <span>Ongoing</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-tasks fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo count(array_filter($projects, fn($p) => $p['status'] === 'planned')); ?></h4>
                                        <span>Planned</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo count(array_filter($projects, fn($p) => $p['featured'])); ?></h4>
                                        <span>Featured</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-star fa-2x"></i>
                                    </div>
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