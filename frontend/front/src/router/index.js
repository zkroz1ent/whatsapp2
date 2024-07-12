import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginPage.vue'
import RegisterView from '../views/RegisterView.vue'
import ChatPage from '../views/ChatPage.vue'
import ProfilePage from '../views/Profil/ProfilePage.vue'
import SettingsPage from '../views/Settings/SettingsPage.vue'
const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginView
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView
  },
  {
    path: '/chat',
    name: 'ChatPage',
    component: ChatPage
  },
  {
    path: '/profile',
    name: 'ProfilePage',
    component: ProfilePage
  },
  {
    path: '/settings',
    name: 'SettingsPage',
    component: SettingsPage
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router
