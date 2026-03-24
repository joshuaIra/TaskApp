<template>
  <header 
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
      'bg-white/95 dark:bg-gray-900/95 border-b border-slate-200 dark:border-gray-800'
    ]"
  >
    <div class="w-full px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-14">
        <!-- Left section: Menu & Logo -->
        <div class="flex items-center space-x-2 sm:space-x-4 min-w-0">
          <button
            @click="toggleSidebar"
            class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/20 transition-all duration-200 active:scale-95"
          >
            <svg class="w-6 h-6 text-slate-700 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <router-link :to="dashboardRoute" class="text-xl sm:text-2xl font-bold flex items-center space-x-2 sm:space-x-3 group min-w-0">
            <div class="w-10 h-10 bg-blue-100 dark:bg-white/20 rounded-xl flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-white/30 transition-all duration-300">
              <svg class="w-6 h-6 text-blue-700 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3a2 2 0 012-2h6a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V3z" />
              </svg>
            </div>
            <span class="text-slate-900 dark:text-white font-bold truncate">TaskApp</span>
          </router-link>
        </div>

        <!-- Center: Global Search -->
        <div class="hidden md:flex flex-1 max-w-xl mx-8">
          <div class="relative w-full">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search tasks..."
              class="w-full pl-12 pr-4 py-2.5 bg-slate-100 dark:bg-white/20 backdrop-blur-sm border border-slate-300 dark:border-white/30 rounded-xl text-slate-800 dark:text-white placeholder-slate-500 dark:placeholder-white/70 focus:bg-white dark:focus:bg-white/30 focus:border-slate-400 dark:focus:border-white/50 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:focus:ring-white/30 transition-all duration-300"
              @focus="searchFocused = true"
              @blur="searchFocused = false"
            >
            <svg class="absolute left-4 top-3 w-5 h-5 text-slate-500 dark:text-white/70" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89.56l4.732 4.732a1 1 0 01-1.414 1.414l-4.732-4.732A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
            <div v-if="searchQuery" class="absolute right-3 top-3">
              <button @click="searchQuery = ''" class="text-slate-500 dark:text-white/70 hover:text-slate-700 dark:hover:text-white transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
            <!-- Search results dropdown -->
            <transition name="fade">
              <div v-if="searchQuery && searchFocused" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden z-50">
                <div v-if="searchResults.length" class="max-h-64 overflow-y-auto">
                  <router-link
                    v-for="task in searchResults"
                    :key="task.id"
                    :to="{ name: 'TaskDetail', params: { id: task.id }}"
                    class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                  >
                    <p class="font-medium text-gray-900 dark:text-white">{{ task.title }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ task.description }}</p>
                  </router-link>
                </div>
                <div v-else class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                  No tasks found
                </div>
              </div>
            </transition>
          </div>
        </div>

        <!-- Center & Right section: Links & Actions -->
        <div class="hidden lg:flex items-center space-x-6">
          <router-link :to="dashboardRoute" class="text-slate-700 dark:text-white hover:underline">Home</router-link>
          <router-link to="/tasks" class="text-slate-700 dark:text-white hover:underline">{{ tasksNavLabel }}</router-link>
          <router-link v-if="canViewReports" to="/reports" class="text-slate-700 dark:text-white hover:underline">Reports</router-link>
          <router-link v-if="canViewSettings" to="/settings" class="text-slate-700 dark:text-white hover:underline">Settings</router-link>
        </div>
        <div class="flex items-center space-x-2 sm:space-x-3">
          <a
            v-if="!isAuthenticated"
            :href="loginUrl"
            class="hidden lg:inline-flex px-3 py-2 rounded-lg text-slate-700 dark:text-white/95 hover:bg-slate-100 dark:hover:bg-white/20 transition-all duration-200"
          >
            Login
          </a>

          <!-- Mobile search button -->
          <button
            @click="mobileSearchOpen = true"
            class="md:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/20 transition"
          >
            <svg class="w-5 h-5 text-slate-700 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89.56l4.732 4.732a1 1 0 01-1.414 1.414l-4.732-4.732A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </button>

          <!-- Dark mode toggle -->
          <button
            @click="toggleDarkMode"
            class="hidden md:inline-flex p-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/20 transition-all duration-200 active:scale-95"
          >
            <svg v-if="!uiStore.darkMode" class="w-5 h-5 text-slate-700 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
            <svg v-else class="w-5 h-5 text-slate-700 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l-2.12-2.12a1 1 0 00-1.414 1.414l2.12 2.12a1 1 0 001.414-1.414zM2.05 6.464l2.12 2.12a1 1 0 101.414-1.414L3.464 5.05a1 1 0 00-1.414 1.414z" clip-rule="evenodd" />
            </svg>
          </button>

          <!-- Notifications -->
          <div v-if="isAuthenticated" class="relative hidden md:block">
            <button
              @click="notificationsOpen = !notificationsOpen"
              class="p-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-white/20 transition-all duration-200 active:scale-95 relative"
            >
              <svg class="w-5 h-5 text-slate-700 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
              </svg>
              <!-- Notification badge -->
              <span v-if="notificationCount > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                {{ notificationCount > 9 ? '9+' : notificationCount }}
              </span>
            </button>

            <!-- Notifications dropdown -->
            <transition name="fade">
              <div v-if="notificationsOpen" class="absolute right-0 mt-3 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                  <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                </div>
                <div class="max-h-64 overflow-y-auto">
                  <div v-for="notif in mockNotifications" :key="notif.id" class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div class="flex items-start space-x-3">
                      <div :class="['w-2 h-2 rounded-full mt-2', notif.type === 'success' ? 'bg-green-500' : notif.type === 'error' ? 'bg-red-500' : 'bg-blue-500']"></div>
                      <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">{{ notif.message }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ notif.time }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700">
                  <button class="text-sm text-blue-600 dark:text-blue-400 font-medium hover:underline">View all notifications</button>
                </div>
              </div>
            </transition>
          </div>

          <!-- User menu -->
          <div v-if="isAuthenticated" class="relative">
            <button
              @click="userMenuOpen = !userMenuOpen"
              class="flex items-center space-x-2 sm:space-x-3 pl-2 md:pl-4 md:border-l border-slate-300 dark:border-white/30 hover:bg-slate-100 dark:hover:bg-white/20 transition-all duration-200 rounded-lg pr-2 py-1"
            >
              <img 
                :src="avatarUrl"
                alt="User" 
                class="w-8 h-8 rounded-full ring-2 ring-slate-300 dark:ring-white/30"
              >
              <span class="hidden md:inline text-slate-700 dark:text-white text-sm font-medium">{{ currentUser.name }}</span>
              <svg class="hidden md:block w-4 h-4 text-slate-500 dark:text-white/70" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>

            <!-- User dropdown -->
            <transition name="fade">
              <div v-if="userMenuOpen" class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                  <p class="font-semibold text-gray-900 dark:text-white">{{ currentUser.name }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ currentUser.email }}</p>
                </div>
                <div class="py-2">
                  <a :href="profileUrl" class="flex items-center space-x-3 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition" @click="userMenuOpen = false">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <span>Profile</span>
                  </a>
                  <router-link v-if="canViewSettings" to="/settings" class="flex items-center space-x-3 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition" @click="userMenuOpen = false">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    <span>Settings</span>
                  </router-link>
                </div>
                <div class="py-2 border-t border-gray-200 dark:border-gray-700">
                  <button class="flex items-center space-x-3 px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition w-full" @click="signOut">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h14a1 1 0 001-1V4a1 1 0 00-1-1H3zm14 12H3V5h14v10zM9 8a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1zm-3 5a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Sign out</span>
                  </button>
                </div>
              </div>
            </transition>
          </div>
          <div v-else class="hidden md:flex items-center space-x-2 sm:space-x-3">
            <a :href="loginUrl" class="px-3 py-2 rounded-lg text-slate-700 dark:text-white/95 hover:bg-slate-100 dark:hover:bg-white/20 transition-all duration-200">
              Login
            </a>
            <a :href="registerUrl" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-all duration-200">
              Sign up
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile search overlay -->
    <transition name="fade">
      <div v-if="mobileSearchOpen && !isDesktop" class="fixed inset-0 bg-black/50 z-50" @click="mobileSearchOpen = false">
        <div class="p-4" @click.stop>
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search tasks..."
              class="w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-800 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              autofocus
            >
            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89.56l4.732 4.732a1 1 0 01-1.414 1.414l-4.732-4.732A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
            <button @click="mobileSearchOpen = false" class="absolute right-4 top-3.5">
              <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useUIStore } from '../stores/uiStore';
import { useTaskStore } from '../stores/taskStore';

const uiStore = useUIStore();
const taskStore = useTaskStore();

const searchQuery = ref('');
const searchFocused = ref(false);
const userMenuOpen = ref(false);
const notificationsOpen = ref(false);
const mobileSearchOpen = ref(false);
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024);
const authState = window.TaskAppAuth ?? {
  isAuthenticated: false,
  user: null,
  loginUrl: '/login',
  registerUrl: '/register',
  profileUrl: '/profile',
  logoutUrl: '/logout',
};

// Mock notifications
const mockNotifications = ref([
  { id: 1, type: 'success', message: 'Task "Design homepage" completed', time: '2 min ago' },
  { id: 2, type: 'info', message: 'New comment on "API integration"', time: '1 hour ago' },
  { id: 3, type: 'error', message: 'Task "Fix login bug" is overdue', time: '3 hours ago' },
]);

const notificationCount = computed(() => mockNotifications.value.length);
const isAuthenticated = computed(() => Boolean(authState.isAuthenticated));
const currentUser = computed(() => authState.user ?? { name: 'Guest', email: '' });
const canViewReports = computed(() => ['admin', 'manager'].includes(authState?.user?.role));
const canViewSettings = computed(() => authState?.user?.role === 'admin');
const tasksNavLabel = computed(() => (authState?.user?.role === 'member' ? 'My Tasks' : 'Tasks'));
const dashboardRoute = computed(() => {
  const role = authState?.user?.role;
  if (role === 'admin') return '/dashboard/admin';
  if (role === 'manager') return '/dashboard/manager';
  return '/dashboard/member';
});
const loginUrl = authState.loginUrl || '/login';
const registerUrl = authState.registerUrl || '/register';
const profileUrl = authState.profileUrl || '/profile';
const logoutUrl = authState.logoutUrl || '/logout';
const avatarUrl = computed(() => `https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(currentUser.value.name || 'User')}`);

const isDesktop = computed(() => windowWidth.value >= 768);

const searchResults = computed(() => {
  if (!searchQuery.value) return [];
  return taskStore.tasks.filter(t => 
    t.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (t.description && t.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
  ).slice(0, 5);
});

const toggleSidebar = () => {
  uiStore.toggleSidebar();
};

const toggleDarkMode = () => {
  uiStore.toggleDarkMode();
};

const signOut = () => {
  userMenuOpen.value = false;
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = logoutUrl;

  const csrf = document.createElement('input');
  csrf.type = 'hidden';
  csrf.name = '_token';
  csrf.value = token;

  form.appendChild(csrf);
  document.body.appendChild(form);
  form.submit();
};

const handleWindowResize = () => {
  windowWidth.value = window.innerWidth;
  // Close mobile search if resizing to desktop
  if (windowWidth.value >= 768) {
    mobileSearchOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener('resize', handleWindowResize);
  // Ensure mobile search is closed on mount
  mobileSearchOpen.value = false;
});

onUnmounted(() => {
  window.removeEventListener('resize', handleWindowResize);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
