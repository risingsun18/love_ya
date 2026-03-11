<?php
require_once 'config.php';
requireUser();

$msg_result = $conn->query("SELECT message FROM messages ORDER BY id DESC LIMIT 1");
$love_message = $msg_result->fetch_assoc()['message'] ?? 'No message found';

$settings_result = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $settings_result->fetch_assoc();

$photos_result = $conn->query("SELECT * FROM photos ORDER BY uploaded_at DESC");
$photos = [];
while ($row = $photos_result->fetch_assoc()) {
    $photos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_title']); ?> // ACCESS GRANTED</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="love-page">
    <div id="loadingScreen" class="loading-screen">
        <canvas id="matrixLoading"></canvas>
        <div class="loading-content">
            <div class="hacking-text" id="hackingText">Initializing Love Protocol...</div>
            <div class="progress-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>
            <div class="progress-text" id="progressText">0%</div>
            <div class="terminal-logs" id="terminalLogs"></div>
        </div>
    </div>

    <div id="mainContent" class="main-content" style="display: none;">
        <canvas id="matrixBg"></canvas>
        
        <div class="love-container">
            <div class="glitch-wrapper">
                <h1 class="glitch" data-text="ACCESS GRANTED">ACCESS GRANTED</h1>
            </div>
            
            <div class="heart-container">
                <div class="heart">
                    <span class="heart-code">&lt;3</span>
                </div>
                <div class="heart-particles"></div>
            </div>
            
            <div class="terminal-love-message">
                <div class="terminal-header-small">
                    <span class="terminal-title">root@mond-heart:~# cat love_message.txt</span>
                </div>
                <div class="message-content" id="loveMessage"></div>
                <span class="typing-cursor">_</span>
            </div>
            
            <?php if (!empty($photos)): ?>
            <div class="photo-gallery-section">
                <h2 class="section-title glitch" data-text="MEMORY_FILES">MEMORY_FILES</h2>
                <div class="photo-grid">
                    <?php foreach ($photos as $photo): ?>
                    <div class="photo-item">
                        <img src="<?php echo htmlspecialchars($photo['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($photo['caption'] ?? 'Memory'); ?>"
                             class="hacker-photo">
                        <div class="photo-overlay">
                            <span class="photo-code">[IMG_<?php echo str_pad($photo['id'], 3, '0', STR_PAD_LEFT); ?>]</span>
                            <?php if ($photo['caption']): ?>
                            <span class="photo-caption"><?php echo htmlspecialchars($photo['caption']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($settings['music_path'])): ?>
            <div class="music-player">
                <div class="player-header">
                    <span class="player-icon">♫</span>
                    <span class="player-title">ROMANTIC_AUDIO_STREAM</span>
                </div>
                <audio controls loop class="hacker-audio">
                    <source src="<?php echo htmlspecialchars($settings['music_path']); ?>" type="audio/mpeg">
                    System error: Audio codec not supported
                </audio>
                <div class="audio-visualizer">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="countdown-section">
                <h2 class="section-title glitch" data-text="TIME_SINCE_LOVE">TIME_SINCE_FIRST_CONNECTION</h2>
                <div class="countdown" id="countdown">
                    <div class="time-unit">
                        <span class="time-value" id="days">000</span>
                        <span class="time-label">DAYS</span>
                    </div>
                    <div class="time-unit">
                        <span class="time-value" id="hours">00</span>
                        <span class="time-label">HOURS</span>
                    </div>
                    <div class="time-unit">
                        <span class="time-value" id="minutes">00</span>
                        <span class="time-label">MINUTES</span>
                    </div>
                    <div class="time-unit">
                        <span class="time-value" id="seconds">00</span>
                        <span class="time-label">SECONDS</span>
                    </div>
                </div>
            </div>
            
            <div class="love-quotes">
                <div class="quote glitch" data-text="You are my favorite function">You are my favorite function</div>
                <div class="quote">while(true) { love(Cassie); }</div>
                <div class="quote">if(you == Cassie) { return infiniteLove; }</div>
            </div>
            
            <div class="logout-section">
                <a href="logout.php" class="logout-btn">
                    <span class="prompt">$</span> terminate_session
                </a>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        const loveMessage = <?php echo json_encode($love_message); ?>;
        
        const loadingTexts = [
            "Establishing secure connection to Cassie's heart...",
            "Decrypting love encryption...",
            "Bypassing firewall of loneliness...",
            "Injecting romantic code...",
            "Compiling feelings...",
            "Uploading love packets...",
            "Connection to Love Server Established ❤️",
            "Accessing Cassie's Heart...",
            "Download complete. Welcome, beautiful."
        ];
        
        let progress = 0;
        let textIndex = 0;
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const hackingText = document.getElementById('hackingText');
        const terminalLogs = document.getElementById('terminalLogs');
        const loadingScreen = document.getElementById('loadingScreen');
        const mainContent = document.getElementById('mainContent');
        
        function addLog(text) {
            const log = document.createElement('div');
            log.className = 'log-line';
            log.innerHTML = '<span class="log-time">[' + new Date().toLocaleTimeString() + ']</span> ' + text;
            terminalLogs.appendChild(log);
            terminalLogs.scrollTop = terminalLogs.scrollHeight;
        }
        
        function updateLoading() {
            if (progress < 100) {
                progress += Math.random() * 3;
                if (progress > 100) progress = 100;
                
                progressBar.style.width = progress + '%';
                progressText.textContent = Math.floor(progress) + '%';
                
                if (textIndex < loadingTexts.length && progress > (textIndex + 1) * (100 / loadingTexts.length)) {
                    hackingText.textContent = loadingTexts[textIndex];
                    addLog(loadingTexts[textIndex]);
                    textIndex++;
                }
                
                setTimeout(updateLoading, 100);
            } else {
                setTimeout(() => {
                    loadingScreen.style.opacity = '0';
                    setTimeout(() => {
                        loadingScreen.style.display = 'none';
                        mainContent.style.display = 'block';
                        initMatrix('matrixBg');
                        typeWriter();
                        startCountdown();
                        initAudioVisualizer();
                    }, 500);
                }, 800);
            }
        }
        
        let charIndex = 0;
        function typeWriter() {
            const element = document.getElementById('loveMessage');
            if (charIndex < loveMessage.length) {
                element.innerHTML += loveMessage.charAt(charIndex);
                charIndex++;
                setTimeout(typeWriter, 50);
            }
        }
        
        function startCountdown() {
            const specialDate = new Date(2024, 0, 1);
            
            function update() {
                const now = new Date();
                const diff = now - specialDate;
                
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                document.getElementById('days').textContent = String(days).padStart(3, '0');
                document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
            }
            
            update();
            setInterval(update, 1000);
        }
        
        function initAudioVisualizer() {
            const bars = document.querySelectorAll('.bar');
            setInterval(() => {
                bars.forEach(bar => {
                    const height = Math.random() * 30 + 10;
                    bar.style.height = height + 'px';
                });
            }, 100);
        }
        
        window.addEventListener('load', () => {
            initMatrix('matrixLoading');
            updateLoading();
        });
    </script>
</body>
</html>
