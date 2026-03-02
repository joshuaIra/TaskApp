<template>
  <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 dark:text-gray-400">
      <div class="mb-2 md:mb-0">
        © {{ new Date().getFullYear() }} TaskApp. All rights reserved.
      </div>
      <div class="flex space-x-4">
        <router-link to="/privacy" class="hover:underline">Privacy Policy</router-link>
        <router-link to="/terms" class="hover:underline">Terms of Service</router-link>
        <router-link to="/contact" class="hover:underline">Contact</router-link>
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>
