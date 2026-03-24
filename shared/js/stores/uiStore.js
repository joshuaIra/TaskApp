import { defineStore } from 'pinia';
import { ref } from 'vue';

// ====================
// Dark Mode Class
// ====================
const DARK_CLASS = 'app-dark';

// ====================
// SSR Safety Helpers
// ====================
const isBrowser = typeof window !== 'undefined';
const isDocument = typeof document !== 'undefined';

export const useUIStore = defineStore('ui', () => {
  // ====================
  // State
  // ====================
  const sidebarOpen = ref(isBrowser ? window.innerWidth >= 1024 : true);
  const darkMode = ref(false);
  const notifications = ref([]);

  // ====================
  // Initialize Dark Mode
  // ====================
  const initDarkMode = () => {
    if (isDocument) {
      document.documentElement.classList.remove('dark');
      document.documentElement.classList.remove(DARK_CLASS);
    }
  };

  // Initialize on store creation (client-side only)
  initDarkMode();

  // ====================
  // Actions
  // ====================
  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
  };

  const setSidebarOpen = (open) => {
    sidebarOpen.value = open;
  };

  const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;

    if (isDocument) {
      document.documentElement.classList.remove(DARK_CLASS);
      if (darkMode.value) {
        document.documentElement.classList.add(DARK_CLASS);
      }
    }
  };

  const setDarkMode = (value) => {
    darkMode.value = value;

    if (isDocument) {
      document.documentElement.classList.remove(DARK_CLASS);
      if (value) {
        document.documentElement.classList.add(DARK_CLASS);
      }
    }
  };

  const addNotification = (notification) => {
    const id = Date.now();
    const notif = { id, ...notification };
    notifications.value.push(notif);

    // Configurable duration (default 5 seconds for better UX)
    const duration = Number(notification?.duration ?? 5000);

    setTimeout(() => {
      removeNotification(id);
    }, duration);

    return id;
  };

  const removeNotification = (id) => {
    notifications.value = notifications.value.filter(n => n.id !== id);
  };

  const clearNotifications = () => {
    notifications.value = [];
  };

  // ====================
  // Return Public API
  // ====================
  return {
    // State
    sidebarOpen,
    darkMode,
    notifications,
    // Actions
    toggleSidebar,
    setSidebarOpen,
    toggleDarkMode,
    setDarkMode,
    addNotification,
    removeNotification,
    clearNotifications,
  };
});

