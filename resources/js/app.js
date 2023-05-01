import '../css/app.css';

import { createApp, markRaw } from 'vue'
import { createPinia } from 'pinia'
import { Quasar, Notify } from 'quasar'
import axios from '@/plugin/axios'
import router from '@/plugin/router'
import App from '@/App.vue'

// quasar icons & css
import quasarIconSet from 'quasar/icon-set/svg-material-icons'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass'

const pinia = createPinia()
const app = createApp(App)

// add vue router to pinia stores so they can trigger navs
pinia.use(({store}) => {
  store.$router = markRaw(router)
});

app.config.globalProperties.axios = axios
app.use(pinia)
app.use(Quasar, {
  plugins: {
    Notify,
  },
  iconSet: quasarIconSet,
})
app.use(router)
app.mount('#app')
