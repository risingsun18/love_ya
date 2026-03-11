// Matrix Rain Effect
class MatrixRain {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;
        
        this.ctx = this.canvas.getContext('2d');
        this.characters = options.characters || 'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        this.fontSize = options.fontSize || 14;
        this.columns = 0;
        this.drops = [];
        this.speed = options.speed || 50;
        
        this.init();
        this.start();
        
        window.addEventListener('resize', () => this.init());
    }
    
    init() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
        
        this.columns = Math.floor(this.canvas.width / this.fontSize);
        this.drops = [];
        
        for (let i = 0; i < this.columns; i++) {
            this.drops[i] = Math.random() * -100;
        }
    }
    
    draw() {
        this.ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.ctx.fillStyle = '#0F0';
        this.ctx.font = this.fontSize + 'px monospace';
        
        for (let i = 0; i < this.drops.length; i++) {
            const text = this.characters.charAt(Math.floor(Math.random() * this.characters.length));
            this.ctx.fillText(text, i * this.fontSize, this.drops[i] * this.fontSize);
            
            if (this.drops[i] * this.fontSize > this.canvas.height && Math.random() > 0.975) {
                this.drops[i] = 0;
            }
            this.drops[i]++;
        }
    }
    
    start() {
        setInterval(() => this.draw(), this.speed);
    }
}

// Initialize Matrix effect
function initMatrix(canvasId) {
    new MatrixRain(canvasId, {
        fontSize: 14,
        speed: 50
    });
}

// Glitch text effect on hover
function initGlitchEffect() {
    const glitchElements = document.querySelectorAll('.glitch');
    
    glitchElements.forEach(el => {
        el.addEventListener('mouseenter', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = '';
            }, 10);
        });
    });
}

// Terminal typing sound effect (visual only)
function typeSound() {
    // Could add audio here if desired
    // Keeping it visual-only for better user experience
}

// Random glitch effect for quotes
function initRandomGlitch() {
    const quotes = document.querySelectorAll('.quote');
    
    setInterval(() => {
        const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
        randomQuote.style.textShadow = '2px 0 #ff00c1, -2px 0 #00fff9';
        
        setTimeout(() => {
            randomQuote.style.textShadow = '';
        }, 100);
    }, 3000);
}

// Interactive terminal effect
function initTerminalEffect() {
    const inputs = document.querySelectorAll('.terminal-input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('active');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('active');
        });
        
        // Add keypress sound effect simulation
        input.addEventListener('keypress', function() {
            this.style.color = '#fff';
            setTimeout(() => {
                this.style.color = '';
            }, 100);
        });
    });
}

// Photo lightbox effect
function initPhotoLightbox() {
    const photos = document.querySelectorAll('.hacker-photo');
    
    photos.forEach(photo => {
        photo.addEventListener('click', function() {
            const modal = document.createElement('div');
            modal.className = 'photo-modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <img src="${this.src}" alt="${this.alt}">
                    <span class="close-modal">[CLOSE]</span>
                </div>
            `;
            
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
                cursor: pointer;
            `;
            
            modal.querySelector('.modal-content').style.cssText = `
                max-width: 90%;
                max-height: 90%;
                border: 2px solid #00ff41;
                box-shadow: 0 0 30px #00ff41;
            `;
            
            modal.querySelector('img').style.cssText = `
                max-width: 100%;
                max-height: 80vh;
                display: block;
            `;
            
            modal.querySelector('.close-modal').style.cssText = `
                display: block;
                text-align: center;
                padding: 10px;
                color: #00ff41;
                font-family: monospace;
            `;
            
            document.body.appendChild(modal);
            
            modal.addEventListener('click', function() {
                this.remove();
            });
        });
    });
}

// Binary clock effect (optional visual)
function createBinaryClock() {
    const clock = document.createElement('div');
    clock.className = 'binary-clock';
    clock.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        font-family: monospace;
        font-size: 0.8em;
        color: #008f11;
        z-index: 100;
    `;
    
    document.body.appendChild(clock);
    
    function update() {
        const now = new Date();
        const binary = {
            h: now.getHours().toString(2).padStart(5, '0'),
            m: now.getMinutes().toString(2).padStart(6, '0'),
            s: now.getSeconds().toString(2).padStart(6, '0')
        };
        
        clock.innerHTML = `
            <div>BIN_TIME: ${binary.h}:${binary.m}:${binary.s}</div>
            <div>DEC_TIME: ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}:${String(now.getSeconds()).padStart(2,'0')}</div>
        `;
    }
    
    update();
    setInterval(update, 1000);
}

// Initialize all effects when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initMatrix('matrixCanvas');
    initGlitchEffect();
    initRandomGlitch();
    initTerminalEffect();
    initPhotoLightbox();
    
    // Add binary clock to dashboard
    if (document.querySelector('.dashboard-page')) {
        createBinaryClock();
    }
});

// Export for use in other scripts
window.initMatrix = initMatrix;