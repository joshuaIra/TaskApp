import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../pages/Dashboard.vue';
import ManagerDashboard from '../pages/ManagerDashboard.vue';
import MemberDashboard from '../pages/MemberDashboard.vue';
import TasksList from '../pages/TasksList.vue';
import TaskDetail from '../pages/TaskDetail.vue';
import CreateTask from '../pages/CreateTask.vue';
import Reports from '../pages/Reports.vue';
import Settings from '../pages/Settings.vue';
import UserManagement from '../pages/UserManagement.vue';

const resolveDashboardNameByRole = (role) => {
  if (role === 'admin') return 'AdminDashboard';
  if (role === 'manager') return 'ManagerDashboard';
  return 'MemberDashboard';
};

const routes = [
  {
    path: '/',
    name: 'DashboardHome',
    redirect: () => {
      const role = window.TaskAppAuth?.user?.role;
      return { name: resolveDashboardNameByRole(role) };
    },
    meta: { requiresAuth: true },
  },
  {
    path: '/dashboard/admin',
    name: 'AdminDashboard',
    component: Dashboard,
    meta: { requiresAuth: true, roles: ['admin'] },
  },
  {
    path: '/dashboard/manager',
    name: 'ManagerDashboard',
    component: ManagerDashboard,
    meta: { requiresAuth: true, roles: ['manager'] },
  },
  {
    path: '/dashboard/member',
    name: 'MemberDashboard',
    component: MemberDashboard,
    meta: { requiresAuth: true, roles: ['member'] },
  },
  {
    path: '/contact',
    name: 'Contact',
    component: () => import('../pages/Contact.vue'),
  },
  {
    path: '/privacy',
    name: 'Privacy',
    component: () => import('../pages/Privacy.vue'),
  },
  {
    path: '/terms',
    name: 'Terms',
    component: () => import('../pages/Terms.vue'),
  },
  {
    path: '/tasks',
    name: 'TasksList',
    component: TasksList,
    meta: { requiresAuth: true },
  },
  {
    path: '/tasks/create',
    name: 'CreateTask',
    component: CreateTask,
    meta: { requiresAuth: true, roles: ['admin', 'manager'] },
  },
  {
    path: '/tasks/:id',
    name: 'TaskDetail',
    component: TaskDetail,
    meta: { requiresAuth: true },
  },
  {
    path: '/reports',
    name: 'Reports',
    component: Reports,
    meta: { requiresAuth: true, roles: ['admin', 'manager'] },
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: { requiresAuth: true, roles: ['admin'] },
  },
  {
    path: '/user-management',
    name: 'UserManagement',
    component: UserManagement,
    meta: { requiresAuth: true, roles: ['admin'] },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const authState = window.TaskAppAuth ?? { isAuthenticated: false, user: null, loginUrl: '/login' };
  const userRole = authState?.user?.role || null;

  if (to.matched.some((record) => record.meta?.requiresAuth) && !authState.isAuthenticated) {
    window.location.href = authState.loginUrl || '/login';
    return;
  }

  const allowedRoles = to.matched.find((record) => Array.isArray(record.meta?.roles))?.meta?.roles;
  if (allowedRoles && !allowedRoles.includes(userRole)) {
    next({ name: resolveDashboardNameByRole(userRole) });
    return;
  }

  next();
});

export default router;
