import './bootstrap';
import Alpine from 'alpinejs';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Make Alpine available globally
window.Alpine = Alpine;
window.L = L;

// Alpine.js components for SkyBrokerSystem
document.addEventListener('alpine:init', () => {
    // Sidebar toggle component
    Alpine.data('sidebar', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        }
    }));
    
    // Dropdown component
    Alpine.data('dropdown', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        }
    }));
    
    // Modal component
    Alpine.data('modal', () => ({
        open: false,
        show() {
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        hide() {
            this.open = false;
            document.body.style.overflow = 'auto';
        }
    }));
    
    // Toast notifications
    Alpine.data('toast', () => ({
        visible: false,
        message: '',
        type: 'success',
        show(message, type = 'success') {
            this.message = message;
            this.type = type;
            this.visible = true;
            setTimeout(() => {
                this.visible = false;
            }, 5000);
        },
        hide() {
            this.visible = false;
        }
    }));
    
    // Search component
    Alpine.data('search', () => ({
        query: '',
        results: [],
        loading: false,
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }
            
            this.loading = true;
            // Implement search logic here
            setTimeout(() => {
                this.loading = false;
            }, 500);
        }
    }));
    
    // Table pagination component
    Alpine.data('pagination', (initialPage = 1) => ({
        currentPage: initialPage,
        perPage: 20,
        total: 0,
        get totalPages() {
            return Math.ceil(this.total / this.perPage);
        },
        get hasNext() {
            return this.currentPage < this.totalPages;
        },
        get hasPrev() {
            return this.currentPage > 1;
        },
        next() {
            if (this.hasNext) {
                this.currentPage++;
                this.loadPage();
            }
        },
        prev() {
            if (this.hasPrev) {
                this.currentPage--;
                this.loadPage();
            }
        },
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                this.loadPage();
            }
        },
        loadPage() {
            // Implement page loading logic
            console.log('Loading page:', this.currentPage);
        }
    }));
    
    // Form validation component
    Alpine.data('formValidation', () => ({
        errors: {},
        touched: {},
        validate(field, value, rules) {
            this.touched[field] = true;
            this.errors[field] = [];
            
            if (rules.required && (!value || value.trim() === '')) {
                this.errors[field].push('This field is required');
            }
            
            if (rules.email && value && !this.isValidEmail(value)) {
                this.errors[field].push('Please enter a valid email address');
            }
            
            if (rules.minLength && value && value.length < rules.minLength) {
                this.errors[field].push(`Minimum ${rules.minLength} characters required`);
            }
            
            if (rules.maxLength && value && value.length > rules.maxLength) {
                this.errors[field].push(`Maximum ${rules.maxLength} characters allowed`);
            }
            
            return this.errors[field].length === 0;
        },
        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },
        hasError(field) {
            return this.touched[field] && this.errors[field] && this.errors[field].length > 0;
        },
        getError(field) {
            return this.hasError(field) ? this.errors[field][0] : '';
        }
    }));
});

// Start Alpine
Alpine.start();

// Global utilities
window.SkyBroker = {
    // Format currency
    formatCurrency(amount, currency = 'PLN') {
        return new Intl.NumberFormat('pl-PL', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },
    
    // Format date
    formatDate(date, options = {}) {
        const defaultOptions = {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        };
        return new Intl.DateTimeFormat('pl-PL', {...defaultOptions, ...options}).format(new Date(date));
    },
    
    // Format relative time
    formatRelativeTime(date) {
        const rtf = new Intl.RelativeTimeFormat('pl-PL', { numeric: 'auto' });
        const now = new Date();
        const target = new Date(date);
        const diffInSeconds = (target - now) / 1000;
        
        if (Math.abs(diffInSeconds) < 60) {
            return rtf.format(Math.round(diffInSeconds), 'second');
        } else if (Math.abs(diffInSeconds) < 3600) {
            return rtf.format(Math.round(diffInSeconds / 60), 'minute');
        } else if (Math.abs(diffInSeconds) < 86400) {
            return rtf.format(Math.round(diffInSeconds / 3600), 'hour');
        } else {
            return rtf.format(Math.round(diffInSeconds / 86400), 'day');
        }
    },
    
    // Copy to clipboard
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.error('Failed to copy text: ', err);
            return false;
        }
    },
    
    // Show toast notification
    toast(message, type = 'success') {
        const event = new CustomEvent('show-toast', {
            detail: { message, type }
        });
        window.dispatchEvent(event);
    }
};

// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.querySelectorAll('[data-flash-message]');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
});

// Loading states for forms
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-loading]');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
            }
        });
    });
});
