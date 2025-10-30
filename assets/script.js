class ExpiryApp {
    constructor() {
        this.apiUrl = window.location.origin;
        this.settings = {};
        this.init();
    }

    async init() {
        await this.loadSettings();
        this.applyColors();
        this.createFloatingBackgrounds();
        this.startCountdown();
        this.attachEventListeners();
    }

    async loadSettings() {
        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();
            
            if (data.success) {
                this.settings = data.data;
                this.updateUI();
                
                // Update analytics display
                this.updateAnalytics();
            }
        } catch (error) {
            console.error('Failed to load settings:', error);
            this.showError('Failed to connect to server');
        }
    }

    updateUI() {
        // Update main content
        document.getElementById('dialogTitle').textContent = this.settings.dialog_title;
        document.getElementById('dialogMessage').textContent = this.settings.dialog_message;
        
        // Update update button
        const updateBtn = document.querySelector('.update-btn');
        updateBtn.textContent = this.settings.download_button.text;
        updateBtn.onclick = () => this.handleUpdateClick();
        
        // Update channel buttons
        this.updateChannelButtons();
        
        // Update exit button
        const exitBtn = document.querySelector('.exit-btn');
        exitBtn.textContent = this.settings.exit_button;
        exitBtn.onclick = () => this.handleExitClick();
        
        // Show/hide countdown based on expiry
        this.toggleCountdown();
    }

    applyColors() {
        if (this.settings.colors) {
            document.documentElement.style.setProperty('--primary-color', this.settings.colors.primary);
            document.documentElement.style.setProperty('--bg-color', this.settings.colors.background);
            document.documentElement.style.setProperty('--text-color', this.settings.colors.text);
        }
    }

    updateChannelButtons() {
        const container = document.getElementById('channelButtons');
        container.innerHTML = '';
        
        this.settings.FFMainActivityX.forEach((channel, index) => {
            if (channel.enabled) {
                const button = document.createElement('button');
                button.className = `button channel-btn`;
                button.textContent = channel.text;
                button.onclick = () => this.handleChannelClick(channel.link);
                
                // Staggered animation
                button.style.animationDelay = `${0.3 + (index * 0.1)}s`;
                
                container.appendChild(button);
            }
        });
    }

    toggleCountdown() {
        const countdownElement = document.getElementById('countdownTimer');
        const expiryDate = new Date(this.settings.expiry_date);
        const now = new Date();
        
        if (now < expiryDate) {
            countdownElement.style.display = 'block';
            this.startCountdown();
        } else {
            countdownElement.style.display = 'none';
        }
    }

    startCountdown() {
        const countdownElement = document.getElementById('countdownTimer');
        const expiryDate = new Date(this.settings.expiry_date);
        
        const updateTimer = () => {
            const now = new Date();
            const distance = expiryDate - now;
            
            if (distance < 0) {
                countdownElement.innerHTML = '⏰ TIME EXPIRED!';
                countdownElement.className = 'countdown expired';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            countdownElement.innerHTML = 
                `⏳ Time Left: ${days}d ${hours}h ${minutes}m ${seconds}s`;
            countdownElement.className = 'countdown';
        };
        
        updateTimer();
        setInterval(updateTimer, 1000);
    }

    createFloatingBackgrounds() {
        const container = document.body;
        
        for (let i = 0; i < 3; i++) {
            const element = document.createElement('div');
            element.className = 'floating-bg';
            container.appendChild(element);
        }
    }

    updateAnalytics() {
        if (this.settings.analytics) {
            document.getElementById('totalChecks').textContent = 
                this.formatNumber(this.settings.analytics.total_checks);
            document.getElementById('lastCheck').textContent = 
                this.formatDate(this.settings.analytics.last_check);
            
            // Update stats display
            this.updateStatsDisplay();
        }
    }

    updateStatsDisplay() {
        const statsContainer = document.querySelector('.stats');
        if (statsContainer && this.settings.analytics) {
            statsContainer.innerHTML = `
                <div class="stat-item">
                    <div class="stat-number">${this.formatNumber(this.settings.analytics.total_checks)}</div>
                    <div class="stat-label">Total Checks</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${this.formatNumber(this.settings.analytics.button_clicks.download)}</div>
                    <div class="stat-label">Update Clicks</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${this.formatNumber(this.settings.analytics.button_clicks.exit)}</div>
                    <div class="stat-label">Exit Clicks</div>
                </div>
            `;
        }
    }

    async handleUpdateClick() {
        // Track click
        await this.trackAction('download');
        
        // Open update link
        window.open(this.settings.download_button.link, '_blank');
        
        // Button click animation
        this.animateButton('.update-btn');
    }

    async handleChannelClick(link) {
        window.open(link, '_blank');
        this.animateButton('.channel-btn:last-child');
    }

    async handleExitClick() {
        // Track click
        await this.trackAction('exit');
        
        // Exit animation
        this.animateButton('.exit-btn');
        
        setTimeout(() => {
            if (confirm('Are you sure you want to exit?')) {
                // For web - show message, for app - close app
                this.showExitMessage();
            }
        }, 500);
    }

    async trackAction(action) {
        try {
            await fetch(`${this.apiUrl}?action=${action}`);
            // Reload analytics
            this.loadSettings();
        } catch (error) {
            console.error('Failed to track action:', error);
        }
    }

    animateButton(selector) {
        const button = document.querySelector(selector);
        button.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            button.style.transform = '';
        }, 150);
    }

    showExitMessage() {
        const message = document.createElement('div');
        message.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            color: white;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #ff4444;
            z-index: 1000;
            text-align: center;
        `;
        message.innerHTML = `
            <h3>App Closed</h3>
            <p>Please update to continue using the app.</p>
        `;
        
        document.body.appendChild(message);
        
        setTimeout(() => {
            message.remove();
        }, 3000);
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ff4444;
            color: white;
            padding: 15px;
            border-radius: 8px;
            z-index: 1000;
            animation: slideInRight 0.5s ease-out;
        `;
        errorDiv.textContent = message;
        
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleString();
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ExpiryApp();
});

// Add additional CSS for animations
const additionalStyles = `
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.countdown.expired {
    background: rgba(255, 68, 68, 0.2);
    border-color: #ff4444;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);