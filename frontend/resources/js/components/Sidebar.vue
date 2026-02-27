<template>
  <aside
    :class="[
      'fixed left-0 top-16 z-40 transition-all duration-300 ease-in-out overflow-hidden',
      uiStore.sidebarOpen ? 'w-64' : 'w-0',
    ]"
  >
    <div class="h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700">
      <!-- Sidebar content wrapper -->
      <div class="w-64 h-full overflow-y-auto">
        <nav class="p-4 space-y-2">
          <!-- Main Navigation -->
          <div class="mb-4">
            <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
              Main
            </p>
            
            <router-link
              to="/"
              class="nav-item group"
              active-class="nav-active"
            >
              <div class="absolute left-0 w-1 h-12 bg-blue-500 rounded-r-full transition-all duration-300"
                :class="$route.path === '/' ? 'opacity-100' : 'opacity-0'"
              ></div>
              <div class="nav-icon">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
              </div>
              <span class="nav-text">Dashboard</span>
            </router-link>

            <router-link
              to="/tasks"
              class="nav-item group"
              :class="{ 'nav-active': $route.path === '/tasks' || $route.path.startsWith('/tasks/') }"
            >
              <div class="absolute left-0 w-1 h-12 bg-blue-500 rounded-r-full transition-all duration-300"
                :class="$route.path === '/tasks' || $route.path.startsWith('/tasks/') ? 'opacity-100' : 'opacity-0'"
              ></div>
              <div class="nav-icon">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a1 1 0 000-2H5a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V5a1 1 0 10-2 0v10a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" clip-rule="evenodd" />
                </svg>
              </div>
              <span class="nav-text">All Tasks</span>
            </router-link>

            <router-link
              to="/tasks?filter=my"
              class="nav-item group"
            >
              <div class="absolute left-0 w-1 h-12 bg-blue-500 rounded-r-full transition-all duration-300 opacity-0"></div>
              <div class="nav-icon">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                </svg>
              </div>
              <span class="nav-text">My Tasks</span>
            </router-link>
          </div>

          <!-- Divider -->
          <div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>

          <!-- Quick Actions -->
          <div class="mb-4">
            <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
              Quick Actions
            </p>

            <router-link
              to="/tasks/create"
              class="nav-item group bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30"
            >
              <div class="absolute left-0 w-1 h-12 bg-blue-500 rounded-r-full transition-all duration-300 opacity-0"></div>
              <div class="nav-icon text-blue-600 dark:text-blue-400">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <span class="nav-text text-blue-600 dark:text-blue-400 font-medium">New Task</span>
            </router-link>
          </div>

          <!-- Divider -->
          <div class="my-4 border-t border-gray-200 dark:border-gray-700"></div>

          <!-- Settings -->
          <div>
            <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
              Settings
            </p>

            <button
              @click="toggleDarkMode"
              class="nav-item group w-full text-left"
            >
              <div class="absolute left-0 w-1 h-12 bg-blue-500 rounded-r-full transition-all duration-300 opacity-0"></div>
              <div class="nav-icon">
                <svg v-if="!uiStore.darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
                <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l-2.12-2.12a1 1 0 00-1.414 1.414l2.12 2.12a1 1 0 001.414-1.414zM2.05 6.464l2.12 2.12a1 1 0 101.414-1.414L3.464 5.05a1 1 0 00-1.414 1.414z" clip-rule="evenodd" />
                </svg>
              </div>
              <span class="nav-text">{{ uiStore.darkMode ? 'Light Mode' : 'Dark Mode' }}</span>
            </button>

            <button
              @click="toggleSidebar"
              class="nav-item group w-full text-left"
            >
              <div class="absolute left-0 w-1 h-12 bg-blue-500 rounded-r-full transition-all duration-300 opacity-0"></div>
              <div class="nav-icon">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </div>
              <span class="nav-text">Collapse</span>
            </button>
          </div>
        </nav>

        <!-- Sidebar footer -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
          <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
              JD
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">John Doe</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Pro Member</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useUIStore } from '../stores/uiStore';

const uiStore = useUIStore();

const toggleDarkMode = () => {
  uiStore.toggleDarkMode();
};

const toggleSidebar = () => {
  uiStore.toggleSidebar();
};
</script>

<style scoped>
.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  transition: all 0.2s;
  position: relative;
  overflow: hidden;
}

.nav-item:hover {
  background-color: #f3f4f6;
}

.dark .nav-item:hover {
  background-color: #1f2937;
}

.nav-active {
  background-color: #eff6ff;
  color: #2563eb;
}

.dark .nav-active {
  background-color: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
}

.nav-icon {
  flex-shrink: 0;
  width: 1.25rem;
  height: 1.25rem;
  color: #6b7280;
  transition: color 0.2s;
}

.nav-active .nav-icon {
  color: #2563eb;
}

.dark .nav-active .nav-icon {
  color: #60a5fa;
}

.nav-item:hover .nav-icon {
  color: #374151;
}

.dark .nav-item:hover .nav-icon {
  color: #d1d5db;
}

.nav-text {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  transition: color 0.2s;
}

.dark .nav-text {
  color: #d1d5db;
}

.nav-active .nav-text {
  color: #2563eb;
  font-weight: 600;
}

.dark .nav-active .nav-text {
  color: #60a5fa;
}

.nav-item:hover .nav-text {
  color: #111827;
}

.dark .nav-item:hover .nav-text {
  color: #f9fafb;
}

/* Active indicator glow effect */
.nav-active::before {
  content: '';
  position: absolute;
  inset: 0;
  background-color: rgba(59, 130, 246, 0.05);
}

.dark .nav-active::before {
  background-color: rgba(59, 130, 246, 0.1);
}
</style>
