<?php
include '../includes/config.php';

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search_query = $_GET['search'] ?? '';

// Build query with filters
$sql = "SELECT * FROM projects WHERE 1=1";
$params = [];

if ($status_filter !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

if (!empty($search_query)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ? OR client LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$sql .= " ORDER BY 
    CASE status 
        WHEN 'ongoing' THEN 1
        WHEN 'planned' THEN 2
        WHEN 'completed' THEN 3
        ELSE 4
    END, created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Get project counts by status for filter badges
$status_counts = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM projects 
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

$total_projects = array_sum($status_counts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Projects - <?php echo $company_info['company_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .project-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
        }
        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .project-image {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .project-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
        }
        .filter-active {
            background-color: #0d6efd !important;
            color: white !important;
        }
        .search-box {
            max-width: 400px;
        }
        .project-meta {
            font-size: 0.9em;
        }
        .featured-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 2;
        }
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="fas fa-building me-2"></i><?php echo $company_info['company_name']; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo SITE_URL; ?>/pages/projects.php">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm ms-2" href="<?php echo SITE_URL; ?>/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-primary text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 fw-bold mb-3">Our Projects</h1>
                    <p class="lead mb-4">Explore our portfolio of successful civil engineering projects and infrastructure developments</p>
                    
                    <!-- Search Box -->
                    <form method="GET" class="search-box mx-auto">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search projects..." 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="py-5">
        <div class="container">
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="mb-3">
                            <h5 class="mb-3">Filter by Status:</h5>
                            <div class="btn-group" role="group">
                                <a href="?status=all<?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" 
                                   class="btn btn-outline-primary <?php echo $status_filter === 'all' ? 'filter-active' : ''; ?>">
                                    All Projects <span class="badge bg-secondary ms-1"><?php echo $total_projects; ?></span>
                                </a>
                                <a href="?status=ongoing<?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" 
                                   class="btn btn-outline-primary <?php echo $status_filter === 'ongoing' ? 'filter-active' : ''; ?>">
                                    Ongoing <span class="badge bg-warning ms-1"><?php echo $status_counts['ongoing'] ?? 0; ?></span>
                                </a>
                                <a href="?status=planned<?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" 
                                   class="btn btn-outline-primary <?php echo $status_filter === 'planned' ? 'filter-active' : ''; ?>">
                                    Planned <span class="badge bg-info ms-1"><?php echo $status_counts['planned'] ?? 0; ?></span>
                                </a>
                                <a href="?status=completed<?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" 
                                   class="btn btn-outline-primary <?php echo $status_filter === 'completed' ? 'filter-active' : ''; ?>">
                                    Completed <span class="badge bg-success ms-1"><?php echo $status_counts['completed'] ?? 0; ?></span>
                                </a>
                            </div>
                        </div>
                        
                        <?php if ($status_filter !== 'all' || !empty($search_query)): ?>
                        <div class="mb-3">
                            <a href="projects.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <?php if (!empty($search_query) || $status_filter !== 'all'): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Showing <?php echo count($projects); ?> project(s)
                        <?php if (!empty($search_query)): ?>
                            matching "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                        <?php endif; ?>
                        <?php if ($status_filter !== 'all'): ?>
                            with status: <strong><?php echo ucfirst($status_filter); ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Projects Grid -->
            <div class="row" id="projects-grid">
                <?php if (empty($projects)): ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h3 class="text-muted">No Projects Found</h3>
                            <p class="text-muted mb-4">
                                <?php if (!empty($search_query) || $status_filter !== 'all'): ?>
                                    No projects match your current filters. Try adjusting your search criteria.
                                <?php else: ?>
                                    We're currently working on updating our project portfolio. Please check back soon.
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($search_query) || $status_filter !== 'all'): ?>
                                <a href="projects.php" class="btn btn-primary">
                                    <i class="fas fa-undo me-1"></i>View All Projects
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card project-card">
                            <div class="position-relative">
                                <?php if ($project['image_path']): ?>
                                    <img src="../<?php echo $project['image_path']; ?>" 
                                         class="project-image" 
                                         alt="<?php echo htmlspecialchars($project['title']); ?>">
                                <?php else: ?>
                                    <div class="project-image bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <i class="fas fa-image fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Status Badge -->
                                <span class="project-badge badge <?php 
                                    switch($project['status']) {
                                        case 'completed': echo 'bg-success'; break;
                                        case 'ongoing': echo 'bg-warning'; break;
                                        case 'planned': echo 'bg-info'; break;
                                        case 'on_hold': echo 'bg-secondary'; break;
                                        default: echo 'bg-secondary';
                                    }
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                </span>
                                
                                <!-- Featured Badge -->
                                <?php if ($project['featured']): ?>
                                    <span class="featured-badge badge bg-warning">
                                        <i class="fas fa-star me-1"></i>Featured
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                <p class="card-text text-muted">
                                    <?php 
                                    if (!empty($project['short_description'])) {
                                        echo htmlspecialchars($project['short_description']);
                                    } else {
                                        echo truncateText($project['description'], 120);
                                    }
                                    ?>
                                </p>
                                
                                <div class="project-meta">
                                    <div class="mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <strong>Location:</strong> <?php echo htmlspecialchars($project['location']); ?>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        <strong>Client:</strong> <?php echo htmlspecialchars($project['client']); ?>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <strong>Start Date:</strong> <?php echo formatDate($project['start_date'], 'M Y'); ?>
                                    </div>
                                    <?php if ($project['end_date']): ?>
                                    <div class="mb-2">
                                        <i class="fas fa-flag-checkered text-primary me-2"></i>
                                        <strong>End Date:</strong> <?php echo formatDate($project['end_date'], 'M Y'); ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($project['budget']): ?>
                                    <div class="mb-3">
                                        <i class="fas fa-dollar-sign text-primary me-2"></i>
                                        <strong>Budget:</strong> <?php echo formatCurrency($project['budget']); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Project Progress</small>
                                        <small class="text-muted">
                                            <?php 
                                            $progress = getProjectProgress($project['status']);
                                            echo $progress . '%';
                                            ?>
                                        </small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar 
                                            <?php 
                                            switch($project['status']) {
                                                case 'completed': echo 'bg-success'; break;
                                                case 'ongoing': echo 'bg-warning'; break;
                                                case 'planned': echo 'bg-info'; break;
                                                case 'on_hold': echo 'bg-secondary'; break;
                                                default: echo 'bg-secondary';
                                            }
                                            ?>" 
                                            style="width: <?php echo $progress; ?>%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Call to Action -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <div class="bg-light py-5 rounded">
                        <h3 class="mb-3">Have a Project in Mind?</h3>
                        <p class="lead mb-4">Let's discuss how we can bring your vision to life with our engineering expertise.</p>
                        <a href="contact.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Get In Touch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><?php echo $company_info['company_name']; ?></h5>
                    <p class="text-light">Leading civil engineering solutions with innovation and excellence since 2003.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contact Info</h5>
                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><?php echo $company_info['address']; ?></p>
                    <p class="mb-1"><i class="fas fa-phone me-2"></i><?php echo $company_info['phone']; ?></p>
                    <p class="mb-0"><i class="fas fa-envelope me-2"></i><?php echo $company_info['email']; ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="projects.php" class="text-light text-decoration-none">Projects</a></li>
                        <li><a href="about.php" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 <?php echo $company_info['company_name']; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>