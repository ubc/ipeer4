import { createRouter, createWebHashHistory } from 'vue-router'
import HomePage from '@/page/HomePage.vue'
import LoginPage from '@/page/LoginPage.vue'
import RegisterPage from '@/page/RegisterPage.vue'
import AdminHomePage from '@/page/admin/AdminHomePage.vue'

const routes = [
  { path: '/', component: HomePage },
  { path: '/login', component: LoginPage },
  { path: '/register', component: RegisterPage },
  { path: '/admin', component: AdminHomePage },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
})

export default router
