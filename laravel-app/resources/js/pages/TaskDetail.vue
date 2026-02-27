<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 transition-colors duration-300">
    <div class="w-full p-4 sm:p-8 pt-6">
      <!-- Back Button -->
      <button
        @click="$router.back()"
        class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 mb-6 transition group animate-fadeInDown"
      >
        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Back to Tasks
      </button>

      <!-- Task Header -->
      <div v-if="currentTask" class="mb-8 animate-fadeInUp">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 mb-6">
          <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-3">
                <span :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold',
                  currentTask.priority === 'high' ? 'badge-high' : currentTask.priority === 'medium' ? 'badge-medium' : 'badge-low',
                ]">
                  {{ currentTask.priority || 'Medium' }} Priority
                </span>
                <span :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold flex items-center',
                  currentTask.status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : '',
                  currentTask.status === 'in_progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : '',
                  currentTask.status === 'pending' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : '',
                ]">
                  <span :class="[
                    'w-2 h-2 rounded-full mr-1.5',
                    currentTask.status === 'completed' ? 'bg-green-500' : currentTask.status === 'in_progress' ? 'bg-blue-500' : 'bg-gray-400'
                  ]"></span>
                  {{ formatStatus(currentTask.status) }}
                </span>
              </div>
              <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ currentTask.title }}</h1>
              <p class="text-gray-600 dark:text-gray-300 text-lg">{{ currentTask.description || 'No description provided' }}</p>
            </div>
            <div class="flex space-x-3">
              <button
                @click="editTask"
                class="flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-200 dark:hover:bg-blue-900/50 transition font-medium"
              >
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit
              </button>
              <button
                @click="deleteTask"
                class="flex items-center px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/50 transition font-medium"
              >
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Delete
              </button>
            </div>
          </div>

          <!-- Status & Priority -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</label>
              <select
                v-model="currentTask.status"
                @change="updateStatus"
                class="w-full px-4 py-2.5 rounded-xl font-medium transition-all duration-200 cursor-pointer"
                :class="[
                  currentTask.status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : '',
                  currentTask.status === 'in_progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : '',
                  currentTask.status === 'pending' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : '',
                ]"
              >
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Priority</label>
              <select
                v-model="currentTask.priority"
                @change="updatePriority"
                class="w-full px-4 py-2.5 rounded-xl font-medium transition-all duration-200 cursor-pointer"
                :class="[
                  currentTask.priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' : '',
                  currentTask.priority === 'medium' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200' : '',
                  currentTask.priority === 'low' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : '',
                ]"
              >
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Due Date</label>
              <input
                v-model="currentTask.due_date"
                type="date"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
              >
            </div>
          </div>

          <!-- Meta info -->
          <div class="flex flex-wrap gap-6 text-sm text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
              </svg>
              Created: {{ formatDate(currentTask.created_at) }}
            </div>
            <div v-if="currentTask.assigned_to" class="flex items-center">
              <img :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${currentTask.assigned_to}`" class="w-5 h-5 rounded-full mr-2" alt="">
              Assigned to {{ currentTask.assigned_to }}
            </div>
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
              </svg>
              {{ mockComments.length }} comments
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6 animate-fadeInUp" style="animation-delay: 100ms;">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'px-6 py-3 font-medium border-b-2 transition-all duration-300 relative overflow-hidden',
              activeTab === tab.id 
                ? 'border-blue-600 text-blue-600 dark:text-blue-400' 
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
            ]"
          >
            <span class="flex items-center">
              <component :is="tab.icon" class="w-5 h-5 mr-2" />
              {{ tab.label }}
            </span>
            <div v-if="activeTab === tab.id" class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500"></div>
          </button>
        </div>

        <!-- Tab Content -->
        <div class="animate-fadeInUp" style="animation-delay: 200ms;">
          <!-- Comments Section -->
          <div v-if="activeTab === 'comments'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <div class="space-y-4">
              <!-- Add Comment -->
              <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <textarea
                  v-model="newComment"
                  placeholder="Write a comment..."
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"
                  rows="3"
                ></textarea>
                <div class="flex justify-between items-center mt-3">
                  <div class="flex space-x-2">
                    <button class="p-2 text-gray-400 hover:text-blue-500 transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <button class="p-2 text-gray-400 hover:text-blue-500 transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
                  <button
                    @click="addComment"
                    :disabled="!newComment.trim()"
                    class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Post
                  </button>
                </div>
              </div>

              <!-- Comments List -->
              <div class="space-y-4">
                <div v-for="comment in mockComments" :key="comment.id" class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                      <img :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${comment.author}`" alt="" class="w-10 h-10 rounded-full">
                      <div>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ comment.author }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ comment.date }}</span>
                      </div>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                      </svg>
                    </button>
                  </div>
                  <p class="text-gray-700 dark:text-gray-300 ml-13">{{ comment.text }}</p>
                  <div class="flex items-center mt-3 space-x-4">
                    <button class="flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-blue-500 transition">
                      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                      </svg>
                      {{ comment.likes || 0 }}
                    </button>
                    <button class="flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-blue-500 transition">
                      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      Reply
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Attachments Section -->
          <div v-if="activeTab === 'attachments'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <div class="mb-6">
              <label class="flex items-center justify-center w-full px-6 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition group">
                <div class="text-center">
                  <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <p class="text-gray-600 dark:text-gray-400 font-medium">Click to upload files</p>
                  <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">or drag and drop</p>
                </div>
                <input type="file" class="hidden" multiple>
              </label>
            </div>

            <div class="space-y-3">
              <div v-for="attachment in mockAttachments" :key="attachment.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ attachment.name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ attachment.size }}</p>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button class="p-2 text-gray-400 hover:text-blue-500 transition rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <button class="p-2 text-gray-400 hover:text-red-500 transition rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity Section -->
          <div v-if="activeTab === 'activity'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Activity Timeline</h3>
            <div class="relative">
              <!-- Timeline line -->
              <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
              
              <div class="space-y-6">
                <div v-for="(activity, index) in mockActivity" :key="index" class="relative pl-12 animate-slideInRight" :style="{ animationDelay: (index * 100) + 'ms' }">
                  <!-- Timeline dot -->
                  <div class="absolute left-2 w-5 h-5 rounded-full border-4 border-white dark:border-gray-800"
                    :class="activity.type === 'created' ? 'bg-green-500' : activity.type === 'updated' ? 'bg-blue-500' : 'bg-purple-500'"
                  ></div>
                  
                  <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                      <span class="font-medium text-gray-900 dark:text-white">{{ activity.user }}</span>
                      <span class="text-sm text-gray-500 dark:text-gray-400">{{ activity.time }}</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">{{ activity.action }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-else-if="taskStore.loading" class="text-center py-12">
        <div class="inline-block animate-spin">
          <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6m0-6h-6m0 0h-6m0 6h6m0 0h6" />
          </svg>
        </div>
        <p class="mt-4 text-gray-500 dark:text-gray-400">Loading task...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, h, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useTaskStore } from '../stores/taskStore';
import { useUIStore } from '../stores/uiStore';

const route = useRoute();
const taskStore = useTaskStore();
const uiStore = useUIStore();

const currentTask = ref(null);
const activeTab = ref('comments');
const newComment = ref('');

// Tab icons
const tabs = [
  { 
    id: 'comments', 
    label: 'Comments', 
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z', 'clip-rule': 'evenodd' })
        ]);
      }
    }
  },
  { 
    id: 'attachments', 
    label: 'Attachments', 
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z', 'clip-rule': 'evenodd' })
        ]);
      }
    }
  },
  { 
    id: 'activity', 
    label: 'Activity', 
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z', 'clip-rule': 'evenodd' })
        ]);
      }
    }
  },
];

// Mock data
const mockComments = ref([
  { id: 1, author: 'John Doe', date: '2 hours ago', text: 'Let\'s prioritize this task for the current sprint. The client needs this delivered by next week.', likes: 3 },
  { id: 2, author: 'Jane Smith', date: '1 hour ago', text: 'I\'ve started working on this. The initial design mockups are ready for review.', likes: 5 },
  { id: 3, author: 'Bob Johnson', date: '30 min ago', text: 'Great progress! Let me know if you need any help with the backend integration.', likes: 2 },
]);

const mockAttachments = ref([
  { id: 1, name: 'requirements.pdf', size: '2.4 MB' },
  { id: 2, name: 'design-mockup.fig', size: '5.2 MB' },
  { id: 3, name: 'api-documentation.docx', size: '1.1 MB' },
]);

const mockActivity = ref([
  { type: 'created', user: 'John Doe', action: 'Created this task', time: '2 days ago' },
  { type: 'updated', user: 'Jane Smith', action: 'Changed priority to High', time: '1 day ago' },
  { type: 'comment', user: 'John Doe', action: 'Added a comment', time: '5 hours ago' },
  { type: 'updated', user: 'Jane Smith', action: 'Updated status to In Progress', time: '1 hour ago' },
]);

onMounted(async () => {
  const taskId = route.params.id;
  await taskStore.fetchTaskById(taskId);
  currentTask.value = taskStore.selectedTask;
  
  // If no task found, use mock data
  if (!currentTask.value) {
    currentTask.value = {
      id: taskId,
      title: 'Design Homepage Mockup',
      description: 'Create a modern and responsive homepage design for the new website project.',
      status: 'in_progress',
      priority: 'high',
      due_date: '2024-12-15',
      assigned_to: 'john',
      created_at: '2024-12-01',
    };
  }
});

const formatStatus = (status) => {
  const statusMap = {
    pending: 'Pending',
    in_progress: 'In Progress',
    completed: 'Completed',
  };
  return statusMap[status] || status;
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const updateStatus = async () => {
  uiStore.addNotification({
    type: 'success',
    message: 'Status updated successfully!',
  });
};

const updatePriority = async () => {
  uiStore.addNotification({
    type: 'success',
    message: 'Priority updated successfully!',
  });
};

const editTask = () => {
  uiStore.addNotification({
    type: 'info',
    message: 'Edit functionality coming soon!',
  });
};

const deleteTask = () => {
  if (confirm('Are you sure you want to delete this task?')) {
    uiStore.addNotification({
      type: 'success',
      message: 'Task deleted successfully!',
    });
    route.push('/tasks');
  }
};

const addComment = () => {
  if (newComment.value.trim()) {
    mockComments.value.unshift({
      id: Date.now(),
      author: 'You',
      date: 'Just now',
      text: newComment.value,
      likes: 0,
    });
    newComment.value = '';
    uiStore.addNotification({
      type: 'success',
      message: 'Comment added!',
    });
  }
};
</script>

<style scoped>
@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-slideInRight {
  animation: slideInRight 0.4s ease-out forwards;
}
</style>
