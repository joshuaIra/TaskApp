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
            
            <!-- Dashboard -->
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

            <!-- All Tasks -->
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

            <!-- Board (NEW) -->
            <router-link
              to="/board"
              class="nav-item group"
              :class="{ 'nav-active': $route.path === '/board' }"
            >
              <div class="absolute left-0 w-1 h-12 bg-blue-500 rounded-r-full transition-all duration-300"
                :class="$route.path === '/board' ? 'opacity-100' : 'opacity-0'"
              ></div>
              <div class="nav-icon">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    fill-rule="evenodd"
                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H4zm8 0a2 2 0 00-2 2v5a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2z"
                    clip-rule="evenodd"
                  />
                  <path d="M10 12a2 2 0 012-2h2a2 2 0 012 2v3a2 2 0 01-2 2h-2a2 2 0 01-2-2v-3z"/>
                </svg>
              </div>
              <span class="nav-text">Board</span>
            </router-link>

            <!-- My Tasks -->
            <router-link
              to="/tasks?filter=my"
              class="nav-item group"
            >
              <div class="absolute left-0 w-1 h-12 bg-blue-500 rounded-r-full transition-all duration-300 opacity-0"></div>
              <div class="nav-icon">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0z"/>
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
              <div class="nav-icon text-blue-600 dark:text-blue-400">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
              </div>
              <span class="nav-text text-blue-600 dark:text-blue-400 font-medium">New Task</span>
            </router-link>
          </div>

        </nav>
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