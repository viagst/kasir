// router.js
const routes = [
  { path: '/login', component: LoginPage },
  { path: '/dashboard', component: DashboardAdmin, meta: { requiresAuth: true, role: 'super_admin' } },
  { path: '/kasir', component: DashboardKasir, meta: { requiresAuth: true, role: 'kasir' } },
  { path: '/transaksi', component: TransactionsPage },
  { path: '/produk', component: ProductsPage },
  { path: '/laporan', component: ReportsPage },
  { path: '/', redirect: '/login' }
]

// Navigation guard untuk cek role
router.beforeEach((to, from, next) => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  const token = localStorage.getItem('token')
  
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.meta.role && user.level !== to.meta.role) {
    alert('Akses ditolak! Role tidak sesuai.')
    next(user.level === 'super_admin' ? '/dashboard' : '/kasir')
  } else {
    next()
  }
})