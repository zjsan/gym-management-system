import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000' + '/api',
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
})

api.defaults.withCredentials = true;
api.defaults.withXSRFToken = true;

export default api;