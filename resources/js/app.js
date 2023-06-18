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

// commonly used components/store/plugin that we want to be globally available
import ErrorBox from '@/component/ErrorBox.vue'
import notify from '@/plugin/notify'
import { useErrorStore } from '@/store/ErrorStore'

const pinia = createPinia()
const app = createApp(App)

// add vue router to pinia stores so they can trigger navs
pinia.use(({store}) => {
  store.$router = markRaw(router)
});

app.config.globalProperties.$axios = axios
app.use(pinia)
app.use(Quasar, {
  plugins: {
    Notify,
  },
  iconSet: quasarIconSet,
})
app.use(router)

// add error system globally so we don't have to manually import them everywhere
app.component('ErrorBox', ErrorBox)
app.config.globalProperties.$notify = notify;
app.config.globalProperties.$error = useErrorStore();
// global vue error handler
app.config.errorHandler = function(err, vm, info) {
  app.config.globalProperties.$error.handle(err)
}

app.mount('#app')
