/**
 * Utility functions for Tailwind theme
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

// Theme assets configuration
export const ThemeAssets = {
    images: {
        hdmBoot: '/themes/main/assets/images/php82.jpg',
        slim4: '/themes/main/assets/images/javascript.jpg',
        ephemeris: '/themes/main/assets/images/digital-marketing.jpg',
        logo: '/themes/main/assets/images/logo.svg'
    }
};

// CSS loading detection
export const CSSUtils = {
    /**
     * Add theme-loaded class immediately since CSS loads synchronously
     */
    detectCSSLoading() {
        // CSS loads synchronously now, so add class immediately
        document.documentElement.classList.add('theme-loaded');
    }
};

// Initialize theme utilities
export function initThemeUtils() {
    console.log('TailwindCSS theme loaded');
    console.log('Theme assets loaded:', ThemeAssets);

    // Initialize CSS loading detection
    document.addEventListener('DOMContentLoaded', () => {
        CSSUtils.detectCSSLoading();
        initEventListeners();
    });

    // Make utilities globally available for backward compatibility
    window.NavigationUtils = NavigationUtils;
    window.themeAssets = ThemeAssets;
}

// Initialize event listeners for data-action attributes
function initEventListeners() {
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
        }
    });
}
