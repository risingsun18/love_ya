<?php
require_once 'config.php';
requireAdmin();

$success = '';
$error = '';

// Get current settings
$settings_result = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $settings_result->fetch_assoc();

// Get current message
$msg_result = $conn->query("SELECT * FROM messages ORDER BY id DESC LIMIT 1");
$current_message = $msg_result->fetch_assoc();

// Get all photos
$photos_result = $conn->query("SELECT * FROM photos ORDER BY uploaded_at DESC");

// Get login logs
$logs_result = $conn->query("SELECT * FROM login_logs ORDER BY login_time DESC LIMIT 20");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_message'])) {
        $message = clean_input($_POST['message']);
        $conn->query("DELETE FROM messages");
        $stmt = $conn->prepare("INSERT INTO messages (message) VALUES (?)");
        $stmt->bind_param("s", $message);
        if ($stmt->execute()) {
            $success = "Love message updated successfully";
        } else {
            $error = "Error updating message";
        }
    }
    
    if (isset($_POST['update_settings'])) {
        $site_title = clean_input($_POST['site_title']);
        $secret_notes = clean_input($_POST['secret_notes']);
        
        $stmt = $conn->prepare("UPDATE settings SET site_title = ?, secret_notes = ? WHERE id = 1");
        $stmt->bind_param("ss", $site_title, $secret_notes);
        if ($stmt->execute()) {
            $success = "Settings updated successfully";
        } else {
            $error = "Error updating settings";
        }
    }
    
    if (isset($_POST['delete_photo'])) {
        $photo_id = intval($_POST['photo_id']);
        $photo_result = $conn->query("SELECT image_path FROM photos WHERE id = $photo_id");
        if ($photo = $photo_result->fetch_assoc()) {
            if (file_exists($photo['image_path'])) {
                unlink($photo['image_path']);
            }
            $conn->query("DELETE FROM photos WHERE id = $photo_id");
            $success = "Photo deleted successfully";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN DASHBOARD // ROOT ACCESS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <canvas id="matrixCanvas"></canvas>
    
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="glitch" data-text="ROOT ACCESS GRANTED">ROOT ACCESS GRANTED</h1>
            <div class="admin-info">
                <span class="prompt">root@love-hacker:~#</span> whoami<br>
                <span class="output">admin (Mond)</span><br>
                <span class="prompt">root@love-hacker:~#</span> status<br>
                <span class="output">System Operational | Love Level: MAXIMUM</span>
            </div>
            <a href="logout.php" class="logout-btn-small">[DISCONNECT]</a>
        </div>
        
        <?php if ($success): ?>
        <div class="alert success">
            <span class="prompt">$</span> echo "<?php echo $success; ?>"
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert error">
            <span class="prompt">$</span> echo "ERROR: <?php echo $error; ?>"
        </div>
        <?php endif; ?>
        
        <div class="dashboard-grid">
            <!-- Edit Love Message -->
            <div class="panel">
                <div class="panel-header">
                    <span class="panel-icon">✎</span>
                    <span class="panel-title">EDIT_LOVE_MESSAGE.exe</span>
                </div>
                <div class="panel-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label><span class="prompt">></span> Message Content:</label>
                            <textarea name="message" class="terminal-textarea" rows="6"><?php echo htmlspecialchars($current_message['message'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" name="update_message" class="btn-primary">
                            <span class="prompt">$</span> COMMIT_CHANGES
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Site Settings -->
            <div class="panel">
                <div class="panel-header">
                    <span class="panel-icon">⚙</span>
                    <span class="panel-title">SYSTEM_SETTINGS.cfg</span>
                </div>
                <div class="panel-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label><span class="prompt">></span> Site Title:</label>
                            <input type="text" name="site_title" class="terminal-input" 
                                   value="<?php echo htmlspecialchars($settings['site_title']); ?>">
                        </div>
                        <div class="form-group">
                            <label><span class="prompt">></span> Secret Notes:</label>
                            <textarea name="secret_notes" class="terminal-textarea" rows="3"><?php echo htmlspecialchars($settings['secret_notes']); ?></textarea>
                        </div>
                        <button type="submit" name="update_settings" class="btn-primary">
                            <span class="prompt">$</span> SAVE_CONFIG
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Upload Photos -->
            <div class="panel">
                <div class="panel-header">
                    <span class="panel-icon">▲</span>
                    <span class="panel-title">UPLOAD_MEMORY.img</span>
                </div>
                <div class="panel-body">
                    <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label><span class="prompt">></span> Select Image:</label>
                            <input type="file" name="photo" accept="image/*" class="terminal-file-input" required>
                        </div>
                        <div class="form-group">
                            <label><span class="prompt">></span> Caption (optional):</label>
                            <input type="text" name="caption" class="terminal-input" placeholder="Enter memory description...">
                        </div>
                        <button type="submit" class="btn-primary">
                            <span class="prompt">$</span> UPLOAD_FILE
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Upload Music -->
            <div class="panel">
                <div class="panel-header">
                    <span class="panel-icon">♫</span>
                    <span class="panel-title">UPLOAD_AUDIO.mp3</span>
                </div>
                <div class="panel-body">
                    <form action="upload_music.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label><span class="prompt">></span> Select Audio:</label>
                            <input type="file" name="music" accept="audio/*" class="terminal-file-input" required>
                        </div>
                        <button type="submit" class="btn-primary">
                            <span class="prompt">$</span> UPLOAD_AUDIO
                        </button>
                    </form>
                    <?php if (!empty($settings['music_path'])): ?>
                    <div class="current-file">
                        <span class="prompt">$</span> Current: <?php echo basename($settings['music_path']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Photo Management -->
        <div class="panel full-width">
            <div class="panel-header">
                <span class="panel-icon">◈</span>
                <span class="panel-title">MEMORY_DATABASE [<?php echo $photos_result->num_rows; ?> FILES]</span>
            </div>
            <div class="panel-body">
                <div class="photo-management-grid">
                    <?php while ($photo = $photos_result->fetch_assoc()): ?>
                    <div class="photo-card">
                        <img src="<?php echo htmlspecialchars($photo['image_path']); ?>" alt="Memory">
                        <div class="photo-info">
                            <span class="photo-id">ID: <?php echo $photo['id']; ?></span>
                            <span class="photo-date"><?php echo $photo['uploaded_at']; ?></span>
                            <?php if ($photo['caption']): ?>
                            <span class="photo-cap"><?php echo htmlspecialchars($photo['caption']); ?></span>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="" class="delete-form">
                            <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
                            <button type="submit" name="delete_photo" class="btn-danger" onclick="return confirm('Delete this memory?')">
                                [DELETE]
                            </button>
                        </form>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <!-- Login Logs -->
        <div class="panel full-width">
            <div class="panel-header">
                <span class="panel-icon">◉</span>
                <span class="panel-title">ACCESS_LOGS.log</span>
            </div>
            <div class="panel-body">
                <div class="logs-container">
                    <table class="terminal-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>USERNAME</th>
                                <th>LOGIN_TIME</th>
                                <th>IP_ADDRESS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($log = $logs_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $log['id']; ?></td>
                                <td><?php echo htmlspecialchars($log['username']); ?></td>
                                <td><?php echo $log['login_time']; ?></td>
                                <td><?php echo $log['ip_address']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>