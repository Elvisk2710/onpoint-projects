<?php
include '../includes/config.php';
include '../includes/auth.php';
redirectIfNotLoggedIn();

// Check if project ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: projects.php");
    exit();
}

$project_id = $_GET['id'];

// Get project data
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    $_SESSION['error'] = "Project not found!";
    header("Location: projects.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $short_description = $_POST['short_description'];
    $location = $_POST['location'];
    $client = $_POST['client'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'] ?: null;
    $status = $_POST['status'];
    $budget = $_POST['budget'] ?: null;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $remove_image = isset($_POST['remove_image']) ? 1 : 0;
    
    $image_path = $project['image_path'];
    
    // Handle image removal
    if ($remove_image && $image_path) {
        if (file_exists('../' . $image_path)) {
            unlink('../' . $image_path);
        }
        $image_path = '';
    }
    
    // Handle new image upload
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] == 0) {
        // Remove old image if exists
        if ($image_path && file_exists('../' . $image_path)) {
            unlink('../' . $image_path);
        }
        
        $upload_dir = '../uploads/projects/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['project_image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $file_extension = pathinfo($_FILES['project_image']['name'], PATHINFO_EXTENSION);
            $filename = 'project_' . time() . '_' . uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['project_image']['tmp_name'], $target_path)) {
                $image_path = 'uploads/projects/' . $filename;
            } else {
                $_SESSION['error'] = "Error uploading image file.";
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Please upload JPEG, PNG, or GIF images only.";
        }
    }
    
    if (!isset($_SESSION['error'])) {
        $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, short_description = ?, location = ?, client = ?, start_date = ?, end_date = ?, status = ?, budget = ?, image_path = ?, featured = ? WHERE id = ?");
        
        if ($stmt->execute([$title, $description, $short_description, $location, $client, $start_date, $end_date, $status, $budget, $image_path, $featured, $project_id])) {
            $_SESSION['success'] = "Project updated successfully!";
            header("Location: projects.php");
            exit();
        } else {
            $error = "Error updating project! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - <?php echo $company_info['company_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            margin-top: 10px;
            border: 2px dashed #dee2e6;
            border-radius: 5px;
        }
        .current-image {
            max-width: 200px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
                    <h1 class="h2">Edit Project</h1>
                    <div>
                        <a href="projects.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Projects
                        </a>
                        <a href="../pages/projects.php#project-<?php echo $project['id']; ?>" target="_blank" class="btn btn-info">
                            <i class="fas fa-eye me-2"></i>View on Website
                        </a>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>Edit Project: <?php echo htmlspecialchars($project['title']); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="projectForm">
                            <div class="form-section">
                                <h6 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Project Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" required 
                                                   value="<?php echo htmlspecialchars($project['title']); ?>"
                                                   placeholder="Enter project title">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="client" class="form-label">Client Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="client" name="client" required 
                                                   value="<?php echo htmlspecialchars($project['client']); ?>"
                                                   placeholder="Enter client name">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="location" name="location" required 
                                                   value="<?php echo htmlspecialchars($project['location']); ?>"
                                                   placeholder="Enter project location">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="budget" class="form-label">Budget ($)</label>
                                            <input type="number" class="form-control" id="budget" name="budget" 
                                                   step="0.01" min="0" 
                                                   value="<?php echo $project['budget']; ?>"
                                                   placeholder="Enter project budget">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h6 class="mb-3 text-primary">
                                    <i class="fas fa-calendar-alt me-2"></i>Timeline
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required
                                                   value="<?php echo $project['start_date']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">End Date (Optional)</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date"
                                                   value="<?php echo $project['end_date']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Project Status <span class="text-danger">*</span></label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="planned" <?php echo $project['status'] == 'planned' ? 'selected' : ''; ?>>Planned</option>
                                                <option value="ongoing" <?php echo $project['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                                <option value="completed" <?php echo $project['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="on_hold" <?php echo $project['status'] == 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Options</label>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                                                    <?php echo $project['featured'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="featured">
                                                    Feature this project on homepage
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h6 class="mb-3 text-primary">
                                    <i class="fas fa-image me-2"></i>Project Image
                                </h6>
                                
                                <?php if ($project['image_path']): ?>
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label>
                                    <div>
                                        <img src="../<?php echo $project['image_path']; ?>" 
                                             alt="Current project image" 
                                             class="current-image mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                            <label class="form-check-label text-danger" for="remove_image">
                                                Remove current image
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="project_image" class="form-label">
                                        <?php echo $project['image_path'] ? 'Upload New Image' : 'Project Image'; ?>
                                    </label>
                                    <input type="file" class="form-control" id="project_image" name="project_image" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Recommended size: 800x600px. Supported formats: JPG, PNG, GIF, WebP</small>
                                    <img id="imagePreview" class="image-preview" alt="Image preview">
                                </div>
                            </div>

                            <div class="form-section">
                                <h6 class="mb-3 text-primary">
                                    <i class="fas fa-file-alt me-2"></i>Project Description
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="short_description" class="form-label">Short Description</label>
                                    <textarea class="form-control" id="short_description" name="short_description" 
                                              rows="3" placeholder="Brief description (appears in project cards)"><?php echo htmlspecialchars($project['short_description']); ?></textarea>
                                    <small class="text-muted">Maximum 500 characters. If empty, first 100 characters of main description will be used.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="6" required placeholder="Detailed project description"><?php echo htmlspecialchars($project['description']); ?></textarea>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="projects.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Project
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }

        // Set minimum date for end date based on start date
        document.getElementById('start_date').addEventListener('change', function() {
            const endDate = document.getElementById('end_date');
            endDate.min = this.value;
        });

        // Form validation
        document.getElementById('projectForm').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            
            if (document.getElementById('end_date').value && endDate < startDate) {
                e.preventDefault();
                alert('End date cannot be before start date.');
            }
        });

        // Show current image in preview if exists
        <?php if ($project['image_path']): ?>
            document.getElementById('imagePreview').src = '../<?php echo $project['image_path']; ?>';
            document.getElementById('imagePreview').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>