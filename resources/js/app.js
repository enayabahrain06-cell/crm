import './bootstrap';

import axios from 'axios';
window.axios = axios;

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

