import { createRouter, createWebHistory } from 'vue-router'
import store from './store'

// Auth Components
import LoginForm from './components/auth/LoginForm.vue'
import RegisterForm from './components/auth/RegisterForm.vue'

// Participant Components
import ParticipantLayout from './components/layouts/ParticipantLayout.vue'
import ParticipantDashboard from './components/participant/Dashboard.vue'
import ProfileUpdate from './components/profile/ProfileUpdate.vue'
import QrCodeDisplay from './components/qr/QrCodeDisplay.vue'

// Admin Components
import AdminLayout from './components/layouts/AdminLayout.vue'
import AdminDashboard from './components/admin/Dashboard.vue'
import AdminReports from './components/admin/Reports.vue'

// Validator Components
import QrValidator from './components/validator/QrValidator.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: LoginForm,
    meta: { guest: true }
  },
  {
    path: '/register',
    component: RegisterForm,
    meta: { guest: true }
  },
  // Participant Routes
  {
    path: '/dashboard/participant',
    component: ParticipantLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        component: ParticipantDashboard,
        name: 'participant-dashboard'
      },
      {
        path: 'profile',
        component: ProfileUpdate,
        name: 'participant-profile'
      },
      {
        path: 'qr-code',
        component: QrCodeDisplay,
        name: 'participant-qr-code'
      }
    ]
  },
  // Admin Routes
  {
    path: '/dashboard/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: '',
        component: AdminDashboard,
        name: 'admin-dashboard'
      },
      {
        path: 'reports',
        component: AdminReports,
        name: 'admin-reports'
      }
    ]
  },
  // Validator Routes
  {
    path: '/dashboard/validator',
    component: QrValidator,
    meta: { requiresAuth: true, requiresValidator: true },
    name: 'validator-dashboard'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = store.state.auth.token !== null
  const userRole = store.state.auth.user?.role

  if (to.meta.guest && isAuthenticated) {
    const dashboardPath = getDashboardPath(userRole)
    return next(dashboardPath)
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return next('/login')
  }

  if (to.meta.requiresAdmin && userRole !== 'admin') {
    const dashboardPath = getDashboardPath(userRole)
    return next(dashboardPath)
  }

  if (to.meta.requiresValidator && userRole !== 'validator') {
    const dashboardPath = getDashboardPath(userRole)
    return next(dashboardPath)
  }

  next()
})

function getDashboardPath(role) {
  switch (role) {
    case 'admin':
      return '/dashboard/admin'
    case 'validator':
      return '/dashboard/validator'
    default:
      return '/dashboard/participant'
  }
}

export default router
