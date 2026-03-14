<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <div class="mx-auto w-full max-w-7xl p-4 sm:p-8">
      <div class="mb-6 flex items-center justify-between gap-4">
        <button
          @click="$router.back()"
          class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
        >
          <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
          Back
        </button>

        <div class="flex items-center gap-2">
          <button
            type="button"
            :disabled="isImporting"
            @click="openImportPicker"
            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-60 disabled:cursor-not-allowed dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
          >
            <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3 3a2 2 0 012-2h3a1 1 0 010 2H5v14h10V3h-3a1 1 0 110-2h3a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V3zm7 2a1 1 0 011 1v5.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L9 11.586V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            {{ isImporting ? 'Importing...' : 'Import Excel' }}
          </button>

          <input
            ref="importInput"
            type="file"
            accept=".xlsx,.csv"
            class="hidden"
            @change="handleImportFile"
          >
        </div>
      </div>

      <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Create New Task</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create a task and assign one or more members.</p>
      </div>

      <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="space-y-6 xl:col-span-2">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Task Details</h2>

            <div class="space-y-4">
              <div>
                <label for="title" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">SMM & Recommendations *</label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-100': errors.title }"
                  placeholder="e.g. Prepare release checklist"
                  required
                >
                <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.title }}</p>
              </div>

              <div>
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="5"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                  placeholder="Add context, acceptance criteria, or links"
                ></textarea>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Priority</h2>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
              <button
                v-for="priority in priorities"
                :key="priority.value"
                type="button"
                @click="form.priority = priority.value"
                :class="[
                  'rounded-xl border px-4 py-4 text-sm font-medium transition flex items-center justify-center gap-2',
                  form.priority === priority.value
                    ? priority.activeClass
                    : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700'
                ]"
              >
                {{ priority.label }}
              </button>
            </div>
          </div>
        </section>

        <aside class="space-y-6">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Scheduling & Assignment</h2>

            <div class="space-y-4">
              <div>
                <label for="due_date" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Due Date</label>
                <input
                  id="due_date"
                  v-model="form.due_date"
                  type="date"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                >
              </div>

              <div>
                <label for="assignee-search" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Responsible person</label>
                <input
                  id="assignee-search"
                  v-model="assigneeSearch"
                  type="text"
                  placeholder="Search member by name, username, or email"
                  class="mb-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                  @input="onAssigneeSearchInput"
                >
                <div class="max-h-40 overflow-y-auto rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2" :class="{ 'opacity-60': assigneesLoading }">
                  <label
                    v-for="assignee in assigneeOptions"
                    :key="assignee.id"
                    class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      :value="String(assignee.id)"
                      v-model="form.assignee_ids"
                      :disabled="assigneesLoading"
                      class="h-4 w-4"
                    >
                    <span>{{ assignee.name }}</span>
                  </label>
                  <p v-if="!assigneesLoading && !assigneeOptions.length" class="px-2 py-2 text-xs text-slate-500 dark:text-slate-400">No members found.</p>
                  <p v-if="assigneesLoading" class="px-2 py-2 text-xs text-slate-500 dark:text-slate-400">Loading members...</p>
                </div>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Only active users with member role are shown. You can create the task now and assign later.</p>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Summary</h2>
            <div class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
              <p><span class="font-medium text-slate-800 dark:text-white">Status:</span> Pending</p>
              <p><span class="font-medium text-slate-800 dark:text-white">Priority:</span> {{ capitalize(form.priority) }}</p>
              <p><span class="font-medium text-slate-800 dark:text-white">Assignees:</span> {{ selectedAssigneeText }}</p>
            </div>

            <div class="mt-6 space-y-3">
              <button
                type="submit"
                :disabled="isSubmitting"
                class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
              >
                <svg v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ isSubmitting ? 'Creating...' : 'Create Task' }}
              </button>

              <button
                type="button"
                @click="$router.back()"
                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
              >
                Cancel
              </button>
            </div>

            <transition name="fade">
              <div v-if="successMessage" class="mt-4 rounded-xl bg-emerald-50 px-3 py-2 text-sm text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                {{ successMessage }}
              </div>
            </transition>

            <transition name="fade">
              <div v-if="formError" class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-300">
                {{ formError }}
              </div>
            </transition>
          </div>
        </aside>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useTaskStore } from '../stores/taskStore';
import { useUIStore } from '../stores/uiStore';
import { taskService } from '../services/api';

const router = useRouter();
const taskStore = useTaskStore();
const uiStore = useUIStore();

const form = ref({
  title: '',
  description: '',
  priority: 'medium',
  due_date: '',
  assignee_ids: [],
});

const errors = ref({});
const isSubmitting = ref(false);
const successMessage = ref('');
const formError = ref('');
const assigneeOptions = ref([]);
const assigneesLoading = ref(false);
const assigneeSearch = ref('');
const importInput = ref(null);
const isImporting = ref(false);
let assigneeSearchTimer = null;

const getApiErrorMessage = (error, fallbackMessage) => error?.response?.data?.message || fallbackMessage;

const normalizeValidationErrors = (apiErrors) => {
  if (!apiErrors || typeof apiErrors !== 'object') {
    return {};
  }

  return Object.entries(apiErrors).reduce((acc, [key, messages]) => {
    acc[key] = Array.isArray(messages) ? messages[0] : String(messages);
    return acc;
  }, {});
};

const formatHttpError = (message, statusCode, fallbackMessage) => (
  message
    ? `${message}${statusCode ? ` (HTTP ${statusCode})` : ''}`
    : `${fallbackMessage}${statusCode ? ` (HTTP ${statusCode})` : ''}. Please try again.`
);

const selectedAssigneeText = computed(() => {
  if (!form.value.assignee_ids.length) return 'Unassigned';

  const selectedNames = assigneeOptions.value
    .filter((item) => form.value.assignee_ids.includes(String(item.id)))
    .map((item) => item.name);

  if (selectedNames.length) {
    return selectedNames.join(', ');
  }

  return `${form.value.assignee_ids.length} member(s) selected`;
});

// Priority options with icons
const priorities = [
  { 
    value: 'low', 
    label: 'Low', 
    activeClass: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-2 border-green-500',
  },
  { 
    value: 'medium', 
    label: 'Medium', 
    activeClass: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 border-2 border-yellow-500',
  },
  { 
    value: 'high', 
    label: 'High', 
    activeClass: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-2 border-red-500',
  },
];

const loadAssignees = async (search = '') => {
  assigneesLoading.value = true;

  try {
    const response = await taskService.getAssignees({ search });
    assigneeOptions.value = Array.isArray(response.data?.assignees) ? response.data.assignees : [];
  } catch {
    assigneeOptions.value = [];
    uiStore.addNotification({ type: 'error', message: 'Unable to load members.' });
  } finally {
    assigneesLoading.value = false;
  }
};

const onAssigneeSearchInput = () => {
  if (assigneeSearchTimer) {
    clearTimeout(assigneeSearchTimer);
  }

  assigneeSearchTimer = setTimeout(() => {
    loadAssignees(assigneeSearch.value.trim());
  }, 300);
};

const openImportPicker = () => {
  if (isImporting.value) return;
  importInput.value?.click();
};

const handleImportFile = async (event) => {
  const file = event?.target?.files?.[0];
  if (!file) return;

  isImporting.value = true;

  try {
    const formData = new FormData();
    formData.append('file', file);

    const response = await taskService.import(formData);
    const created = Number(response.data?.created || 0);
    const skipped = Number(response.data?.skipped || 0);

    uiStore.addNotification({
      type: 'success',
      message: skipped > 0
        ? `Import complete: ${created} task(s) created, ${skipped} row(s) skipped.`
        : `Import complete: ${created} task(s) created.`,
    });

    await taskStore.fetchAllTasks(true);
  } catch (error) {
    uiStore.addNotification({
      type: 'error',
      message: getApiErrorMessage(error, 'Task import failed. Check file format and try again.'),
    });
  } finally {
    isImporting.value = false;
    if (event?.target) {
      event.target.value = '';
    }
  }
};

onMounted(async () => {
  await loadAssignees('');
});

const submitForm = async () => {
  errors.value = {};
  successMessage.value = '';
  formError.value = '';

  if (!form.value.title.trim()) {
    errors.value.title = 'Title is required';
    return;
  }

  isSubmitting.value = true;

  try {
    const payload = {
      title: form.value.title,
      description: form.value.description || null,
      priority: form.value.priority,
      status: 'pending',
      due_at: form.value.due_date || null,
      assignees: form.value.assignee_ids.map((id) => Number(id)),
    };

    await taskStore.createTask(payload);
    await taskStore.fetchAllTasks(true);
    successMessage.value = 'Task created successfully!';
    uiStore.addNotification({
      type: 'success',
      message: 'Task created successfully!',
    });

    setTimeout(() => {
      router.push('/tasks');
    }, 1500);
  } catch (error) {
    const apiErrors = error?.response?.data?.errors;
    const apiMessage = getApiErrorMessage(error, 'Failed to create task. Please try again.');
    const statusCode = error?.response?.status;
    const normalizedErrors = normalizeValidationErrors(apiErrors);

    if (Object.keys(normalizedErrors).length > 0) {
      errors.value = normalizedErrors;
    } else {
      errors.value.title = apiMessage;
    }

    uiStore.addNotification({
      type: 'error',
      message: apiMessage,
    });

    formError.value = formatHttpError(apiMessage, statusCode, 'Failed to create task');
  } finally {
    isSubmitting.value = false;
  }
};

const capitalize = (value) => value.charAt(0).toUpperCase() + value.slice(1);
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
