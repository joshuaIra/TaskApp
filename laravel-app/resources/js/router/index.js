import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../pages/Dashboard.vue';
import TasksList from '../pages/TasksList.vue';
import TaskDetail from '../pages/TaskDetail.vue';
import CreateTask from '../pages/CreateTask.vue';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
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
    meta: { requiresAuth: true },
  },
  {
    path: '/tasks/:id',
    name: 'TaskDetail',
    component: TaskDetail,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const authState = window.TaskAppAuth ?? { isAuthenticated: false, loginUrl: '/login' };

  if (to.matched.some((record) => record.meta?.requiresAuth) && !authState.isAuthenticated) {
    window.location.href = authState.loginUrl || '/login';
    return;
  }

  next();
});

export default router;
