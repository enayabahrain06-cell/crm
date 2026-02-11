import './bootstrap';

import axios from 'axios';
window.axios = axios;

// Set withCredentials to true for cross-domain requests (required for session cookies)
window.axios.defaults.withCredentials = true;

// Set base URL for API requests (will use relative URLs if not set)
window.axios.defaults.baseURL = '';

// Common headers for all requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag or cookie and set it for all axios requests
function getCsrfToken() {
    var token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        return token.getAttribute('content');
    }
    // Fallback: try to get from cookie
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf('XSRF-TOKEN=') === 0) {
            return decodeURIComponent(cookie.substring('XSRF-TOKEN='.length));
        }
    }
    return null;
}

// Set CSRF token for all axios requests
var csrfToken = getCsrfToken();
if (csrfToken) {
    window.axios.defaults.headers.common['X-XSRF-TOKEN'] = csrfToken;
}

// Handle axios response errors globally
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // CSRF token mismatch - reload the page to get new token
            console.warn('CSRF token mismatch, reloading page...');
            window.location.reload();
        }
        return Promise.reject(error);
    }
);

// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    var sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('show');
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // Handle chevron rotation for collapsible sections
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            setTimeout(function() {
                var chevron = this.querySelector('.chevron-icon');
                if (chevron) {
                    chevron.style.transform = this.classList.contains('collapsed') ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            }.bind(this), 10);
        });
    });

    // Close sidebar on route change (for SPA-like behavior)
    if (window.innerWidth < 992) {
        document.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (sidebar) {
                    sidebar.classList.remove('show');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }
            });
        });
    }
});

