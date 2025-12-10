<?php

/**
 * Civil Engineering Project - Helper Functions
 * Contains reusable functions for both admin and client sides
 */

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate SEO-friendly slug from string
 * @param string $string
 * @return string
 */
function generateSlug($string)
{
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Format date for display
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'F j, Y')
{
    if (empty($date) || $date == '0000-00-00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Truncate text to specified length
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncateText($text, $length = 100, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }

    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    return $text . $suffix;
}

/**
 * Get project status badge HTML
 * @param string $status
 * @return string
 */
function getStatusBadge($status)
{
    $statuses = [
        'planned' => ['label' => 'Planned', 'class' => 'bg-info'],
        'ongoing' => ['label' => 'Ongoing', 'class' => 'bg-warning'],
        'completed' => ['label' => 'Completed', 'class' => 'bg-success'],
        'on_hold' => ['label' => 'On Hold', 'class' => 'bg-secondary']
    ];

    if (array_key_exists($status, $statuses)) {
        $badge = $statuses[$status];
        return '<span class="badge ' . $badge['class'] . '">' . $badge['label'] . '</span>';
    }

    return '<span class="badge bg-secondary">Unknown</span>';
}

/**
 * Upload image with validation
 * @param array $file
 * @param string $uploadDir
 * @param array $allowedTypes
 * @param int $maxSize
 * @return array [success, message, file_path]
 */
function uploadImage($file, $uploadDir = '../uploads/', $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'], $maxSize = 5242880)
{

    // Check if upload directory exists, create if not
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return [false, 'Failed to create upload directory.', ''];
        }
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
        ];

        $message = $errorMessages[$file['error']] ?? 'Unknown upload error.';
        return [false, $message, ''];
    }

    // Check file type
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        return [false, 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes), ''];
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        return [false, 'File size too large. Maximum size: ' . ($maxSize / 1024 / 1024) . 'MB', ''];
    }

    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Remove '../' from the path for database storage if needed
        $dbPath = str_replace('../', '', $filePath);
        return [true, 'File uploaded successfully.', $dbPath];
    }

    return [false, 'Failed to move uploaded file.', ''];
}

/**
 * Delete file from server
 * @param string $filePath
 * @return bool
 */
function deleteFile($filePath)
{
    if (file_exists($filePath) && is_file($filePath)) {
        return unlink($filePath);
    }
    return false;
}

/**
 * Get project image URL with fallback
 * @param string $imagePath
 * @param string $fallback
 * @return string
 */
function getProjectImage($imagePath, $fallback = 'assets/images/default-project.jpg')
{
    if (!empty($imagePath) && file_exists($imagePath)) {
        return $imagePath;
    }
    return $fallback;
}

/**
 * Format currency amount
 * @param float $amount
 * @param string $currency
 * @return string
 */
function formatCurrency($amount, $currency = '$')
{
    if (empty($amount)) {
        return 'N/A';
    }
    return $currency . number_format($amount, 2);
}

/**
 * Calculate project duration in days
 * @param string $startDate
 * @param string $endDate
 * @return int
 */
function calculateProjectDuration($startDate, $endDate = null)
{
    if (empty($endDate) || $endDate == '0000-00-00') {
        $endDate = date('Y-m-d');
    }

    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $interval = $start->diff($end);

    return $interval->days;
}

/**
 * Get project progress percentage based on status
 * @param string $status
 * @return int
 */
function getProjectProgress($status)
{
    $progress = [
        'planned' => 10,
        'ongoing' => 50,
        'completed' => 100,
        'on_hold' => 25
    ];

    return $progress[$status] ?? 0;
}

/**
 * Send email notification
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param string $from
 * @return bool
 */
function sendEmail($to, $subject, $message, $from = null)
{
    if ($from === null) {
        global $company_info;
        $from = $company_info['email'] ?? 'noreply@' . $_SERVER['HTTP_HOST'];
    }

    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    return mail($to, $subject, $message, $headers);
}

/**
 * Log admin activity
 * @param PDO $pdo
 * @param string $activity
 * @param int $adminId
 * @return bool
 */
function logActivity($pdo, $activity, $adminId = null)
{
    if ($adminId === null) {
        $adminId = $_SESSION['admin_id'] ?? 0;
    }

    $stmt = $pdo->prepare("INSERT INTO admin_activities (admin_id, activity, ip_address, user_agent) VALUES (?, ?, ?, ?)");

    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    return $stmt->execute([$adminId, $activity, $ipAddress, $userAgent]);
}

/**
 * Get pagination parameters
 * @param int $totalItems
 * @param int $currentPage
 * @param int $itemsPerPage
 * @return array
 */
function getPagination($totalItems, $currentPage = 1, $itemsPerPage = 10)
{
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;

    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'items_per_page' => $itemsPerPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Generate pagination HTML
 * @param array $pagination
 * @param string $baseUrl
 * @return string
 */
function generatePagination($pagination, $baseUrl = '?page=')
{
    if ($pagination['total_pages'] <= 1) {
        return '';
    }

    $html = '<nav aria-label="Page navigation"><ul class="pagination">';

    // Previous button
    if ($pagination['has_previous']) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $baseUrl . ($pagination['current_page'] - 1) . '">Previous</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }

    // Page numbers
    $startPage = max(1, $pagination['current_page'] - 2);
    $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);

    for ($i = $startPage; $i <= $endPage; $i++) {
        $active = $i == $pagination['current_page'] ? ' active' : '';
        $html .= '<li class="page-item' . $active . '">';
        $html .= '<a class="page-link" href="' . $baseUrl . $i . '">' . $i . '</a>';
        $html .= '</li>';
    }

    // Next button
    if ($pagination['has_next']) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $baseUrl . ($pagination['current_page'] + 1) . '">Next</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }

    $html .= '</ul></nav>';

    return $html;
}

/**
 * Validate email address
 * @param string $email
 * @return bool
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (basic validation)
 * @param string $phone
 * @return bool
 */
function isValidPhone($phone)
{
    $cleaned = preg_replace('/[^0-9]/', '', $phone);
    return strlen($cleaned) >= 10;
}

/**
 * Get client IP address
 * @return string
 */
function getClientIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
}

/**
 * Generate random password
 * @param int $length
 * @return string
 */
function generateRandomPassword($length = 12)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $password;
}

/**
 * Check if string is JSON
 * @param string $string
 * @return bool
 */
function isJson($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Get file extension from filename
 * @param string $filename
 * @return string
 */
function getFileExtension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Convert bytes to human readable format
 * @param int $bytes
 * @param int $precision
 * @return string
 */
function formatBytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Get current URL
 * @return string
 */
function getCurrentUrl()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Redirect with message
 * @param string $url
 * @param string $type
 * @param string $message
 */
function redirectWithMessage($url, $type = 'success', $message = '')
{
    if (!empty($message)) {
        $_SESSION[$type . '_message'] = $message;
    }
    header('Location: ' . $url);
    exit();
}

/**
 * Get flash message and clear it
 * @param string $type
 * @return string
 */
function getFlashMessage($type = 'success')
{
    $key = $type . '_message';
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return '';
}

/**
 * Create database backup (basic implementation)
 * @param PDO $pdo
 * @param string $backupPath
 * @return bool
 */
function createDatabaseBackup($pdo, $backupPath = '../backups/')
{
    if (!is_dir($backupPath)) {
        mkdir($backupPath, 0755, true);
    }

    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $filepath = $backupPath . $filename;

    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    $backupContent = '';

    foreach ($tables as $table) {
        // Drop table if exists
        $backupContent .= "DROP TABLE IF EXISTS `$table`;\n";

        // Create table structure
        $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $backupContent .= $createTable['Create Table'] . ";\n\n";

        // Insert data
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            $backupContent .= "INSERT INTO `$table` VALUES ";

            $values = [];
            foreach ($rows as $row) {
                $rowValues = array_map(function ($value) use ($pdo) {
                    if ($value === null) return 'NULL';
                    return $pdo->quote($value);
                }, $row);

                $values[] = '(' . implode(', ', $rowValues) . ')';
            }

            $backupContent .= implode(",\n", $values) . ";\n\n";
        }
    }

    return file_put_contents($filepath, $backupContent) !== false;
}

// Include this file in config.php to make functions available globally
