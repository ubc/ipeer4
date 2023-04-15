import '../css/app.css';

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { Quasar } from 'quasar'
import axios from '@/plugins/axios'
import router from '@/plugins/router'
import App from '@/App.vue'

// quasar icons & css
import quasarIconSet from 'quasar/icon-set/svg-material-icons'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass'

const pinia = createPinia()
const app = createApp(App)
app.config.globalProperties.axios = axios
app.use(pinia)
app.use(Quasar, {
  plugins: {},
  iconSet: quasarIconSet,
})
app.use(router)
app.mount('#app')
