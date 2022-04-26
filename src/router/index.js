import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import HeaderView from '../views/HeaderView.vue'
import FooterView from '../views/FooterView.vue'
import AboutView from '../views/AboutView.vue'
import RightListView from '../views/RightListView.vue'
import loginView from '../views/LoginView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    components: {
      default: HomeView,
      Header:HeaderView,
      Footer:FooterView,
      RightList:RightListView
    }
  },
  {
    path: '/about',
    name: 'about',
    components: {
      default: AboutView,
      Header:HeaderView,
      Footer:FooterView,
      RightList:RightListView
    }
  },
  {
    path: '/login',
    name: 'login',
    components: {
      default: loginView,
      Header:HeaderView,
      Footer:FooterView,
      RightList:RightListView
    },
    meta:{ requiresAuth: true}
  }
]

const router = createRouter({
  history: createWebHistory(),
  mode: 'history',
  routes
})

export default router
