import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF Token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Request interceptor for loading states
axios.interceptors.request.use(function (config) {
    // Show loading spinner
    document.body.classList.add('loading');
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Response interceptor
axios.interceptors.response.use(function (response) {
    // Hide loading spinner
    document.body.classList.remove('loading');
    return response;
}, function (error) {
    // Hide loading spinner
    document.body.classList.remove('loading');
    
    // Handle common errors
    if (error.response) {
        switch (error.response.status) {
            case 401:
                window.location.href = '/login';
                break;
            case 403:
                SkyBroker.toast('Access denied', 'danger');
                break;
            case 419:
                window.location.reload();
                break;
            case 422:
                // Validation errors will be handled by form components
                break;
            case 500:
                SkyBroker.toast('Server error occurred', 'danger');
                break;
        }
    }
    
    return Promise.reject(error);
});