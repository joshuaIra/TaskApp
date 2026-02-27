<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 transition-colors duration-300">
    <div class="w-full p-4 sm:p-8 pt-6">
      <!-- Header with Filter & Sort -->
      <div class="mb-8 animate-fadeInDown">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
          <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">All Tasks</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ filteredTasks.length }} tasks found</p>
          </div>
          <div class="flex items-center space-x-3">
            <!-- View Toggle -->
            <div class="flex bg-gray-100 dark:bg-gray-800 rounded-xl p-1">
              <button
                @click="viewMode = 'grid'"
                :class="['view-toggle-btn', viewMode === 'grid' ? 'active' : '']"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M5 3a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V3z" />
                  <path d="M5 11a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6z" />
                </svg>
              </button>
              <button
                @click="viewMode = 'list'"
                :class="['view-toggle-btn', viewMode === 'list' ? 'active' : '']"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
            
            <router-link
              to="/tasks/create"
              class="btn-primary flex items-center"
            >
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              New Task
            </router-link>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="space-y-4">
          <!-- Search -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search tasks by title or description..."
              class="input-field pl-12"
            >
            <svg class="absolute left-4 top-4 w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89.56l4.732 4.732a1 1 0 01-1.414 1.414l-4.732-4.732A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>

          <!-- Filter Chips -->
          <div class="flex flex-wrap gap-3">
            <!-- Status Filters -->
            <button
              v-for="status in ['All', 'pending', 'in_progress', 'completed']"
              :key="status"
              @click="filterStatus = status === 'All' ? '' : status"
              :class="['filter-chip', (status === 'All' && !filterStatus) || filterStatus === status ? 'active' : '']"
            >
              <span v-if="status === 'All'" class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />
                </svg>
                All
              </span>
              <span v-else class="flex items-center">
                <span :class="[
                  'w-2 h-2 rounded-full mr-1.5',
                  status === 'pending' ? 'bg-gray-400' : status === 'in_progress' ? 'bg-blue-500' : 'bg-green-500'
                ]"></span>
                {{ formatStatus(status) }}
              </span>
            </button>

            <div class="w-px h-8 bg-gray-300 dark:bg-gray-600 mx-2"></div>

            <!-- Priority Filters -->
            <button
              v-for="priority in ['All', 'high', 'medium', 'low']"
              :key="priority"
              @click="filterPriority = priority === 'All' ? '' : priority"
              :class="['filter-chip', (priority === 'All' && !filterPriority) || filterPriority === priority ? 'active' : '']"
            >
              <span v-if="priority === 'All'" class="flex items-center">All Priority</span>
              <span v-else class="flex items-center">
                <span :class="[
                  'w-2 h-2 rounded-full mr-1.5',
                  priority === 'high' ? 'bg-red-500' : priority === 'medium' ? 'bg-yellow-500' : 'bg-green-500'
                ]"></span>
                {{ priority.charAt(0).toUpperCase() + priority.slice(1) }}
              </span>
            </button>

            <div class="w-px h-8 bg-gray-300 dark:bg-gray-600 mx-2"></div>

            <!-- Sort -->
            <select
              v-model="sortBy"
              class="filter-chip appearance-none pr-8"
            >
              <option value="recent">Sort: Recent</option>
              <option value="due_date">Sort: Due Date</option>
              <option value="priority">Sort: Priority</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="taskStore.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="bg-white dark:bg-gray-800 rounded-2xl p-6">
          <div class="skeleton h-4 w-20 mb-4"></div>
          <div class="skeleton h-6 w-full mb-2"></div>
          <div class="skeleton h-4 w-3/4 mb-4"></div>
          <div class="flex justify-between">
            <div class="skeleton h-6 w-16"></div>
            <div class="skeleton h-6 w-16"></div>
          </div>
        </div>
      </div>

      <!-- Tasks Grid/List -->
      <div v-else-if="filteredTasks.length">
        <div 
          :class="[
            viewMode === 'grid' 
              ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' 
              : 'space-y-4'
          ]"
        >
          <TaskCard
            v-for="(task, index) in filteredTasks"
            :key="task.id"
            :task="task"
            :class="viewMode === 'list' ? 'flex-row' : ''"
            class="animate-fadeInUp"
            :style="{ animationDelay: (index * 50) + 'ms' }"
            @star-toggle="handleStarToggle"
          />
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16 animate-fadeInUp">
        <div class="relative inline-block">
          <div class="w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <!-- Floating elements -->
          <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center animate-bounce-subtle">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
          </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No tasks found</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
          {{ searchQuery || filterStatus || filterPriority 
            ? 'Try adjusting your filters or search query to find what you\'re looking for.' 
            : 'Start by creating your first task and organize your work efficiently.' }}
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <button
            v-if="searchQuery || filterStatus || filterPriority"
            @click="clearFilters"
            class="btn-secondary"
          >
            Clear Filters
          </button>
          <router-link
            to="/tasks/create"
            class="btn-primary"
          >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Create First Task
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useTaskStore } from '../stores/taskStore';
import TaskCard from '../components/TaskCard.vue';

const taskStore = useTaskStore();

const searchQuery = ref('');
const filterStatus = ref('');
const filterPriority = ref('');
const sortBy = ref('recent');
const viewMode = ref('grid');

onMounted(async () => {
  await taskStore.fetchAllTasks();
});

const filteredTasks = computed(() => {
  let tasks = [...taskStore.tasks];

  // Search filter
  if (searchQuery.value) {
    tasks = tasks.filter(t =>
      t.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (t.description && t.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
    );
  }

  // Status filter
  if (filterStatus.value) {
    tasks = tasks.filter(t => t.status === filterStatus.value);
  }

  // Priority filter
  if (filterPriority.value) {
    tasks = tasks.filter(t => t.priority === filterPriority.value);
  }

  // Sorting
  if (sortBy.value === 'priority') {
    const priorityOrder = { high: 1, medium: 2, low: 3 };
    tasks = tasks.sort((a, b) => (priorityOrder[a.priority] || 4) - (priorityOrder[b.priority] || 4));
  } else if (sortBy.value === 'due_date') {
    tasks = tasks.sort((a, b) => new Date(a.due_date || '9999') - new Date(b.due_date || '9999'));
  } else {
    // Recent - sort by ID descending
    tasks = tasks.sort((a, b) => b.id - a.id);
  }

  return tasks;
});

const formatStatus = (status) => {
  const statusMap = {
    pending: 'Pending',
    in_progress: 'In Progress',
    completed: 'Completed',
  };
  return statusMap[status] || status;
};

const clearFilters = () => {
  searchQuery.value = '';
  filterStatus.value = '';
  filterPriority.value = '';
};

const handleStarToggle = (taskId) => {
  console.log('Task starred:', taskId);
};
</script>

<style scoped>
.view-toggle-btn {
  padding: 0.5rem;
  border-radius: 0.5rem;
  transition: all 0.2s;
  background: transparent;
  border: none;
  cursor: pointer;
}

.view-toggle-btn.active {
  background-color: #dbeafe;
  color: #2563eb;
}

.dark .view-toggle-btn.active {
  background-color: #1e3a8a;
  color: #60a5fa;
}

.filter-chip {
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  background-color: #f3f4f6;
  color: #4b5563;
  border: none;
}

.dark .filter-chip {
  background-color: #374151;
  color: #d1d5db;
}

.filter-chip:hover {
  background-color: #e5e7eb;
}

.dark .filter-chip:hover {
  background-color: #4b5563;
}

.filter-chip.active {
  background-color: #3b82f6;
  color: white;
}

.input-field {
  width: 100%;
  padding: 0.75rem 3rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  background-color: white;
  color: #111827;
  transition: all 0.3s;
  outline: none;
}

.input-field:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.dark .input-field {
  background-color: #374151;
  border-color: #4b5563;
  color: white;
}

.btn-primary {
  background: linear-gradient(to right, #3b82f6, #2563eb);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  font-weight: 500;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  border: none;
  cursor: pointer;
}

.btn-primary:hover {
  background: linear-gradient(to right, #2563eb, #1d4ed8);
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
  transform: translateY(-2px);
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #1f2937;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  font-weight: 500;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  border: none;
  cursor: pointer;
}

.dark .btn-secondary {
  background-color: #374151;
  color: #e5e7eb;
}

.btn-secondary:hover {
  background-color: #d1d5db;
  transform: translateY(-2px);
}

.dark .btn-secondary:hover {
  background-color: #4b5563;
}
</style>
