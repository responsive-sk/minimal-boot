// Main theme entry point with TailwindCSS + Alpine.js
import Alpine from 'alpinejs';
import './style.css';

// Import images for Vite processing
import hdmBootImg from './images/php82.jpg';
import slim4Img from './images/javascript.jpg';
import ephemerisImg from './images/digital-marketing.jpg';
import logoSvg from './images/nav/logo.svg';

// Make images available globally for dynamic use
window.themeAssets = {
    images: {
        hdmBoot: hdmBootImg,
        slim4: slim4Img,
        ephemeris: ephemerisImg,
        logo: logoSvg
    }
};

// Dropdown functionality for Tailwind theme
function initializeDropdowns()
{
    // Handle all dropdown toggles
    document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
        const targetId = button.getAttribute('data-dropdown-toggle');
        const dropdown = document.getElementById(targetId);

        if (dropdown) {
            // Toggle dropdown on click
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== dropdown) {
                        menu.classList.add('hidden');
                        menu.classList.remove('opacity-100', 'visible');
                        menu.classList.add('opacity-0', 'invisible');
                        menu.style.display = 'none';
                    }
                });

                // Toggle current dropdown
                const isHidden = dropdown.classList.contains('hidden');

            if (isHidden) {
                dropdown.classList.remove('hidden', 'opacity-0', 'invisible');
                dropdown.classList.add('opacity-100', 'visible');
                // Force display with inline styles for reliable visibility
                dropdown.style.display = 'block';
                dropdown.style.opacity = '1';
                dropdown.style.visibility = 'visible';
                dropdown.style.zIndex = '9999';
            } else {
                dropdown.classList.add('hidden', 'opacity-0', 'invisible');
                dropdown.classList.remove('opacity-100', 'visible');
                dropdown.style.display = 'none';
            }
            });
        }
    });

    // Handle group hover dropdowns (desktop)
    document.querySelectorAll('.group').forEach(group => {
        const dropdown = group.querySelector('.group-hover\\:opacity-100, .group-hover\\:visible');

        if (dropdown) {
            let hoverTimeout;

            group.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
                dropdown.classList.remove('opacity-0', 'invisible');
                dropdown.classList.add('opacity-100', 'visible');
            });

            group.addEventListener('mouseleave', () => {
                hoverTimeout = setTimeout(() => {
                    dropdown.classList.remove('opacity-100', 'visible');
                    dropdown.classList.add('opacity-0', 'invisible');
                }, 150);
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.group') && !e.target.closest('[data-dropdown-toggle]')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden', 'opacity-0', 'invisible');
                menu.classList.remove('opacity-100', 'visible');
                menu.style.display = 'none';
            });
        }
    });

    // Handle mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Theme switching functionality
    document.querySelectorAll('.theme-switch-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const theme = this.dataset.theme;

            // Show loading state
            const button = document.getElementById('themeDropdown');
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
        });
    });
}

// Initialize Alpine.js
window.Alpine = Alpine;

// Start Alpine.js after a small delay to ensure DOM is fully ready
setTimeout(() => {
    Alpine.start();
}, 100);

// Initialize dropdowns when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeDropdowns);
} else {
    initializeDropdowns();
}

console.log('Main theme (TailwindCSS + Alpine.js) loaded');
console.log('Theme assets loaded:', window.themeAssets);
