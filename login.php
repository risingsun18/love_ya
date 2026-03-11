<?php
require_once 'config.php';

if (isLoggedIn()) {
    if (isAdmin()) redirect('dashboard.php');
    if (isUser()) redirect('love.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, username, password, name, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_stmt = $conn->prepare("INSERT INTO login_logs (username, ip_address) VALUES (?, ?)");
            $log_stmt->bind_param("ss", $username, $ip);
            $log_stmt->execute();
            
            if ($user['role'] === 'admin') {
                redirect('dashboard.php');
            } else {
                redirect('love.php');
            }
        } else {
            $error = "ACCESS DENIED: Invalid credentials";
        }
    } else {
        $error = "ACCESS DENIED: User not found in database";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECURE SYSTEM ACCESS // LOVE HACKER PROTOCOL</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <canvas id="matrixCanvas"></canvas>
    
    <div class="login-container">
        <div class="terminal-header">
            <span class="terminal-btn close"></span>
            <span class="terminal-btn minimize"></span>
            <span class="terminal-btn maximize"></span>
            <span class="terminal-title">root@love-hacker:~# secure_login</span>
        </div>
        
        <div class="terminal-body">
            <div class="boot-sequence" id="bootSequence"></div>
            
            <form method="POST" action="" class="login-form" id="loginForm" style="display: none;">
                <div class="input-line">
                    <span class="prompt">root@love-hacker:~#</span>
                    <span class="command">login</span>
                </div>
                
                <?php if ($error): ?>
                <div class="error-message">
                    <span class="error-icon">⚠</span> <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <div class="input-group">
                    <label><span class="prompt">></span> Username:</label>
                    <input type="text" name="username" id="username" class="terminal-input" autocomplete="off" required>
                    <span class="cursor">_</span>
                </div>
                
                <div class="input-group">
                    <label><span class="prompt">></span> Password:</label>
                    <input type="password" name="password" id="password" class="terminal-input" required>
                    <span class="cursor">_</span>
                </div>
                
                <div class="input-group">
                    <button type="submit" class="terminal-btn-submit">
                        <span class="prompt">$</span> EXECUTE_LOGIN
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="system-info">
        <div class="info-line">SYSTEM: Love Hacker Protocol v2.0</div>
        <div class="info-line">SECURITY: AES-256 Encryption Active</div>
        <div class="info-line">STATUS: Waiting for authentication...</div>
    </div>

    <script src="script.js"></script>
    <script>
        const bootTexts = [
            "Initializing Love Hacker Protocol...",
            "Loading security modules...",
            "Establishing secure connection...",
            "Checking system integrity...",
            "Access granted to terminal...",
            "Waiting for user credentials..."
        ];
        
        let bootIndex = 0;
        const bootElement = document.getElementById('bootSequence');
        const form = document.getElementById('loginForm');
        
        function typeBootText() {
            if (bootIndex < bootTexts.length) {
                const line = document.createElement('div');
                line.className = 'boot-line';
                line.innerHTML = '<span class="timestamp">[' + new Date().toLocaleTimeString() + ']</span> ' + bootTexts[bootIndex];
                bootElement.appendChild(line);
                bootIndex++;
                setTimeout(typeBootText, 800);
            } else {
                setTimeout(() => {
                    form.style.display = 'block';
                    document.getElementById('username').focus();
                }, 500);
            }
        }
        
        window.addEventListener('load', () => {
            setTimeout(typeBootText, 1000);
            initMatrix('matrixCanvas');
        });
    </script>
</body>
</html>
