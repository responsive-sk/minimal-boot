/**
 * Utility functions for Bootstrap theme
 * Replaces inline JavaScript handlers
 */

// Navigation utilities
export const NavigationUtils = {
    /**
     * Go back in browser history
     */
    goBack() {
        history.back();
    },

    /**
     * Reload current page
     */
    reload() {
        location.reload();
    }
};

// Theme utilities
export const ThemeUtils = {
    /**
     * Toggle between light and dark theme
     */
    toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-bs-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';

        // Add smooth transition
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';

        html.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);

        // Update theme icons
        this.updateAllThemeIcons(newTheme);

        // Show notification
        this.showThemeNotification(newTheme);

        // Remove transition after animation
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    },

    /**
     * Update all theme toggle icons
     */
    updateAllThemeIcons(theme) {
        const icon = theme === 'dark' ? '☀️' : '🌙';
        const ariaLabel = `Switch to ${theme === 'dark' ? 'light' : 'dark'} theme`;

        // Update floating button
        const themeIcon = document.querySelector('.theme-toggle-icon');
        const themeToggle = document.getElementById('theme-toggle');

        if (themeIcon) {
            themeIcon.textContent = icon;
        }
        if (themeToggle) {
            themeToggle.setAttribute('aria-label', ariaLabel);
        }

        // Update navigation checkbox
        const navThemeCheckbox = document.getElementById('nav-theme-checkbox');
        if (navThemeCheckbox) {
            navThemeCheckbox.checked = theme === 'dark';
        }
    },

    /**
     * Show theme change notification
     */
    showThemeNotification(theme) {
        const notification = document.getElementById('theme-notification');
        const message = document.getElementById('theme-message');

        if (notification && message) {
            message.textContent = `Switched to ${theme} theme`;
            notification.style.display = 'block';

            // Auto-hide after 2 seconds with Bootstrap animation
            setTimeout(() => {
                const alert = notification.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => {
                        notification.style.display = 'none';
                        alert.classList.add('show');
                    }, 150);
                }
            }, 2000);
        }
    },

    /**
     * Initialize theme switching functionality
     */
    initThemeSwitching() {
        const themeToggle = document.getElementById('theme-toggle');
        const navThemeCheckbox = document.getElementById('nav-theme-checkbox');
        const html = document.documentElement;

        // Get current theme from HTML attribute (set by inline script)
        const currentTheme = html.getAttribute('data-bs-theme') || 'light';
        this.updateAllThemeIcons(currentTheme);

        // Add event listeners to both buttons
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }

        if (navThemeCheckbox) {
            navThemeCheckbox.addEventListener('change', () => this.toggleTheme());
        }

        // Theme switching functionality for dropdown links
        document.querySelectorAll('.theme-switch-link').forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();

                const theme = link.dataset.theme;

                // Show loading state
                const button = document.getElementById('themeDropdown');
                if (button) {
                    const originalText = button.textContent;
                    button.textContent = 'Switching...';
                    button.disabled = true;

                    // Make AJAX request to switch theme
                    fetch('/theme/switch', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ theme: theme })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to apply new theme
                            window.location.reload();
                        } else {
                            alert('Error switching theme: ' + (data.error || 'Unknown error'));
                            button.textContent = originalText;
                            button.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Theme switch error:', error);
                        alert('Error switching theme. Please try again.');
                        button.textContent = originalText;
                        button.disabled = false;
                    });
                }
            });
        });
    }
};

// Theme assets configuration
export const ThemeAssets = {
    images: {
        hdmBoot: '/themes/bootstrap/assets/images/php82.jpg',
        slim4: '/themes/bootstrap/assets/images/javascript.jpg',
        ephemeris: '/themes/bootstrap/assets/images/digital-marketing.jpg',
        logo: '/themes/bootstrap/assets/images/logo.svg'
    }
};

// Initialize theme utilities
export function initThemeUtils()
{
    console.log('Bootstrap theme loaded');
    console.log('Theme assets loaded:', ThemeAssets);

    // Initialize event listeners for data-action attributes
    initEventListeners();

    // Initialize theme switching functionality
    ThemeUtils.initThemeSwitching();

    // Make utilities globally available for backward compatibility
    window.NavigationUtils = NavigationUtils;
    window.ThemeUtils = ThemeUtils;
    window.themeAssets = ThemeAssets;
}

// Initialize event listeners for data-action attributes
function initEventListeners()
{
    document.addEventListener('click', (e) => {
        const action = e.target.getAttribute('data-action');

        switch (action) {
            case 'go-back':
                e.preventDefault();
                NavigationUtils.goBack();
                break;
            case 'reload':
                e.preventDefault();
                NavigationUtils.reload();
                break;
            case 'toggle-theme':
                e.preventDefault();
                ThemeUtils.toggleTheme();
                break;
        }
    });
}
