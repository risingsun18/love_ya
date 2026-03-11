<?php
require_once 'config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['music'])) {
    $upload_dir = 'uploads/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['music'];
    
    $filename = 'romantic_' . time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file['name']);
    $target_path = $upload_dir . $filename;
    
    if ($file['size'] > 10 * 1024 * 1024) {
        header('Location: dashboard.php?error=File too large (max 10MB)');
        exit();
    }
    
    $allowed_types = ['mp3', 'wav', 'ogg', 'm4a'];
    $file_type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($file_type, $allowed_types)) {
        header('Location: dashboard.php?error=Only MP3, WAV, OGG, M4A allowed');
        exit();
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $stmt = $conn->prepare("UPDATE settings SET music_path = ? WHERE id = 1");
        $stmt->bind_param("s", $target_path);
        $stmt->execute();
        
        header('Location: dashboard.php?success=Music uploaded successfully');
    } else {
        header('Location: dashboard.php?error=Upload failed');
    }
} else {
    header('Location: dashboard.php');
}
?>
