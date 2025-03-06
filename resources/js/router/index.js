import { createRouter, createWebHistory } from 'vue-router'
import { requireAuth, requireGuest } from '../middleware/auth'

// Import base components
import Dashboard from '../components/Dashboard.vue'
import AdminDashboard from '../components/admin/Dashboard.vue'
import ValidatorDashboard from '../components/validator/Dashboard.vue'
import ParticipantDashboard from '../components/participant/Dashboard.vue'

const routes = [
    {
        path: '/',
        redirect: '/dashboard'
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../components/auth/Login.vue'),
        beforeEnter: requireGuest
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../components/auth/Register.vue'),
        beforeEnter: requireGuest
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('../components/auth/ForgotPassword.vue'),
        beforeEnter: requireGuest
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        beforeEnter: requireAuth,
        children: [
            {
                path: '',
                redirect: to => {
                    const user = JSON.parse(localStorage.getItem('user'))
                    switch(user?.user_metadata?.role) {
                        case 'admin':
                            return { name: 'admin.dashboard' }
                        case 'validator':
                            return { name: 'validator.dashboard' }
                        case 'participant':
                            return { name: 'participant.dashboard' }
                        default:
                            return { name: 'participant.dashboard' }
                    }
                }
            },
            {
                path: 'admin',
                name: 'admin.dashboard',
                component: AdminDashboard,
                meta: { roles: ['admin'] }
            },
            {
                path: 'validator',
                name: 'validator.dashboard',
                component: ValidatorDashboard,
                meta: { roles: ['validator'] }
            },
            {
                path: 'participant',
                name: 'participant.dashboard',
                component: ParticipantDashboard,
                meta: { roles: ['participant'] }
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
