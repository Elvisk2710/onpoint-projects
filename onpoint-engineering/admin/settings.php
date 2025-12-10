<?php
include '../includes/config.php';
include '../includes/auth.php';
redirectIfNotLoggedIn();

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = $_POST['company_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $about_text = $_POST['about_text'];
    
    $logo_path = $company_info['logo_path'];
    
    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $uploadResult = uploadImage($_FILES['logo'], '../uploads/');
        if ($uploadResult[0]) {
            // Delete old logo if exists
            if ($logo_path && file_exists('../' . $logo_path)) {
                unlink('../' . $logo_path);
            }
            $logo_path = $uploadResult[2];
        }
    }
    
    $stmt = $pdo->prepare("UPDATE company_info SET company_name = ?, address = ?, phone = ?, email = ?, about_text = ?, logo_path = ? WHERE id = 1");
    
    if ($stmt->execute([$company_name, $address, $phone, $email, $about_text, $logo_path])) {
        $_SESSION['success'] = "Settings updated successfully!";
        // Refresh company info
        $stmt = $pdo->query("SELECT * FROM company_info WHERE id = 1");
        $company_info = $stmt->fetch();
    } else {
        $_SESSION['error'] = "Error updating settings!";
    }
    
    header("Location: settings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo $company_info['company_name']; ?></title>
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
                    <h1 class="h2">Company Settings</h1>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" 
                                               value="<?php echo htmlspecialchars($company_info['company_name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo $company_info['email']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="<?php echo $company_info['phone']; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Company Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <?php if ($company_info['logo_path']): ?>
                                            <div class="mt-2">
                                                <img src="../<?php echo $company_info['logo_path']; ?>" 
                                                     alt="Current Logo" 
                                                     style="max-width: 200px; max-height: 100px;" 
                                                     class="border p-2">
                                                <p class="text-muted small mt-1">Current logo</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Company Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($company_info['address']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="about_text" class="form-label">About Text</label>
                                <textarea class="form-control" id="about_text" name="about_text" rows="6" required><?php echo htmlspecialchars($company_info['about_text']); ?></textarea>
                                <div class="form-text">This text appears on the about page.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>