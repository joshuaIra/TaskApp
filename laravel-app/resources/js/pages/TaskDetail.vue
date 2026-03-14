<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 transition-colors duration-300">
    <div class="w-full p-4 sm:p-8 pt-6">
      <button
        @click="$router.back()"
        class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 mb-6 transition group animate-fadeInDown"
      >
        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Back to Tasks
      </button>

      <div v-if="currentTask" class="mb-8 animate-fadeInUp">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 mb-6">
          <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-3">
                <span :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold',
                  currentTask.priority === 'high' ? 'badge-high' : currentTask.priority === 'medium' ? 'badge-medium' : 'badge-low',
                ]">
                  {{ currentTask.priority || 'medium' }} Priority
                </span>
                <span :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold flex items-center',
                  currentTask.status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : '',
                  currentTask.status === 'in_progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : '',
                  currentTask.status === 'pending' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : '',
                ]"
                >
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
            <div class="flex space-x-3" v-if="canManageTask">
              <button
                @click="openEditModal"
                class="flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-200 dark:hover:bg-blue-900/50 transition font-medium"
              >
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit
              </button>
              <button
                v-if="canDeleteTask"
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

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</label>
              <select
                v-model="currentTask.status"
                @change="updateStatus"
                :disabled="!canManageTask"
                class="w-full px-4 py-2.5 rounded-xl font-medium transition-all duration-200 cursor-pointer disabled:cursor-not-allowed disabled:opacity-70"
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
                :disabled="!canManageTask"
                class="w-full px-4 py-2.5 rounded-xl font-medium transition-all duration-200 cursor-pointer disabled:cursor-not-allowed disabled:opacity-70"
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
                v-model="dueAtInput"
                @change="updateDueDate"
                :disabled="!canManageTask"
                type="date"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition disabled:cursor-not-allowed disabled:opacity-70"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Progress</label>
              <div class="space-y-2">
                <input
                  v-model.number="progressDraft"
                  type="range"
                  min="0"
                  max="100"
                  step="1"
                  :disabled="!canUpdateProgress || progressSubmitting"
                  class="w-full"
                >
                <div class="flex items-center justify-between">
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ progressDraft }}%</span>
                  <button
                    v-if="canUpdateProgress"
                    type="button"
                    @click="saveProgress"
                    :disabled="progressSubmitting || progressDraft === Number(currentTask.progress_percentage || 0)"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ progressSubmitting ? 'Saving...' : 'Save' }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap gap-6 text-sm text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
              </svg>
              Created: {{ formatDate(currentTask.created_at) }}
            </div>
            <div v-if="assigneeNames" class="flex items-center">Assigned to {{ assigneeNames }}</div>
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
              </svg>
              {{ comments.length }} comments
            </div>
          </div>
        </div>

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

        <div class="animate-fadeInUp" style="animation-delay: 200ms;">
          <div v-if="activeTab === 'comments'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <div class="space-y-4">
              <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <textarea
                  v-model="newComment"
                  placeholder="Write a comment..."
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"
                  rows="3"
                ></textarea>
                <div class="flex justify-end items-center mt-3">
                  <button
                    @click="addComment"
                    :disabled="!newComment.trim() || commentSubmitting"
                    class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ commentSubmitting ? 'Posting...' : 'Post' }}
                  </button>
                </div>
              </div>

              <div class="space-y-4">
                <div v-for="comment in comments" :key="comment.id" class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                      <img :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${comment.user?.name || 'user'}`" alt="" class="w-10 h-10 rounded-full">
                      <div>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ comment.user?.name || 'Unknown' }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ formatDateTime(comment.created_at) }}</span>
                      </div>
                    </div>
                    <div v-if="canModifyComment(comment)" class="flex items-center gap-2 text-sm">
                      <button @click="startEditComment(comment)" class="text-blue-600 hover:text-blue-700">Edit</button>
                      <button @click="deleteComment(comment)" class="text-red-600 hover:text-red-700">Delete</button>
                    </div>
                  </div>
                  <div v-if="editingCommentId === comment.id" class="space-y-2">
                    <textarea
                      v-model="editingCommentBody"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    ></textarea>
                    <div class="flex items-center gap-2">
                      <button @click="saveComment(comment.id)" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg">Save</button>
                      <button @click="cancelEditComment" class="px-4 py-1.5 bg-gray-200 dark:bg-gray-600 rounded-lg">Cancel</button>
                    </div>
                  </div>
                  <p v-else class="text-gray-700 dark:text-gray-300">{{ comment.body }}</p>
                </div>
                <p v-if="!comments.length" class="text-gray-500 dark:text-gray-400">No comments yet.</p>
              </div>
            </div>
          </div>

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
                  <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Max file size: 10MB</p>
                </div>
                <input type="file" class="hidden" multiple @change="onFileSelected">
              </label>
            </div>

            <div class="space-y-3">
              <div v-for="attachment in attachments" :key="attachment.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ attachment.filename }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatFileSize(attachment.size) }}</p>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button @click="downloadAttachment(attachment)" class="p-2 text-gray-400 hover:text-blue-500 transition rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600" title="Download">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <button
                    v-if="canDeleteAttachment(attachment)"
                    @click="deleteAttachment(attachment)"
                    class="p-2 text-gray-400 hover:text-red-500 transition rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="Delete"
                  >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </div>
              <p v-if="!attachments.length" class="text-gray-500 dark:text-gray-400">No attachments yet.</p>
            </div>
          </div>

          <div v-if="activeTab === 'activity'" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Activity Timeline</h3>
            <div class="relative">
              <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

              <div class="space-y-6">
                <div v-for="(activity, index) in activityTimeline" :key="`${activity.type}-${index}`" class="relative pl-12 animate-slideInRight" :style="{ animationDelay: (index * 100) + 'ms' }">
                  <div class="absolute left-2 w-5 h-5 rounded-full border-4 border-white dark:border-gray-800"
                    :class="activity.type === 'created' ? 'bg-green-500' : activity.type === 'updated' ? 'bg-blue-500' : 'bg-purple-500'"
                  ></div>

                  <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                      <span class="font-medium text-gray-900 dark:text-white">{{ activity.user }}</span>
                      <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDateTime(activity.time) }}</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">{{ activity.action }}</p>
                  </div>
                </div>
                <p v-if="!activityTimeline.length" class="text-gray-500 dark:text-gray-400">No activity yet.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="taskStore.loading" class="text-center py-12">
        <div class="inline-block animate-spin">
          <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6m0-6h-6m0 0h-6m0 6h6m0 0h6" />
          </svg>
        </div>
        <p class="mt-4 text-gray-500 dark:text-gray-400">Loading task...</p>
      </div>

      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeEditModal"></div>
        <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-8">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Task</h3>
            <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Title</label>
              <input
                v-model="editForm.title"
                type="text"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</label>
              <textarea
                v-model="editForm.description"
                rows="4"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</label>
                <select
                  v-model="editForm.status"
                  class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                  <option value="pending">Pending</option>
                  <option value="in_progress">In Progress</option>
                  <option value="completed">Completed</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Priority</label>
                <select
                  v-model="editForm.priority"
                  class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Due Date</label>
                <input
                  v-model="editForm.due_at"
                  type="date"
                  class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assign Users</label>
              <div class="max-h-40 overflow-y-auto rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-2" :class="{ 'opacity-60': assigneesLoading }">
                <label
                  v-for="assignee in assigneeOptions"
                  :key="assignee.id"
                  class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                >
                  <input
                    type="checkbox"
                    :value="String(assignee.id)"
                    v-model="editForm.assignees"
                    :disabled="assigneesLoading"
                    class="h-4 w-4"
                  >
                  <span>{{ assignee.name }}</span>
                </label>
                <p v-if="!assigneesLoading && !assigneeOptions.length" class="px-2 py-2 text-xs text-gray-500 dark:text-gray-400">No members found.</p>
                <p v-if="assigneesLoading" class="px-2 py-2 text-xs text-gray-500 dark:text-gray-400">Loading members...</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end items-center gap-3 mt-6">
            <button
              @click="closeEditModal"
              class="px-5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl"
            >
              Cancel
            </button>
            <button
              @click="confirmTaskUpdate"
              :disabled="editSubmitting || !editForm.title.trim()"
              class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ editSubmitting ? 'Updating...' : 'Confirm Update' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, h, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTaskStore } from '../stores/taskStore';
import { useUIStore } from '../stores/uiStore';
import { attachmentService, commentService, taskService } from '../services/api';

const route = useRoute();
const router = useRouter();
const taskStore = useTaskStore();
const uiStore = useUIStore();

const currentTask = ref(null);
const activeTab = ref('comments');
const newComment = ref('');
const comments = ref([]);
const attachments = ref([]);
const dueAtInput = ref('');
const editingCommentId = ref(null);
const editingCommentBody = ref('');
const commentSubmitting = ref(false);
const showEditModal = ref(false);
const editSubmitting = ref(false);
const progressDraft = ref(0);
const progressSubmitting = ref(false);
const assigneeOptions = ref([]);
const assigneesLoading = ref(false);
const editForm = ref({
  title: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  due_at: '',
  assignees: [],
});

const authUser = computed(() => window.TaskAppAuth?.user || null);
const canManageTask = computed(() => ['admin', 'manager'].includes(authUser.value?.role || ''));
const canUpdateProgress = computed(() => {
  if (!currentTask.value || !authUser.value) return false;
  if (canManageTask.value) return true;

  if (authUser.value.role !== 'member') return false;

  return (currentTask.value.assignees || []).some(
    (assignee) => Number(assignee.id) === Number(authUser.value.id)
  );
});
const canDeleteTask = computed(() => {
  const role = authUser.value?.role;
  if (role === 'admin') return true;
  if (role === 'manager') return currentTask.value?.status === 'pending';
  return false;
});
const assigneeNames = computed(() => (currentTask.value?.assignees || []).map((user) => user.name).join(', '));

const tabs = [
  {
    id: 'comments',
    label: 'Comments',
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z', 'clip-rule': 'evenodd' }),
        ]);
      },
    },
  },
  {
    id: 'attachments',
    label: 'Attachments',
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z', 'clip-rule': 'evenodd' }),
        ]);
      },
    },
  },
  {
    id: 'activity',
    label: 'Activity',
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z', 'clip-rule': 'evenodd' }),
        ]);
      },
    },
  },
];

const activityTimeline = computed(() => {
  if (!currentTask.value) {
    return [];
  }

  const task = currentTask.value;
  const creatorName = task.creator?.name || 'System';

  const items = [
    {
      type: 'created',
      user: creatorName,
      action: 'Created this task',
      time: task.created_at,
    },
  ];

  if (task.updated_at && task.updated_at !== task.created_at) {
    items.push({
      type: 'updated',
      user: creatorName,
      action: 'Updated task details',
      time: task.updated_at,
    });
  }

  comments.value.forEach((comment) => {
    items.push({
      type: 'comment',
      user: comment.user?.name || 'User',
      action: 'Added a comment',
      time: comment.created_at,
    });
  });

  attachments.value.forEach((attachment) => {
    items.push({
      type: 'comment',
      user: attachment.user?.name || 'User',
      action: `Uploaded attachment: ${attachment.filename}`,
      time: attachment.created_at,
    });
  });

  return items.sort((a, b) => new Date(b.time) - new Date(a.time));
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

  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const formatDateTime = (date) => {
  if (!date) return 'N/A';

  return new Date(date).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  });
};

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B';

  const units = ['B', 'KB', 'MB', 'GB'];
  let size = bytes;
  let unitIndex = 0;

  while (size >= 1024 && unitIndex < units.length - 1) {
    size /= 1024;
    unitIndex += 1;
  }

  return `${size.toFixed(unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`;
};

const toDateInputValue = (dateValue) => {
  if (!dateValue) return '';

  const date = new Date(dateValue);
  if (Number.isNaN(date.getTime())) return '';

  return date.toISOString().slice(0, 10);
};

const buildTaskPayload = () => ({
  title: currentTask.value.title,
  description: currentTask.value.description || '',
  status: currentTask.value.status,
  priority: currentTask.value.priority,
  progress_percentage: Number(currentTask.value.progress_percentage || 0),
  due_at: dueAtInput.value || null,
  assignees: (currentTask.value.assignees || []).map((assignee) => assignee.id),
});

const applyTaskResponse = (updatedTask) => {
  currentTask.value = updatedTask;
  comments.value = Array.isArray(updatedTask.comments) ? updatedTask.comments : comments.value;
  attachments.value = Array.isArray(updatedTask.attachments) ? updatedTask.attachments : attachments.value;
  dueAtInput.value = toDateInputValue(updatedTask.due_at);
  progressDraft.value = Number(updatedTask.progress_percentage || 0);
};

const loadTask = async () => {
  await taskStore.fetchTaskById(route.params.id);

  if (!taskStore.selectedTask) {
    uiStore.addNotification({ type: 'error', message: 'Task not found.' });
    router.push('/tasks');
    return;
  }

  applyTaskResponse(taskStore.selectedTask);
};

const loadAssignees = async () => {
  if (!canManageTask.value) return;

  assigneesLoading.value = true;
  try {
    const response = await taskService.getAssignees();
    assigneeOptions.value = Array.isArray(response.data?.assignees) ? response.data.assignees : [];
  } catch {
    assigneeOptions.value = [];
    uiStore.addNotification({ type: 'error', message: 'Unable to load assignees.' });
  } finally {
    assigneesLoading.value = false;
  }
};

const updateTask = async (successMessage) => {
  if (!currentTask.value || !canManageTask.value) return;

  try {
    const updatedTask = await taskStore.updateTask(currentTask.value.id, buildTaskPayload());
    applyTaskResponse(updatedTask);
    uiStore.addNotification({ type: 'success', message: successMessage });
    return true;
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: error?.response?.data?.message || 'Failed to update task.',
    });
    return false;
  }
};

const updateStatus = async () => {
  await updateTask('Status updated successfully!');
};

const updatePriority = async () => {
  await updateTask('Priority updated successfully!');
};

const updateDueDate = async () => {
  await updateTask('Due date updated successfully!');
};

const saveProgress = async () => {
  if (!currentTask.value || !canUpdateProgress.value) return;

  progressSubmitting.value = true;
  try {
    const response = await taskService.updateProgress(currentTask.value.id, {
      progress_percentage: Number(progressDraft.value),
    });

    const updatedTask = response.data?.task;
    if (updatedTask) {
      applyTaskResponse(updatedTask);
    }

    uiStore.addNotification({
      type: 'success',
      message: response.data?.message || 'Progress updated successfully.',
    });
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: error?.response?.data?.message || 'Failed to update progress.',
    });
  } finally {
    progressSubmitting.value = false;
  }
};

const openEditModal = () => {
  if (!currentTask.value || !canManageTask.value) return;

  editForm.value = {
    title: currentTask.value.title || '',
    description: currentTask.value.description || '',
    status: currentTask.value.status || 'pending',
    priority: currentTask.value.priority || 'medium',
    due_at: dueAtInput.value || '',
    assignees: (currentTask.value.assignees || []).map((assignee) => String(assignee.id)),
  };

  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
};

const confirmTaskUpdate = async () => {
  if (!currentTask.value || !canManageTask.value) return;

  const trimmedTitle = editForm.value.title.trim();
  if (!trimmedTitle) return;

  editSubmitting.value = true;
  currentTask.value.title = trimmedTitle;
  currentTask.value.description = editForm.value.description;
  currentTask.value.status = editForm.value.status;
  currentTask.value.priority = editForm.value.priority;
  dueAtInput.value = editForm.value.due_at;
  currentTask.value.assignees = assigneeOptions.value.filter((assignee) =>
    editForm.value.assignees.includes(String(assignee.id))
  );

  const didUpdate = await updateTask('Task updated successfully!');

  if (didUpdate) {
    showEditModal.value = false;
  }

  editSubmitting.value = false;
};

const deleteTask = async () => {
  if (!currentTask.value || !canManageTask.value) return;

  if (!window.confirm('Are you sure you want to delete this task?')) return;

  try {
    await taskStore.deleteTask(currentTask.value.id);
    uiStore.addNotification({ type: 'success', message: 'Task deleted successfully!' });
    router.push('/tasks');
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: error?.response?.data?.message || 'Failed to delete task.',
    });
  }
};

const addComment = async () => {
  if (!newComment.value.trim() || !currentTask.value) return;

  commentSubmitting.value = true;
  try {
    const response = await commentService.create(currentTask.value.id, {
      body: newComment.value.trim(),
    });
    comments.value.unshift(response.data);
    newComment.value = '';
    uiStore.addNotification({ type: 'success', message: 'Comment added!' });
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: error?.response?.data?.message || 'Failed to add comment.',
    });
  } finally {
    commentSubmitting.value = false;
  }
};

const canModifyComment = (comment) => {
  if (!authUser.value) return false;
  if (canManageTask.value) return true;
  return Number(comment.user_id) === Number(authUser.value.id);
};

const startEditComment = (comment) => {
  editingCommentId.value = comment.id;
  editingCommentBody.value = comment.body;
};

const cancelEditComment = () => {
  editingCommentId.value = null;
  editingCommentBody.value = '';
};

const saveComment = async (commentId) => {
  if (!editingCommentBody.value.trim()) return;

  try {
    const response = await commentService.update(commentId, {
      body: editingCommentBody.value.trim(),
    });
    const index = comments.value.findIndex((comment) => comment.id === commentId);
    if (index !== -1) {
      comments.value[index] = response.data;
    }
    cancelEditComment();
    uiStore.addNotification({ type: 'success', message: 'Comment updated.' });
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: error?.response?.data?.message || 'Failed to update comment.',
    });
  }
};

const deleteComment = async (comment) => {
  if (!window.confirm('Delete this comment?')) return;

  try {
    await commentService.delete(comment.id);
    comments.value = comments.value.filter((entry) => entry.id !== comment.id);
    uiStore.addNotification({ type: 'success', message: 'Comment deleted.' });
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: error?.response?.data?.message || 'Failed to delete comment.',
    });
  }
};

const onFileSelected = async (event) => {
  if (!currentTask.value) return;

  const selectedFiles = Array.from(event.target.files || []);
  if (!selectedFiles.length) return;

  for (const file of selectedFiles) {
    const formData = new FormData();
    formData.append('file', file);

    try {
      const response = await attachmentService.upload(currentTask.value.id, formData);
      attachments.value.unshift(response.data);
      uiStore.addNotification({ type: 'success', message: `${file.name} uploaded.` });
    } catch (error) {
      uiStore.addNotification({
        type: 'error',
        message: error?.response?.data?.message || `Failed to upload ${file.name}.`,
      });
    }
  }

  event.target.value = '';
};

const getDownloadFileName = (attachment, contentDisposition) => {
  if (!contentDisposition) return attachment.filename;

  const match = /filename="?([^\";]+)"?/i.exec(contentDisposition);
  return match?.[1] || attachment.filename;
};

const downloadAttachment = async (attachment) => {
  try {
    const response = await attachmentService.download(attachment.id);
    const blob = new Blob([response.data]);
    const url = window.URL.createObjectURL(blob);
    const anchor = document.createElement('a');
    anchor.href = url;
    anchor.download = getDownloadFileName(attachment, response.headers?.['content-disposition']);
    document.body.appendChild(anchor);
    anchor.click();
    anchor.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: error?.response?.data?.message || 'Failed to download attachment.',
    });
  }
};

const canDeleteAttachment = (attachment) => {
  if (!authUser.value) return false;
  if (canManageTask.value) return true;
  return Number(attachment.user_id) === Number(authUser.value.id);
};

const deleteAttachment = async (attachment) => {
  if (!window.confirm('Delete this attachment?')) return;

  try {
    await attachmentService.delete(attachment.id);
    attachments.value = attachments.value.filter((entry) => entry.id !== attachment.id);
    uiStore.addNotification({ type: 'success', message: 'Attachment deleted.' });
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: error?.response?.data?.message || 'Failed to delete attachment.',
    });
  }
};

onMounted(async () => {
  await Promise.all([loadTask(), loadAssignees()]);
});
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
