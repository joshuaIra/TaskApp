import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router/index.js';
import axios from 'axios';
import './bootstrap';
import '../css/app.css';

const app = createApp(App);
const pinia = createPinia();

// Configure Axios
axios.defaults.baseURL = import.meta.env.VITE_API_URL || '/api';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Store axios in app for use in components
app.config.globalProperties.$http = axios;

// Use plugins
app.use(pinia);
app.use(router);

// Mount app
app.mount('#app');
