<template>
  <div 
    class="bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden group cursor-pointer relative card-hover"
    @click="goToTaskDetail"
  >
    <!-- Priority color bar -->
    <div :class="[
      'priority-bar',
      task.priority === 'high' ? 'priority-bar-high' : task.priority === 'medium' ? 'priority-bar-medium' : 'priority-bar-low'
    ]"></div>

    <div class="p-6 pl-8">
      <!-- Priority badge & Actions -->
      <div class="flex justify-between items-start mb-3">
        <span :class="[
          'badge',
          task.priority === 'high' ? 'badge-high' : task.priority === 'medium' ? 'badge-medium' : 'badge-low',
        ]">
          <span class="flex items-center">
            <svg v-if="task.priority === 'high'" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z" clip-rule="evenodd" />
            </svg>
            {{ task.priority || 'Medium' }}
          </span>
        </span>
        <button
          @click.stop="toggleStar"
          class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-transform duration-200 active:scale-95"
        >
          <svg v-if="isFavorite" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L4.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
        </button>
      </div>

      <!-- Title -->
      <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
        {{ task.title }}
      </h3>

      <!-- Description -->
      <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">
        {{ task.description || 'No description' }}
      </p>

      <!-- Status & Assignment -->
      <div class="flex items-center justify-between mb-4">
        <span :class="[
          'px-2.5 py-1 text-xs rounded-lg font-medium flex items-center',
          task.status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : '',
          task.status === 'in_progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : '',
          task.status === 'pending' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : '',
        ]">
          <span :class="[
            'status-dot mr-1.5',
            task.status === 'completed' ? 'status-dot-completed' : task.status === 'in_progress' ? 'status-dot-in-progress' : 'status-dot-pending'
          ]"></span>
          {{ formatStatus(task.status) }}
        </span>
        <div v-if="task.assigned_to" class="flex -space-x-2">
            <img
              :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${task.assigned_to}`"
              :alt="task.assigned_to"
              class="w-7 h-7 rounded-full border-2 border-white dark:border-gray-800 ring-2 ring-transparent"
            >
        </div>
      </div>

      <!-- Tags -->
      <div v-if="task.tags && task.tags.length" class="flex flex-wrap gap-2 mb-4">
        <span
          v-for="tag in task.tags.slice(0, 3)"
          :key="tag"
          class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs rounded-full font-medium"
        >
          #{{ tag }}
        </span>
        <span v-if="task.tags.length > 3" class="text-xs text-gray-500 dark:text-gray-400 py-1">
          +{{ task.tags.length - 3 }} more
        </span>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">
        <div class="flex items-center space-x-4">
          <span class="flex items-center hover:text-blue-500 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
            {{ formatDate(task.due_date) }}
          </span>
        </div>
        <div class="flex items-center space-x-3">
          <span class="flex items-center hover:text-blue-500 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
            </svg>
            {{ task.comments_count || 0 }}
          </span>
          <span class="flex items-center hover:text-blue-500 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
            </svg>
            {{ task.attachments_count || 0 }}
          </span>
        </div>
      </div>
    </div>

    <!-- Hover overlay effect -->
    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/0 to-blue-500/0 group-hover:from-blue-500/5 group-hover:via-blue-500/5 group-hover:to-blue-500/5 transition-all duration-300 pointer-events-none rounded-2xl"></div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['star-toggle']);

const router = useRouter();
const isFavorite = ref(false);

const goToTaskDetail = () => {
  router.push({ name: 'TaskDetail', params: { id: props.task.id } });
};

const toggleStar = () => {
  isFavorite.value = !isFavorite.value;
  emit('star-toggle', props.task.id);
};

const formatStatus = (status) => {
  const statusMap = {
    pending: 'Pending',
    in_progress: 'In Progress',
    completed: 'Completed',
  };
  return statusMap[status] || status;
};

const formatDate = (date) => {
  if (!date) return 'No date';
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-hover {
  transform: translateY(0);
}

.card-hover:hover {
  transform: translateY(-6px);
}
</style>
