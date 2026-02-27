<template>
  <div
    v-if="uiStore.sidebarOpen"
    class="fixed inset-0 z-30 bg-black/30 lg:hidden"
    @click="uiStore.sidebarOpen = false"
  ></div>

  <aside
    :class="[
      'fixed left-0 top-0 bottom-0 z-40 w-full lg:w-64 xl:w-72 transition-transform duration-300 ease-in-out',
      uiStore.sidebarOpen ? 'translate-x-0' : '-translate-x-full',
    ]"
  >
    <div class="h-full bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700">
      <div class="h-full overflow-y-auto px-6 pb-6 pt-16 flex flex-col">
        <div class="text-xs font-bold tracking-wider text-slate-400 uppercase px-2 mb-5">Workspace</div>

        <nav class="space-y-2">
          <router-link to="/contact" class="side-link" :class="{ 'side-link-active': $route.path === '/contact' }" @click="handleNavClick">Contact</router-link>
          <router-link to="/" class="side-link" :class="{ 'side-link-active': $route.path === '/' }" @click="handleNavClick">Dashboard</router-link>
          <router-link to="/tasks" class="side-link" :class="{ 'side-link-active': $route.path === '/tasks' || $route.path.startsWith('/tasks/') }" @click="handleNavClick">All Tasks</router-link>
          <router-link to="/tasks/create" class="side-link" :class="{ 'side-link-active': $route.path === '/tasks/create' }" @click="handleNavClick">Create Task</router-link>
        </nav>

        <div class="mt-8 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm p-6">
          <h3 class="text-3xl font-semibold text-slate-900 dark:text-white">Quick Stats</h3>
          <div class="mt-6 space-y-3 text-sm">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
              <span>Total tasks</span>
              <span class="font-semibold text-slate-900 dark:text-white">{{ totalTasks }}</span>
            </div>
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
              <span>Completed</span>
              <span class="font-semibold text-emerald-500">{{ completedTasks }}</span>
            </div>
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
              <span>In progress</span>
              <span class="font-semibold text-blue-600">{{ inProgressTasks }}</span>
            </div>
          </div>
        </div>

        <div class="mt-auto pt-8">
          <button @click="uiStore.toggleDarkMode()" class="w-full rounded-2xl border border-slate-200 dark:border-slate-700 py-4 text-lg font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            {{ uiStore.darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode' }}
          </button>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue';
import { useUIStore } from '../stores/uiStore';
import { useTaskStore } from '../stores/taskStore';

const uiStore = useUIStore();
const taskStore = useTaskStore();

const totalTasks = computed(() => taskStore.tasks.length);
const completedTasks = computed(() => taskStore.tasks.filter(task => task.status === 'completed').length);
const inProgressTasks = computed(() => taskStore.tasks.filter(task => task.status === 'in_progress').length);

const handleNavClick = () => {
  if (window.innerWidth < 1024) {
    uiStore.sidebarOpen = false;
  }
};

</script>

<style scoped>
.side-link {
  display: flex;
  align-items: center;
  padding: 0.9rem 1rem;
  border-radius: 1rem;
  color: #475569;
  font-size: 1.2rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.side-link:hover {
  background: #eef2f7;
  color: #1e293b;
}

.side-link-active {
  background: #e6edf6;
  color: #1d4ed8;
}

.dark .side-link {
  color: #cbd5e1;
}

.dark .side-link:hover {
  background: #1e293b;
  color: #f8fafc;
}

.dark .side-link-active {
  background: rgba(59, 130, 246, 0.2);
  color: #93c5fd;
}

@media (max-width: 1024px) {
  .side-link {
    font-size: 1.4rem;
  }
}
</style>
