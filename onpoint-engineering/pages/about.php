<?php
include '../includes/config.php';

// Get company statistics
$total_projects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$ongoing_projects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'ongoing'")->fetchColumn();
$completed_projects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'completed'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo $company_info['company_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../assets/images/about-hero.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }
        .stat-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .team-member {
            transition: transform 0.3s ease;
        }
        .team-member:hover {
            transform: translateY(-5px);
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #0d6efd;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -38px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #0d6efd;
            border: 3px solid white;
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
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/projects.php">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo SITE_URL; ?>/pages/about.php">About</a>
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
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">About Our Company</h1>
                    <p class="lead mb-4">Pioneering civil engineering solutions with innovation, expertise, and unwavering commitment to excellence for over two decades.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card stat-card text-center py-4">
                        <div class="card-body">
                            <div class="display-4 fw-bold text-primary"><?php echo $total_projects; ?>+</div>
                            <p class="card-text">Projects Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card text-center py-4">
                        <div class="card-body">
                            <div class="display-4 fw-bold text-warning"><?php echo $ongoing_projects; ?>+</div>
                            <p class="card-text">Active Projects</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card text-center py-4">
                        <div class="card-body">
                            <div class="display-4 fw-bold text-success">20+</div>
                            <p class="card-text">Years Experience</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card text-center py-4">
                        <div class="card-body">
                            <div class="display-4 fw-bold text-info">50+</div>
                            <p class="card-text">Expert Engineers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-4">Our Story & Mission</h2>
                    <p class="lead mb-4">
                        Founded in 2003, <?php echo $company_info['company_name']; ?> has been at the forefront of civil engineering innovation, 
                        delivering exceptional infrastructure solutions that stand the test of time.
                    </p>
                    <p class="mb-4">
                        <?php echo $company_info['about_text'] ?? 'Our mission is to engineer sustainable, efficient, and innovative solutions that transform communities and drive progress. We combine cutting-edge technology with proven engineering principles to deliver projects that exceed expectations.'; ?>
                    </p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Quality Assurance</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Timely Delivery</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Cost Efficiency</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Innovative Solutions</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Sustainable Practices</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Client Satisfaction</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="../assets/images/about-company.jpg" alt="Our Company" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Services</h2>
                <p class="lead">Comprehensive civil engineering solutions for all your infrastructure needs</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-road text-white fa-2x"></i>
                            </div>
                            <h5 class="card-title">Road & Highway Engineering</h5>
                            <p class="card-text">Design and construction of modern transportation infrastructure including highways, bridges, and tunnels.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-building text-white fa-2x"></i>
                            </div>
                            <h5 class="card-title">Structural Engineering</h5>
                            <p class="card-text">Innovative structural designs for commercial, residential, and industrial buildings.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-tint text-white fa-2x"></i>
                            </div>
                            <h5 class="card-title">Water Resources</h5>
                            <p class="card-text">Sustainable water management systems including dams, reservoirs, and irrigation networks.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-leaf text-white fa-2x"></i>
                            </div>
                            <h5 class="card-title">Environmental Engineering</h5>
                            <p class="card-text">Eco-friendly solutions for waste management and environmental protection.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-bolt text-white fa-2x"></i>
                            </div>
                            <h5 class="card-title">Infrastructure Development</h5>
                            <p class="card-text">Comprehensive urban and rural infrastructure planning and development.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="service-icon">
                                <i class="fas fa-drafting-compass text-white fa-2x"></i>
                            </div>
                            <h5 class="card-title">Consultation & Planning</h5>
                            <p class="card-text">Expert consultation services for project planning, feasibility studies, and implementation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Journey</h2>
                <p class="lead">Milestones in our growth and development</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="timeline">
                        <div class="timeline-item">
                            <h5>2003 - Company Foundation</h5>
                            <p class="text-muted">Established with a vision to revolutionize civil engineering practices.</p>
                        </div>
                        <div class="timeline-item">
                            <h5>2008 - National Expansion</h5>
                            <p class="text-muted">Expanded operations nationwide, taking on major infrastructure projects.</p>
                        </div>
                        <div class="timeline-item">
                            <h5>2015 - International Recognition</h5>
                            <p class="text-muted">Awarded for excellence in sustainable engineering practices.</p>
                        </div>
                        <div class="timeline-item">
                            <h5>2020 - Digital Transformation</h5>
                            <p class="text-muted">Implemented advanced digital tools and BIM technology.</p>
                        </div>
                        <div class="timeline-item">
                            <h5>2024 - Continuing Excellence</h5>
                            <p class="text-muted">Leading innovation in smart infrastructure and sustainable development.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="mb-3">Ready to Start Your Project?</h3>
                    <p class="mb-0">Let's collaborate to bring your vision to life with our engineering expertise.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="contact.php" class="btn btn-light btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Get Started
                    </a>
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