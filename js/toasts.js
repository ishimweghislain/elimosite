/**
 * Elimo Toast Utility
 */
const ElimoToast = {
    container: null,

    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'success', duration = 3000) {
        this.init();

        const toast = document.createElement('div');
        toast.className = `elimo-toast ${type}`;

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            info: 'fa-info-circle'
        };

        toast.innerHTML = `
            <div class="elimo-toast-content">
                <i class="fas ${icons[type]} elimo-toast-icon"></i>
                <span class="elimo-toast-message">${message}</span>
            </div>
            <button class="elimo-toast-close">&times;</button>
            <div class="elimo-toast-progress">
                <div class="elimo-toast-progress-bar" style="animation: progress ${duration}ms linear forwards"></div>
            </div>
        `;

        this.container.appendChild(toast);

        // Trigger reflow for animation
        setTimeout(() => toast.classList.add('show'), 10);

        const closeBtn = toast.querySelector('.elimo-toast-close');
        closeBtn.onclick = () => this.hide(toast);

        setTimeout(() => this.hide(toast), duration);
    },

    hide(toast) {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode === this.container) {
                this.container.removeChild(toast);
            }
        }, 400);
    },

    // Check for success/error messages in URL and trigger toasts
    checkURLMessages() {
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');

        if (success) {
            // Only show if not already covered by convertAlerts (alert box rendered by PHP)
            if (!document.querySelector('.alert-success')) {
                let msg = success;
                if (msg === 'added') msg = 'Item added successfully!';
                if (msg === 'updated') msg = 'Item updated successfully!';
                if (msg === 'deleted') msg = 'Item deleted successfully!';
                this.show(msg.replace(/-/g, ' '), 'success');
            }
        }
        if (error) {
            if (!document.querySelector('.alert-danger')) {
                this.show(error.replace(/-/g, ' '), 'error');
            }
        }
    }
};

// Initialize and check for messages on load
document.addEventListener('DOMContentLoaded', () => {
    // Also convert any existing .alert elements to toasts and hide them
    const convertAlerts = () => {
        document.querySelectorAll('.alert-success, .alert-danger, .alert-info, .alert-warning').forEach(alert => {
            if (alert.dataset.toastConverted) return;

            let type = 'success';
            if (alert.classList.contains('alert-danger')) type = 'error';
            if (alert.classList.contains('alert-info')) type = 'info';
            if (alert.classList.contains('alert-warning')) type = 'info';

            const message = alert.innerText.trim();
            if (message) {
                ElimoToast.show(message, type);
                alert.style.display = 'none';
                alert.dataset.toastConverted = 'true';
            }
        });
    };

    convertAlerts();
    ElimoToast.checkURLMessages();

    // Watch for dynamic alerts (AJAX responses)
    const observer = new MutationObserver(convertAlerts);
    observer.observe(document.body, { childList: true, subtree: true });
});

// Global shortcut
window.showToast = (msg, type, duration) => ElimoToast.show(msg, type, duration);
