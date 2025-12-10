<?php
include '../includes/config.php';

// Handle contact form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required.';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required.';
    }
    
    if (empty($errors)) {
        // Save to database
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$name, $email, $phone, $subject, $message])) {
            // Send email notification (you can implement this later)
            // sendEmail($company_info['email'], "New Contact Message: $subject", $message, $email);
            
            $success_message = 'Thank you for your message! We will get back to you soon.';
            
            // Clear form fields
            $_POST = [];
        } else {
            $error_message = 'Sorry, there was an error sending your message. Please try again.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo $company_info['company_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .contact-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../assets/images/contact-hero.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }
        .contact-info-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
        }
        .contact-info-card:hover {
            transform: translateY(-5px);
        }
        .contact-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .map-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/pages/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo SITE_URL; ?>/pages/contact.php">Contact</a>
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
    <section class="contact-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">Get In Touch</h1>
                    <p class="lead mb-4">Ready to start your next project? Contact us today for a free consultation and let's build something amazing together.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card contact-info-card text-center py-4">
                        <div class="card-body">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt text-white fa-2x"></i>
                            </div>
                            <h5>Our Location</h5>
                            <p class="card-text"><?php echo $company_info['address']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card contact-info-card text-center py-4">
                        <div class="card-body">
                            <div class="contact-icon">
                                <i class="fas fa-phone text-white fa-2x"></i>
                            </div>
                            <h5>Phone Number</h5>
                            <p class="card-text"><?php echo $company_info['phone']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card contact-info-card text-center py-4">
                        <div class="card-body">
                            <div class="contact-icon">
                                <i class="fas fa-envelope text-white fa-2x"></i>
                            </div>
                            <h5>Email Address</h5>
                            <p class="card-text"><?php echo $company_info['email']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form & Map -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <h3 class="card-title mb-4">Send us a Message</h3>
                            
                            <!-- Success/Error Messages -->
                            <?php if ($success_message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $success_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error_message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" id="contactForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo $_POST['name'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo $_POST['email'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?php echo $_POST['phone'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="subject" class="form-label">Subject *</label>
                                            <input type="text" class="form-control" id="subject" name="subject" 
                                                   value="<?php echo $_POST['subject'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="6" 
                                              required><?php echo $_POST['message'] ?? ''; ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Office Hours & Map -->
                <div class="col-lg-4">
                    <!-- Office Hours -->
                    <div class="card border-0 shadow mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Office Hours</h5>
                            <div class="mb-3">
                                <strong>Monday - Friday</strong>
                                <p class="mb-1">8:00 AM - 6:00 PM</p>
                            </div>
                            <div class="mb-3">
                                <strong>Saturday</strong>
                                <p class="mb-1">9:00 AM - 2:00 PM</p>
                            </div>
                            <div>
                                <strong>Sunday</strong>
                                <p class="mb-0">Closed</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Contact -->
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Quick Contact</h5>
                            <div class="mb-3">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <strong>Emergency Line:</strong>
                                <p class="mb-0"><?php echo $company_info['phone']; ?></p>
                            </div>
                            <div>
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <strong>Email:</strong>
                                <p class="mb-0"><?php echo $company_info['email']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="text-center mb-5">
                        <h2 class="display-5 fw-bold">Find Us</h2>
                        <p class="lead">Visit our office or get in touch for project discussions</p>
                    </div>
                    
                    <div class="map-container">
                        <!-- Google Maps Embed -->
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.177631294249!2d-74.00594908459418!3d40.71278367933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a2e1e3b3b3d%3A0x8d1b3e3e3e3e3e3e!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Frequently Asked Questions</h2>
                <p class="lead">Get answers to common questions about our services</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    What types of projects do you handle?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We handle a wide range of civil engineering projects including road construction, building structures, 
                                    water resource management, environmental engineering, and infrastructure development projects.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    How long does a typical project take?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Project duration varies based on scope and complexity. Small projects may take 3-6 months, while large 
                                    infrastructure projects can take 1-3 years. We provide detailed timelines during project planning.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    Do you provide free consultations?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we offer free initial consultations to discuss your project requirements, assess feasibility, 
                                    and provide preliminary recommendations.
                                </div>
                            </div>
                        </div>
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
    <script>
        // Form validation
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;
            
            // Basic phone validation (optional field)
            if (phone && !isValidPhone(phone)) {
                e.preventDefault();
                alert('Please enter a valid phone number.');
                return;
            }
            
            // Email validation
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
        });

        function isValidPhone(phone) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            return phoneRegex.test(phone.replace(/[^\d+]/g, ''));
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    </script>
</body>
</html>