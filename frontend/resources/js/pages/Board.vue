<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto p-4 sm:p-8 pt-6">
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Board</h1>
          <p class="text-gray-500 dark:text-gray-400 mt-1">Drag tasks between columns</p>
        </div>

        <div class="flex items-center gap-2">
          <router-link
            to="/tasks"
            class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white hover:opacity-90"
          >
            List view
          </router-link>

          <router-link
            to="/tasks/create"
            class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:opacity-90"
          >
            New task
          </router-link>
        </div>
      </div>

      <KanbanBoard />
    </div>
  </div>
</template>

<script setup>
import KanbanBoard from "../components/kanban/KanbanBoard.vue";
</script>