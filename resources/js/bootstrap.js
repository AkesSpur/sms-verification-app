import axios from 'axios';
window.axios = axios;

// Don't set X-Requested-With globally to allow regular form submissions to work properly
// Only add this header when making explicit AJAX calls
// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Create axios instance for AJAX requests that need the X-Requested-With header
window.axiosAjax = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
});
