import store from '../store';

const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/Login.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/Register.vue'),
    meta: { guest: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/Dashboard.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('../views/Profile.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/payment',
    name: 'payment',
    component: () => import('../views/Payment.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/qr-code',
    name: 'qr-code',
    component: () => import('../views/QrCode.vue'),
    meta: { requiresAuth: true },
  },
];

export default routes;
