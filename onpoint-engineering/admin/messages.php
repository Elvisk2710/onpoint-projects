<?php
include '../includes/config.php';
include '../includes/auth.php';
redirectIfNotLoggedIn();

// Handle message status update
if (isset($_GET['mark_read'])) {
    $id = $_GET['mark_read'];
    $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Message marked as read!";
    header("Location: messages.php");
    exit();
}

if (isset($_GET['delete_message'])) {
    $id = $_GET['delete_message'];
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success'] = "Message deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting message!";
    }
    header("Location: messages.php");
    exit();
}

// Get all messages
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

$new_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - <?php echo $company_info['company_name']; ?></title>
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
                    <h1 class="h2">Contact Messages</h1>
                    <span class="badge bg-danger">
                        <?php echo $new_messages; ?> New Messages
                    </span>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Messages Yet</h5>
                                <p class="text-muted">Contact form messages will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($messages as $message): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-<?php echo $message['status'] == 'new' ? 'danger' : 'success'; ?>">
                                                    <?php echo ucfirst($message['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                                            <td>
                                                <a href="mailto:<?php echo $message['email']; ?>">
                                                    <?php echo $message['email']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                            <td>
                                                <span title="<?php echo htmlspecialchars($message['message']); ?>">
                                                    <?php echo truncateText($message['message'], 50); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($message['created_at']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <?php if ($message['status'] == 'new'): ?>
                                                        <a href="messages.php?mark_read=<?php echo $message['id']; ?>" 
                                                           class="btn btn-outline-success" title="Mark as Read">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-outline-info view-message" 
                                                            data-bs-toggle="modal" data-bs-target="#messageModal"
                                                            data-message="<?php echo htmlspecialchars($message['message']); ?>"
                                                            data-name="<?php echo htmlspecialchars($message['name']); ?>"
                                                            data-email="<?php echo $message['email']; ?>"
                                                            data-phone="<?php echo $message['phone']; ?>"
                                                            data-subject="<?php echo htmlspecialchars($message['subject']); ?>"
                                                            data-date="<?php echo formatDate($message['created_at']); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="messages.php?delete_message=<?php echo $message['id']; ?>" 
                                                       class="btn btn-outline-danger" 
                                                       onclick="return confirm('Delete this message?')"
                                                       title="Delete Message">
                                                        <i class="fas fa-trash"></i>
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
            </main>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalTitle">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Name:</strong> <span id="modalName"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong> <span id="modalEmail"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Phone:</strong> <span id="modalPhone"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Date:</strong> <span id="modalDate"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Subject:</strong> <span id="modalSubject"></span>
                    </div>
                    <div class="mb-3">
                        <strong>Message:</strong>
                        <div class="border p-3 mt-2 bg-light" id="modalMessage"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="modalReplyLink" class="btn btn-primary">
                        <i class="fas fa-reply me-1"></i>Reply via Email
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewButtons = document.querySelectorAll('.view-message');
            
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('modalName').textContent = this.dataset.name;
                    document.getElementById('modalEmail').textContent = this.dataset.email;
                    document.getElementById('modalPhone').textContent = this.dataset.phone || 'N/A';
                    document.getElementById('modalSubject').textContent = this.dataset.subject;
                    document.getElementById('modalDate').textContent = this.dataset.date;
                    document.getElementById('modalMessage').textContent = this.dataset.message;
                    
                    // Set reply link
                    const replyLink = document.getElementById('modalReplyLink');
                    replyLink.href = 'mailto:' + this.dataset.email + '?subject=Re: ' + encodeURIComponent(this.dataset.subject);
                });
            });
        });
    </script>
</body>
</html>