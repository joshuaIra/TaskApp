<template>
  <div class="min-h-screen bg-white dark:bg-gray-950 transition-colors duration-300 flex flex-col">
    <Header />

    <div class="flex flex-1 pt-14">
      <Sidebar />

      <div
        :class="[
          'flex-1 w-full flex flex-col transition-all duration-300',
          uiStore.sidebarOpen ? 'lg:ml-64 xl:ml-72' : 'lg:ml-0',
        ]"
      >
        <main class="flex-1 w-full">
          <router-view v-slot="{ Component }">
            <transition name="page" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </main>

        <Footer />
      </div>
    </div>

    <NotificationCenter />
  </div>
</template>

<script setup>
import Header from './components/Header.vue';
import Sidebar from './components/Sidebar.vue';
import NotificationCenter from './components/NotificationCenter.vue';
import Footer from './components/Footer.vue';
import { useUIStore } from './stores/uiStore';

const uiStore = useUIStore();
</script>

<style>
/* Page transition styles */
.page-enter-active,
.page-leave-active {
  transition: all 0.3s ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.page-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
