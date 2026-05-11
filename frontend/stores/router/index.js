import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import Dashboard from '../components/Dashboard.vue'
import KasirDashboard from '../components/KasirDashboard.vue'
import RegistrasiKasir from '../components/RegistrasiKasir.vue'
import Persetujuan from '../components/Persetujuan.vue'
import Produk from '../components/Produk.vue'
import Kategori from '../components/Kategori.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true, role: 'super_admin' }
  },
  {
    path: '/kasir',
    name: 'KasirDashboard',
    component: KasirDashboard,
    meta: { requiresAuth: true, role: 'kasir' }
  },
  {
    path: '/registrasi-kasir',
    name: 'RegistrasiKasir',
    component: RegistrasiKasir,
    meta: { requiresAuth: true, role: 'super_admin' }
  },
  {
    path: '/persetujuan',
    name: 'Persetujuan',
    component: Persetujuan,
    meta: { requiresAuth: true, role: 'super_admin' }
  },
  {
    path: '/produk',
    name: 'Produk',
    component: Produk,
    meta: { requiresAuth: true }
  },
  {
    path: '/kategori',
    name: 'Kategori',
    component: Kategori,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guard untuk authentication dan role
router.beforeEach((to, from, next) => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  const token = localStorage.getItem('token')
  
  // Jika route membutuhkan auth
  if (to.meta.requiresAuth) {
    if (!token) {
      next('/')
    } else {
      // Cek role
      if (to.meta.role && user.level !== to.meta.role) {
        // Redirect berdasarkan role
        if (user.level === 'super_admin') {
          next('/dashboard')
        } else {
          next('/kasir')
        }
      } else {
        next()
      }
    }
  } else {
    next()
  }
})

export default router