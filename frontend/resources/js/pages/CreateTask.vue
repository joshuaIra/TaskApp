<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto p-4 sm:p-8 pt-6">
      <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8 animate-fadeInDown">
          <button
            @click="router.back()"
            class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 mb-6 transition group"
            type="button"
          >
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back
          </button>

          <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Create New Task</h1>
          <p class="text-gray-500 dark:text-gray-400 mt-2">Fill in the details below to create a new task</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 animate-fadeInUp">
          <form @submit.prevent="submitForm" class="space-y-6">
            <FloatingField
              v-model="form.title"
              id="title"
              label="SMM & Recommendations"
              :required="true"
              :error="errors.title"
            />

            <FloatingField
              v-model="form.description"
              as="textarea"
              id="description"
              label="Description (optional)"
              :rows="4"
            />

            <OptionPills
              v-model="form.priority"
              label="Priority Level"
              :options="priorities"
              :multiple="false"
              container-class="grid grid-cols-3 gap-3"
            />

            <FloatingField
              v-model="form.due_date"
              as="input"
              type="date"
              id="due_date"
              label="Due Date (optional)"
            />

            <FloatingField v-model="form.assigned_to" as="select" id="assigned_to" label="Assign To">
              <option value="">Unassigned</option>
              <option v-for="u in assignees" :key="u.value" :value="u.value">
                {{ u.label }}
              </option>
            </FloatingField>

            <OptionPills
              v-model="form.tags"
              label="Tags"
              :options="tagOptions"
              :multiple="true"
              container-class="flex flex-wrap gap-2"
              :base-btn="'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200'"
            >
              <template #icon="{ selected }">
                <svg v-if="selected" class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </template>

              <template #label="{ option }">
                <span class="flex items-center">#{{ option.label }}</span>
              </template>
            </OptionPills>

            <!-- Buttons -->
            <div class="flex gap-3 pt-6">
              <button
                type="submit"
                :disabled="isSubmitting"
                class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
              >
                <svg v-if="isSubmitting" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isSubmitting ? 'Creating...' : 'Create Task' }}
              </button>

              <button
                type="button"
                @click="router.back()"
                class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white px-6 py-3 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 font-medium"
              >
                Cancel
              </button>
            </div>

            <!-- Success Message -->
            <transition name="fade">
              <div v-if="successMessage" class="p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ successMessage }}
              </div>
            </transition>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useTaskStore } from '../stores/taskStore'
import { useUIStore } from '../stores/uiStore'

import FloatingField from '../components/FloatingField.vue'
import OptionPills from '../components/OptionPills.vue'

const router = useRouter()
const taskStore = useTaskStore()
const uiStore = useUIStore()

const form = reactive({
  title: '',
  description: '',
  priority: 'medium',
  due_date: '',
  assigned_to: '',
  tags: [],
})

const errors = reactive({})
const isSubmitting = ref(false)
const successMessage = ref('')

const assignees = [
  { value: 'john', label: 'John Doe' },
  { value: 'jane', label: 'Jane Smith' },
  { value: 'bob', label: 'Bob Johnson' },
]

const tagOptions = [
  'frontend', 'backend', 'design', 'urgent', 'bug', 'feature', 'documentation',
].map((t) => ({ value: t, label: t }))

// Priority icons kept simple (optional)
const LowIcon = {
  template: `<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>`,
}
const MediumIcon = {
  template: `<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 100 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>`,
}
const HighIcon = {
  template: `<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>`,
}

const priorities = [
  {
    value: 'low',
    label: 'Low',
    activeClass: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-2 border-green-500',
    icon: LowIcon,
  },
  {
    value: 'medium',
    label: 'Medium',
    activeClass: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 border-2 border-yellow-500',
    icon: MediumIcon,
  },
  {
    value: 'high',
    label: 'High',
    activeClass: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-2 border-red-500',
    icon: HighIcon,
  },
]

const notify = (type, message) => uiStore.addNotification({ type, message })

const validate = () => {
  for (const k of Object.keys(errors)) delete errors[k]
  if (!form.title.trim()) errors.title = 'Title is required'
  return Object.keys(errors).length === 0
} 

const submitForm = async () => {
  if (!validate()) return

  isSubmitting.value = true
  try {
    await taskStore.createTask({ ...form })
    successMessage.value = 'Task created successfully!'
    notify('success', 'Task created successfully!')
    setTimeout(() => router.push('/tasks'), 1500)
  } catch (e) {
    notify('error', 'Failed to create task. Please try again.')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>