// Alpine.js for demo pages only
// This file is loaded only on /demo routes to showcase Alpine.js functionality

import Alpine from 'alpinejs';

// Alpine.js demo components
document.addEventListener('alpine:init', () => {
    // Counter component for demo
    Alpine.data('counter', () => ({
        count: 0,
        increment() {
            this.count++;
        },
        decrement() {
            this.count--;
        }
    }));

    // Toggle component for demo
    Alpine.data('toggle', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        }
    }));

    // Dropdown component for demo
    Alpine.data('dropdown', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        }
    }));

    // Tab component for demo
    Alpine.data('tabs', () => ({
        activeTab: 'tab1',
        setActiveTab(tab) {
            this.activeTab = tab;
        },
        isActiveTab(tab) {
            return this.activeTab === tab;
        }
    }));

    // Modal component for demo
    Alpine.data('modal', () => ({
        open: false,
        show() {
            this.open = true;
        },
        hide() {
            this.open = false;
        }
    }));

    // Form validation demo
    Alpine.data('form', () => ({
        email: '',
        password: '',
        errors: {},
        validate() {
            this.errors = {};

            if (!this.email) {
                this.errors.email = 'Email is required';
            } else if (!/\S+@\S+\.\S+/.test(this.email)) {
                this.errors.email = 'Email is invalid';
            }

            if (!this.password) {
                this.errors.password = 'Password is required';
            } else if (this.password.length < 6) {
                this.errors.password = 'Password must be at least 6 characters';
            }

            return Object.keys(this.errors).length === 0;
        },
        submit() {
            if (this.validate()) {
                alert('Form is valid! (Demo only)');
            }
        }
    }));

    // Search component for demo
    Alpine.data('search', () => ({
        query: '',
        results: [],
        items: [
            'Apple', 'Banana', 'Cherry', 'Date', 'Elderberry',
            'Fig', 'Grape', 'Honeydew', 'Kiwi', 'Lemon'
        ],
        search() {
            if (this.query.length > 0) {
                this.results = this.items.filter(
                    item =>
                    item.toLowerCase().includes(this.query.toLowerCase())
                );
            } else {
                this.results = [];
            }
        }
    }));
});

// Initialize Alpine.js for demo
window.Alpine = Alpine;

// Start Alpine.js efficiently
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        requestAnimationFrame(() => {
            Alpine.start();
        });
    });
} else {
    requestAnimationFrame(() => {
        Alpine.start();
    });
}

console.log('Alpine.js demo components loaded');
