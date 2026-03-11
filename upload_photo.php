<?php
require_once 'config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $upload_dir = 'uploads/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['photo'];
    $caption = clean_input($_POST['caption'] ?? '');
    
    $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file['name']);
    $target_path = $upload_dir . $filename;
    
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        header('Location: dashboard.php?error=Invalid image file');
        exit();
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        header('Location: dashboard.php?error=File too large (max 5MB)');
        exit();
    }
    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($file_type, $allowed_types)) {
        header('Location: dashboard.php?error=Only JPG, PNG, GIF, WebP allowed');
        exit();
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $stmt = $conn->prepare("INSERT INTO photos (image_path, caption) VALUES (?, ?)");
        $stmt->bind_param("ss", $target_path, $caption);
        $stmt->execute();
        
        header('Location: dashboard.php?success=Photo uploaded successfully');
    } else {
        header('Location: dashboard.php?error=Upload failed');
    }
} else {
    header('Location: dashboard.php');
}
?>
