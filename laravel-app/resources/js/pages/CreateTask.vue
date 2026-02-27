<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 transition-colors duration-300">
    <div class="w-full p-4 sm:p-8 pt-6">
      <div class="w-full xl:max-w-5xl">
        <!-- Header -->
        <div class="mb-8 animate-fadeInDown">
          <button
            @click="$router.back()"
            class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 mb-6 transition group"
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
            <!-- Title -->
            <div class="relative">
              <input
                v-model="form.title"
                type="text"
                placeholder=" "
                id="title"
                class="peer w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-300 pt-6 pb-2"
                :class="{ 'border-red-500 focus:ring-red-500': errors.title }"
                required
              >
              <label 
                for="title"
                class="absolute left-4 top-4 text-gray-500 dark:text-gray-400 transition-all duration-200 pointer-events-none
                  peer-placeholder-shown:text-base peer-placeholder-shown:top-4
                  peer-focus:-top-2 peer-focus:text-xs peer-focus:text-blue-500 peer-focus:bg-white peer-focus:dark:bg-gray-800 peer-focus:px-1
                  peer-[&:not(:placeholder-shown)]:-top-2 peer-[&:not(:placeholder-shown)]:text-xs peer-[&:not(:placeholder-shown)]:bg-white peer-[&:not(:placeholder-shown)]:dark:bg-gray-800 peer-[&:not(:placeholder-shown)]:px-1"
              >
                Task Title *
              </label>
              <span v-if="errors.title" class="text-red-500 text-sm mt-1 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ errors.title }}
              </span>
            </div>

            <!-- Description -->
            <div class="relative">
              <textarea
                v-model="form.description"
                placeholder=" "
                id="description"
                rows="4"
                class="peer w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-300 resize-none pt-6 pb-2"
              ></textarea>
              <label 
                for="description"
                class="absolute left-4 top-4 text-gray-500 dark:text-gray-400 transition-all duration-200 pointer-events-none
                  peer-placeholder-shown:text-base peer-placeholder-shown:top-4
                  peer-focus:-top-2 peer-focus:text-xs peer-focus:text-blue-500 peer-focus:bg-white peer-focus:dark:bg-gray-800 peer-focus:px-1
                  peer-[&:not(:placeholder-shown)]:-top-2 peer-[&:not(:placeholder-shown)]:text-xs peer-[&:not(:placeholder-shown)]:bg-white peer-[&:not(:placeholder-shown)]:dark:bg-gray-800 peer-[&:not(:placeholder-shown)]:px-1"
              >
                Description (optional)
              </label>
            </div>

            <!-- Priority -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Priority Level
              </label>
              <div class="grid grid-cols-3 gap-3">
                <button
                  v-for="priority in priorities"
                  :key="priority.value"
                  type="button"
                  @click="form.priority = priority.value"
                  :class="[
                    'p-4 rounded-xl font-medium transition-all duration-200 flex flex-col items-center space-y-2',
                    form.priority === priority.value
                      ? priority.activeClass
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                  ]"
                >
                  <component :is="priority.icon" class="w-5 h-5" />
                  {{ priority.label }}
                </button>
              </div>
            </div>

            <!-- Due Date -->
            <div class="relative">
              <input
                v-model="form.due_date"
                type="date"
                id="due_date"
                class="peer w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-300 pt-6 pb-2"
              >
              <label 
                for="due_date"
                class="absolute left-4 top-4 text-gray-500 dark:text-gray-400 transition-all duration-200 pointer-events-none
                  peer-placeholder-shown:text-base peer-placeholder-shown:top-4
                  peer-focus:-top-2 peer-focus:text-xs peer-focus:text-blue-500 peer-focus:bg-white peer-focus:dark:bg-gray-800 peer-focus:px-1
                  peer-[&:not(:placeholder-shown)]:-top-2 peer-[&:not(:placeholder-shown)]:text-xs peer-[&:not(:placeholder-shown)]:bg-white peer-[&:not(:placeholder-shown)]:dark:bg-gray-800 peer-[&:not(:placeholder-shown)]:px-1"
              >
                Due Date (optional)
              </label>
            </div>

            <!-- Assign To -->
            <div class="relative">
              <select
                v-model="form.assigned_to"
                id="assigned_to"
                class="peer w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-300 appearance-none pt-6 pb-2"
              >
                <option value="">Unassigned</option>
                <option value="john">John Doe</option>
                <option value="jane">Jane Smith</option>
                <option value="bob">Bob Johnson</option>
              </select>
              <label 
                for="assigned_to"
                class="absolute left-4 top-4 text-gray-500 dark:text-gray-400 transition-all duration-200 pointer-events-none
                  peer-focus:-top-2 peer-focus:text-xs peer-focus:text-blue-500 peer-focus:bg-white peer-focus:dark:bg-gray-800 peer-focus:px-1
                  [&:not(:focus):not(:placeholder-shown)]:-top-2 [&:not(:focus):not(:placeholder-shown)]:text-xs [&:not(:focus):not(:placeholder-shown)]:bg-white [&:not(:focus):not(:placeholder-shown)]:dark:bg-gray-800 [&:not(:focus):not(:placeholder-shown)]:px-1"
                :class="{ '-top-2 text-xs bg-white dark:bg-gray-800 px-1': form.assigned_to }"
              >
                Assign To
              </label>
              <svg class="absolute right-4 top-4 w-5 h-5 text-gray-400 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </div>

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
                @click="$router.back()"
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
import { ref, h } from 'vue';
import { useRouter } from 'vue-router';
import { useTaskStore } from '../stores/taskStore';
import { useUIStore } from '../stores/uiStore';

const router = useRouter();
const taskStore = useTaskStore();
const uiStore = useUIStore();

const form = ref({
  title: '',
  description: '',
  priority: 'medium',
  due_date: '',
  assigned_to: '',
});

const errors = ref({});
const isSubmitting = ref(false);
const successMessage = ref('');

// Priority options with icons
const priorities = [
  { 
    value: 'low', 
    label: 'Low', 
    activeClass: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-2 border-green-500',
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z', 'clip-rule': 'evenodd' })
        ]);
      }
    }
  },
  { 
    value: 'medium', 
    label: 'Medium', 
    activeClass: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 border-2 border-yellow-500',
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 100 2h2a1 1 0 100-2H9z', 'clip-rule': 'evenodd' })
        ]);
      }
    }
  },
  { 
    value: 'high', 
    label: 'High', 
    activeClass: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-2 border-red-500',
    icon: {
      render() {
        return h('svg', { class: 'w-5 h-5', fill: 'currentColor', viewBox: '0 0 20 20' }, [
          h('path', { 'fill-rule': 'evenodd', d: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z', 'clip-rule': 'evenodd' })
        ]);
      }
    }
  },
];

const submitForm = async () => {
  errors.value = {};

  if (!form.value.title.trim()) {
    errors.value.title = 'Title is required';
    return;
  }

  isSubmitting.value = true;

  try {
    await taskStore.createTask(form.value);
    successMessage.value = 'Task created successfully!';
    uiStore.addNotification({
      type: 'success',
      message: 'Task created successfully!',
    });

    setTimeout(() => {
      router.push('/tasks');
    }, 1500);
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: 'Failed to create task. Please try again.',
    });
  } finally {
    isSubmitting.value = false;
  }
};
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
