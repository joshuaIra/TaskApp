<template>
  <section class="space-y-6">
    <header class="flex flex-col gap-2">
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reports</h1>
      <p class="text-slate-600 dark:text-slate-300">Filter tasks by date range, status, priority, assignee, manager, and due window.</p>
    </header>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 sm:p-6 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <label class="flex flex-col gap-2">
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Date From</span>
          <input v-model="filters.dateFrom" type="date" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </label>

        <label class="flex flex-col gap-2">
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Date To</span>
          <input v-model="filters.dateTo" type="date" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100" />
        </label>

        <label class="flex flex-col gap-2">
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Status</span>
          <select v-model="filters.status" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </label>

        <label class="flex flex-col gap-2">
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Priority</span>
          <select v-model="filters.priority" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option value="">All</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </label>

        <label class="flex flex-col gap-2">
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Assignee</span>
          <select v-model="filters.assignee" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option value="">All</option>
            <option v-for="name in assigneeOptions" :key="name" :value="name">{{ name }}</option>
          </select>
        </label>

        <label class="flex flex-col gap-2">
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Manager</span>
          <select v-model="filters.manager" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option value="">All</option>
            <option v-for="name in managerOptions" :key="name" :value="name">{{ name }}</option>
          </select>
        </label>

        <label class="flex flex-col gap-2">
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Due Window</span>
          <select v-model="filters.dueWindow" class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-100">
            <option value="">All</option>
            <option value="overdue">Overdue</option>
            <option value="today">Due Today</option>
            <option value="next_7">Next 7 Days</option>
            <option value="next_30">Next 30 Days</option>
            <option value="no_due">No Due Date</option>
          </select>
        </label>
      </div>

      <div class="flex items-center justify-between gap-4 flex-wrap">
        <p class="text-sm text-slate-600 dark:text-slate-300">{{ filteredTasks.length }} task(s) matched</p>
        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="exportCsv"
            :disabled="!filteredTasks.length"
            class="rounded-xl border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Export CSV
          </button>
          <button type="button" @click="resetFilters" class="rounded-xl border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
            Clear Filters
          </button>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden">
      <div v-if="taskStore.loading" class="p-6 text-slate-600 dark:text-slate-300">Loading report data...</div>
      <div v-else-if="!filteredTasks.length" class="p-6 text-slate-600 dark:text-slate-300">No tasks match the selected filters.</div>
      <div v-else class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300">
            <tr>
              <th class="text-left px-4 py-3 font-semibold">Task</th>
              <th class="text-left px-4 py-3 font-semibold">Status</th>
              <th class="text-left px-4 py-3 font-semibold">Priority</th>
              <th class="text-left px-4 py-3 font-semibold">Assignee</th>
              <th class="text-left px-4 py-3 font-semibold">Manager</th>
              <th class="text-left px-4 py-3 font-semibold">Due Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in filteredTasks" :key="task.id" class="border-t border-slate-100 dark:border-slate-700">
              <td class="px-4 py-3 text-slate-900 dark:text-slate-100">
                <router-link :to="`/tasks/${task.id}`" class="font-medium hover:underline">{{ task.title }}</router-link>
              </td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ formatStatus(task.status) }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ capitalize(task.priority) }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ getAssigneeName(task) }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ getManagerName(task) }}</td>
              <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ formatDate(getDueDate(task)) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTaskStore } from '../stores/taskStore';

const taskStore = useTaskStore();
const route = useRoute();
const router = useRouter();
const quickExportDone = ref(false);

const filters = reactive({
  dateFrom: '',
  dateTo: '',
  status: '',
  priority: '',
  assignee: '',
  manager: '',
  dueWindow: '',
});

onMounted(async () => {
  await taskStore.fetchAllTasks();

  if (route.query.quickExport === '1' && !quickExportDone.value) {
    exportCsv();
    quickExportDone.value = true;
    const query = { ...route.query };
    delete query.quickExport;
    router.replace({ path: route.path, query });
  }
});

const normalizeDate = (dateValue) => {
  if (!dateValue) return null;
  const date = new Date(dateValue);
  if (Number.isNaN(date.getTime())) return null;
  date.setHours(0, 0, 0, 0);
  return date;
};

const getDueDate = (task) => task.due_at || task.due_date || null;

const getAssigneeName = (task) => {
  if (typeof task.assigned_to === 'string' && task.assigned_to.trim().length) return task.assigned_to;
  if (Array.isArray(task.assignees) && task.assignees.length) return task.assignees[0]?.name || 'Unassigned';
  return 'Unassigned';
};

const getManagerName = (task) => {
  if (task.creator?.name) return task.creator.name;
  if (task.manager?.name) return task.manager.name;
  return 'N/A';
};

const assigneeOptions = computed(() => {
  const set = new Set(taskStore.tasks.map(getAssigneeName).filter((name) => name && name !== 'Unassigned'));
  return [...set].sort((a, b) => a.localeCompare(b));
});

const managerOptions = computed(() => {
  const set = new Set(taskStore.tasks.map(getManagerName).filter((name) => name && name !== 'N/A'));
  return [...set].sort((a, b) => a.localeCompare(b));
});

const filteredTasks = computed(() => {
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  return [...taskStore.tasks].filter((task) => {
    const dueDate = normalizeDate(getDueDate(task));
    const fromDate = normalizeDate(filters.dateFrom);
    const toDate = normalizeDate(filters.dateTo);

    if (filters.status && task.status !== filters.status) return false;
    if (filters.priority && task.priority !== filters.priority) return false;
    if (filters.assignee && getAssigneeName(task) !== filters.assignee) return false;
    if (filters.manager && getManagerName(task) !== filters.manager) return false;
    if (fromDate && (!dueDate || dueDate < fromDate)) return false;
    if (toDate && (!dueDate || dueDate > toDate)) return false;

    if (filters.dueWindow === 'no_due') return !dueDate;
    if (!dueDate && filters.dueWindow) return false;
    if (filters.dueWindow === 'overdue') return dueDate < today;
    if (filters.dueWindow === 'today') return dueDate.getTime() === today.getTime();
    if (filters.dueWindow === 'next_7') {
      const next7 = new Date(today);
      next7.setDate(next7.getDate() + 7);
      return dueDate >= today && dueDate <= next7;
    }
    if (filters.dueWindow === 'next_30') {
      const next30 = new Date(today);
      next30.setDate(next30.getDate() + 30);
      return dueDate >= today && dueDate <= next30;
    }

    return true;
  }).sort((a, b) => {
    const aDate = normalizeDate(getDueDate(a));
    const bDate = normalizeDate(getDueDate(b));
    if (!aDate && !bDate) return b.id - a.id;
    if (!aDate) return 1;
    if (!bDate) return -1;
    return aDate - bDate;
  });
});

const formatDate = (dateValue) => {
  if (!dateValue) return 'No due date';
  const date = new Date(dateValue);
  if (Number.isNaN(date.getTime())) return 'No due date';
  return new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(date);
};

const formatStatus = (status) => {
  const map = {
    pending: 'Pending',
    in_progress: 'In Progress',
    completed: 'Completed',
  };
  return map[status] || status;
};

const capitalize = (value) => {
  if (!value) return '-';
  return value.charAt(0).toUpperCase() + value.slice(1);
};

const csvEscape = (value) => {
  const raw = value == null ? '' : String(value);
  return `"${raw.replace(/"/g, '""')}"`;
};

const exportCsv = () => {
  if (!filteredTasks.value.length) return;

  const headers = ['ID', 'Task', 'Status', 'Priority', 'Assignee', 'Manager', 'Due Date'];
  const rows = filteredTasks.value.map((task) => [
    task.id,
    task.title,
    formatStatus(task.status),
    capitalize(task.priority),
    getAssigneeName(task),
    getManagerName(task),
    formatDate(getDueDate(task)),
  ]);

  const csv = [headers, ...rows]
    .map((row) => row.map((value) => csvEscape(value)).join(','))
    .join('\n');

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `reports-${new Date().toISOString().slice(0, 10)}.csv`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
};

const resetFilters = () => {
  filters.dateFrom = '';
  filters.dateTo = '';
  filters.status = '';
  filters.priority = '';
  filters.assignee = '';
  filters.manager = '';
  filters.dueWindow = '';
};
</script>
