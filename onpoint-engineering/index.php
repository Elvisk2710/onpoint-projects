<?php
include 'includes/config.php';

// Get featured projects
$stmt = $pdo->query("SELECT * FROM projects WHERE featured = TRUE AND status = 'completed' ORDER BY created_at DESC LIMIT 6");
$featuredProjects = $stmt->fetchAll();

// Get ongoing projects count
$ongoingProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'ongoing'")->fetchColumn();
$completedProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'completed'")->fetchColumn();
$totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $company_info['company_name']; ?> - Civil Engineering Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 150px 0;
            text-align: center;
        }
        .project-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .project-card:hover {
            transform: translateY(-10px);
        }
        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .navbar-brand {
            font-weight: bold;
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
                        <a class="nav-link active" href="<?php echo SITE_URL; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/projects.php">Projects</a>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Building the Future Together</h1>
            <p class="lead mb-4">Leading civil engineering solutions with innovation, excellence, and over 20 years of experience</p>
            <a href="<?php echo SITE_URL; ?>/pages/projects.php" class="btn btn-primary btn-lg me-2">
                <i class="fas fa-project-diagram me-2"></i>View Our Projects
            </a>
            <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-envelope me-2"></i>Get In Touch
            </a>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="display-4 fw-bold"><?php echo $totalProjects; ?>+</div>
                    <p class="mb-0">Projects Completed</p>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 fw-bold"><?php echo $ongoingProjects; ?>+</div>
                    <p class="mb-0">Ongoing Projects</p>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 fw-bold">20+</div>
                    <p class="mb-0">Years Experience</p>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 fw-bold">50+</div>
                    <p class="mb-0">Expert Engineers</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Projects Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Featured Projects</h2>
                <p class="lead">Showcasing our most remarkable engineering achievements</p>
            </div>
            
            <div class="row">
                <?php if (empty($featuredProjects)): ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No featured projects available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($featuredProjects as $project): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card project-card h-100">
                            <?php if ($project['image_path']): ?>
                                <img src="<?php echo $project['image_path']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>" style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($project['short_description'] ?: substr($project['description'], 0, 100) . '...'); ?></p>
                                <div class="mb-2">
                                    <?php echo getStatusBadge($project['status']); ?>
                                </div>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($project['location']); ?>
                                </p>
                                <p class="text-muted small">
                                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($project['client']); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="<?php echo SITE_URL; ?>/pages/projects.php#project-<?php echo $project['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?php echo SITE_URL; ?>/pages/projects.php" class="btn btn-primary btn-lg">
                    View All Projects <i class="fas fa-arrow-right ms-2"></i>
                </a>
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
                        <li><a href="<?php echo SITE_URL; ?>/pages/projects.php" class="text-light text-decoration-none">Projects</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/about.php" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/contact.php" class="text-light text-decoration-none">Contact</a></li>
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