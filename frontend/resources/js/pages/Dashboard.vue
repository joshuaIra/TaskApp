<template>
  <div class="min-h-screen bg-gray-950 transition-colors duration-300">
    <div class="p-4 sm:p-8">
      <!-- Dashboard Header with Date Range -->
      <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-fadeInDown">
        <div>
          <h1 class="text-3xl font-bold text-white mb-1">Task Analytics</h1>
          <p class="text-gray-400 text-sm">Performance overview and task metrics</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2 bg-gray-800/50 backdrop-blur px-4 py-2 rounded-lg border border-gray-700">
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M6 2a1 1 0 000 2h8a1 1 0 100-2H6zM4 5a2 2 0 012-2 1 1 0 000 2h8a1 1 0 000-2 2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" />
            </svg>
            <span class="text-xs text-gray-300">Monthly</span>
          </div>
          <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
            Export
          </button>
        </div>
      </div>

      <!-- Main Grid Layout - 3 Column -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Left: Key Metrics Cards -->
        <div class="space-y-6 animate-fadeInUp" style="animation-delay: 100ms;">
          <!-- Total Tasks Card -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 hover:border-blue-500/50 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
              <div>
                <p class="text-gray-400 text-sm font-medium">Total Tasks</p>
                <p class="text-3xl font-bold text-white mt-2">{{ taskStore.taskCount }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a1 1 0 000-2H5a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V5a1 1 0 10-2 0v10a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <div class="flex items-center gap-1">
              <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
              <span class="text-xs text-green-400">+12% this month</span>
            </div>
          </div>

          <!-- In Progress Card -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
              <div>
                <p class="text-gray-400 text-sm font-medium">In Progress</p>
                <p class="text-3xl font-bold text-white mt-2">{{ taskStore.tasks.filter(t => t.status === 'in_progress').length }}</p>
              </div>
              <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <div class="w-full h-1 bg-gray-700 rounded-full overflow-hidden">
              <div class="h-full bg-gradient-to-r from-purple-500 to-purple-400" :style="{ width: inProgressPercent + '%' }"></div>
            </div>
          </div>

          <!-- Completed Card -->
          <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
              <div>
                <p class="text-gray-400 text-sm font-medium">Completed</p>
                <p class="text-3xl font-bold text-white mt-2">{{ completedCount }}</p>
              </div>
              <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <div class="text-xs text-green-400">{{ completedPercent }}% completion rate</div>
          </div>
        </div>

        <!-- Center: Main Chart -->
        <div class="lg:col-span-1 bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 animate-fadeInUp" style="animation-delay: 200ms;">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-white">Task Status</h3>
            <button class="text-gray-400 hover:text-gray-300">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.5 1.5H5.75A4.25 4.25 0 001.5 5.75v8.5A4.25 4.25 0 005.75 18.5h8.5a4.25 4.25 0 004.25-4.25v-4.75" />
              </svg>
            </button>
          </div>
          
          <div class="space-y-4">
            <div>
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-300">Pending</span>
                <span class="text-sm font-semibold text-gray-100">{{ taskStore.tasks.filter(t => t.status === 'pending').length }}</span>
              </div>
              <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-gray-400" :style="{ width: pendingPercent + '%' }"></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-300">In Progress</span>
                <span class="text-sm font-semibold text-gray-100">{{ taskStore.tasks.filter(t => t.status === 'in_progress').length }}</span>
              </div>
              <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-blue-400" :style="{ width: inProgressPercent + '%' }"></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-300">Completed</span>
                <span class="text-sm font-semibold text-gray-100">{{ completedCount }}</span>
              </div>
              <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-green-400" :style="{ width: completedPercent + '%' }"></div>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-6 border-t border-gray-700">
            <p class="text-xs text-gray-400 mb-3">Overall Progress</p>
            <div class="flex items-center gap-4">
              <div class="flex-1 h-8 bg-gray-700 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 transition-all duration-1000"
                  :style="{ width: overallProgress + '%' }"
                ></div>
              </div>
              <span class="text-lg font-bold text-white min-w-12">{{ overallProgress }}%</span>
            </div>
          </div>
        </div>

        <!-- Right: Priority Distribution -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 animate-fadeInUp" style="animation-delay: 300ms;">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-white">Priority Levels</h3>
            <button class="text-gray-400 hover:text-gray-300">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 11-4 0 2 2 0 014 0zM0 10a2 2 0 112 2H2a2 2 0 11-2-2zm12-2a2 2 0 100 4 2 2 0 000-4z" />
              </svg>
            </button>
          </div>

          <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                <span class="text-sm text-gray-300">High Priority</span>
              </div>
              <span class="text-sm font-semibold text-white">{{ taskStore.tasks.filter(t => t.priority === 'high').length }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                <span class="text-sm text-gray-300">Medium Priority</span>
              </div>
              <span class="text-sm font-semibold text-white">{{ taskStore.tasks.filter(t => t.priority === 'medium').length }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                <span class="text-sm text-gray-300">Low Priority</span>
              </div>
              <span class="text-sm font-semibold text-white">{{ taskStore.tasks.filter(t => t.priority === 'low').length }}</span>
            </div>
          </div>

          <div class="mt-6 pt-6 border-t border-gray-700">
            <router-link
              to="/tasks/create"
              class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              New Task
            </router-link>
          </div>
        </div>
      </div>

      <!-- Bottom Section - Recent Tasks -->
      <div class="grid grid-cols-1 gap-6 mb-8">
        <!-- Recent Tasks Table -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl border border-gray-700 overflow-hidden animate-fadeInUp" style="animation-delay: 400ms;">
          <div class="px-6 py-5 border-b border-gray-700 flex justify-between items-center">
            <div>
              <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a1 1 0 000-2H5a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V5a1 1 0 10-2 0v10a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" clip-rule="evenodd" />
                </svg>
                Recent Tasks
              </h3>
              <p class="text-xs text-gray-400 mt-1">Your latest 5 tasks</p>
            </div>
            <router-link
              to="/tasks"
              class="text-blue-400 hover:text-blue-300 text-sm font-medium flex items-center transition"
            >
              View All
              <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </router-link>
          </div>
          
          <div v-if="recentTasks.length" class="divide-y divide-gray-700">
            <div
              v-for="(task, index) in recentTasks.slice(0, 5)"
              :key="task.id"
              class="px-6 py-4 hover:bg-gray-700/30 transition-all duration-200 cursor-pointer group animate-slideInRight border-l-4 border-l-transparent hover:border-l-blue-400"
              :style="{ animationDelay: (400 + index * 100) + 'ms' }"
              @click="$router.push({name: 'TaskDetail', params: {id: task.id}})"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 flex-1">
                  <!-- Priority dot -->
                  <div :class="[
                    'w-2.5 h-2.5 rounded-full shrink-0',
                    task.priority === 'high' ? 'bg-red-500' : task.priority === 'medium' ? 'bg-yellow-500' : 'bg-green-500'
                  ]"></div>
                  
                  <div class="flex-1">
                    <h4 class="font-medium text-white group-hover:text-blue-300 transition">
                      {{ task.title }}
                    </h4>
                    <p class="text-xs text-gray-400 mt-0.5">{{ task.description ? task.description.substring(0, 50) : 'No description' }}</p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3 ml-4">
                  <span :class="[
                    'px-2.5 py-1 text-xs rounded font-medium whitespace-nowrap',
                    task.status === 'completed' ? 'bg-green-500/20 text-green-300' : '',
                    task.status === 'in_progress' ? 'bg-blue-500/20 text-blue-300' : '',
                    task.status === 'pending' ? 'bg-gray-600 text-gray-200' : '',
                  ]">
                    {{ formatStatus(task.status) }}
                  </span>
                  <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-400 transition-all group-hover:translate-x-1 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="p-12 text-center">
            <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h4 class="text-sm font-medium text-gray-300 mb-1">No tasks yet</h4>
            <p class="text-xs text-gray-500 mb-4">Create your first task to get started</p>
            <router-link
              to="/tasks/create"
              class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600/80 hover:bg-blue-600 text-white rounded-lg transition-colors text-sm font-medium"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              Create Task
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useTaskStore } from '../stores/taskStore';

const taskStore = useTaskStore();

onMounted(async () => {
  await taskStore.fetchAllTasks();
  await taskStore.fetchMyTasks();
});

const recentTasks = computed(() => {
  return taskStore.tasks.slice(0, 5);
});

const highPriorityCount = computed(() => {
  return taskStore.tasks.filter(t => t.priority === 'high').length;
});

const completedCount = computed(() => {
  return taskStore.tasks.filter(t => t.status === 'completed').length;
});

const overallProgress = computed(() => {
  if (taskStore.tasks.length === 0) return 0;
  const completed = taskStore.tasks.filter(t => t.status === 'completed').length;
  return Math.round((completed / taskStore.tasks.length) * 100);
});

const pendingPercent = computed(() => {
  if (taskStore.tasks.length === 0) return 0;
  return Math.round((taskStore.tasks.filter(t => t.status === 'pending').length / taskStore.tasks.length) * 100);
});

const inProgressPercent = computed(() => {
  if (taskStore.tasks.length === 0) return 0;
  return Math.round((taskStore.tasks.filter(t => t.status === 'in_progress').length / taskStore.tasks.length) * 100);
});

const completedPercent = computed(() => {
  if (taskStore.tasks.length === 0) return 0;
  return Math.round((taskStore.tasks.filter(t => t.status === 'completed').length / taskStore.tasks.length) * 100);
});

const formatStatus = (status) => {
  const statusMap = {
    pending: 'Pending',
    in_progress: 'In Progress',
    completed: 'Completed',
  };
  return statusMap[status] || status;
};
</script>
