import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUIStore = defineStore('ui', () => {
  const sidebarOpen = ref(typeof window !== 'undefined' ? window.innerWidth >= 1024 : true);
  const darkMode = ref(false);
  const notifications = ref([]);

  if (typeof document !== 'undefined') {
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.remove('app-dark');
  }

  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
  };

  const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    if (typeof document !== 'undefined') {
      document.documentElement.classList.remove('dark');
      if (darkMode.value) {
        document.documentElement.classList.add('app-dark');
      } else {
        document.documentElement.classList.remove('app-dark');
      }
    }
  };

  const addNotification = (notification) => {
    const id = Date.now();
    const notif = { id, ...notification };
    notifications.value.push(notif);
    setTimeout(() => {
      removeNotification(id);
    }, 3000);
    return id;
  };

  const removeNotification = (id) => {
    notifications.value = notifications.value.filter(n => n.id !== id);
  };

  return {
    sidebarOpen,
    darkMode,
    notifications,
    toggleSidebar,
    toggleDarkMode,
    addNotification,
    removeNotification,
  };
});
