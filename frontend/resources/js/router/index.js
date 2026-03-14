import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../pages/Dashboard.vue';
import TasksList from '../pages/TasksList.vue';
import TaskDetail from '../pages/TaskDetail.vue';
import CreateTask from '../pages/CreateTask.vue';
import Board from '../pages/Board.vue';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('../pages/About.vue'),
  },
  {
    path: '/contact',
    name: 'Contact',
    component: () => import('../pages/Contact.vue'),
  },
  {
    path: '/tasks',
    name: 'TasksList', 
    component: TasksList,
  },
  {
    path: '/tasks/create',
    name: 'CreateTask',
    component: CreateTask,
  },
  {
    path: '/tasks/:id',
    name: 'TaskDetail',
    component: TaskDetail,
  },
  {
  path: '/board',
  name: 'Board',
  component: Board,
},
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
