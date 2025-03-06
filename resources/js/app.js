import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import axios from 'axios';

// Set up axios defaults
axios.defaults.baseURL = '/api';
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add request interceptor
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Add response interceptor
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            store.dispatch('auth/logout');
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

const app = createApp(App);

// Use plugins
app.use(router);
app.use(store);

// Mount app
app.mount('#app');
