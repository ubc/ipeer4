import '../css/app.css';

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from '@/plugins/axios'
import App from '@/App.vue'

const pinia = createPinia()
const app = createApp(App)
app.config.globalProperties.axios = axios
app.use(pinia)
app.mount('#app')
