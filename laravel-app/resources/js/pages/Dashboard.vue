<template>
  <div class="min-h-screen bg-gray-50 text-slate-900">
    <div class="mx-auto w-full max-w-[1400px] p-4 sm:p-6 lg:p-8 space-y-6">
      <section class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-7 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Overview</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Task Performance Dashboard</h1>
            <p class="mt-2 text-sm text-slate-600">Track team progress, workload balance, and delivery trends at a glance.</p>
            <div class="mt-4 flex flex-wrap items-center gap-2">
              <a
                v-if="canCreateTasks"
                href="/tasks/create"
                class="inline-flex items-center rounded-xl bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
              >
                Create New Task
              </a>
            </div>
          </div>

          <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-3 lg:w-auto">
            <input
              v-model.trim="draftSearch"
              type="text"
              placeholder="Search task"
              class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
              @keyup.enter="applyFilters"
            />
            <select v-model="draftRange" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
              <option value="7">Last 7 days</option>
              <option value="30">Last 30 days</option>
              <option value="90">This quarter</option>
              <option value="all">All time</option>
            </select>
            <button @click="applyFilters" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
              Apply
            </button>
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Total Tasks</p>
          <div class="mt-3 flex items-end justify-between">
            <p class="text-3xl font-bold">{{ total }}</p>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600">Overall</span>
          </div>
          <p class="mt-3 text-xs text-slate-500">Across all projects</p>
        </article>

        <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Completed</p>
          <div class="mt-3 flex items-end justify-between">
            <p class="text-3xl font-bold text-emerald-700">{{ completedCount }}</p>
            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs text-emerald-700">Done</span>
          </div>
          <p class="mt-3 text-xs text-emerald-700/80">Finished successfully</p>
        </article>

        <article class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">In Progress</p>
          <div class="mt-3 flex items-end justify-between">
            <p class="text-3xl font-bold text-blue-700">{{ inProgressCount }}</p>
            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs text-blue-700">Active</span>
          </div>
          <p class="mt-3 text-xs text-blue-700/80">Actively being worked</p>
        </article>

        <article class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-violet-700">Completion Rate</p>
          <p class="mt-3 text-3xl font-bold text-violet-700">{{ completionRate }}%</p>
          <div class="mt-4 h-2 w-full rounded-full bg-violet-100">
            <div class="h-2 rounded-full bg-violet-500" :style="{ width: completionRate + '%' }"></div>
          </div>
        </article>
      </section>

      <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Weekly Activity</h2>
            <p class="text-xs text-slate-500">Template chart grid</p>
          </div>

          <div class="mt-5 grid grid-cols-7 gap-2 sm:gap-3">
            <div v-for="(bar, index) in activityBars" :key="index" class="space-y-2">
              <div class="h-40 rounded-xl bg-slate-50 border border-slate-200 p-2 flex items-end">
                <div class="w-full rounded-lg bg-cyan-500" :style="{ height: bar + '%' }"></div>
              </div>
              <p class="text-center text-[11px] text-slate-500">{{ weekLabels[index] }}</p>
            </div>
          </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 class="text-lg font-semibold">Task Mix</h2>

          <div class="mt-5 space-y-4">
            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="text-slate-600">Pending</span>
                <span class="font-medium text-slate-800">{{ pendingCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-slate-500" :style="{ width: pendingPercent + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="text-slate-600">In Progress</span>
                <span class="font-medium text-slate-800">{{ inProgressCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-blue-500" :style="{ width: inProgressPercent + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="text-slate-600">Completed</span>
                <span class="font-medium text-slate-800">{{ completedCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-emerald-500" :style="{ width: completedPercent + '%' }"></div>
              </div>
            </div>
          </div>

          <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">Due Soon</p>
            <p class="mt-2 text-2xl font-bold text-amber-700">{{ pendingCount }}</p>
            <p class="mt-1 text-xs text-amber-700/80">Pending tasks requiring attention</p>
          </div>
        </article>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center justify-between">
          <h2 class="text-lg font-semibold">Recent Tasks</h2>
          <router-link to="/tasks" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition">View all</router-link>
        </div>

        <div v-if="recentTasks.length" class="divide-y divide-slate-200">
          <div
            v-for="task in recentTasks"
            :key="task.id"
            class="px-5 py-4 grid grid-cols-1 gap-2 md:grid-cols-[2fr_1fr_1fr_1fr] md:items-center hover:bg-slate-50 transition"
          >
            <div>
              <p class="text-sm font-semibold text-slate-900">{{ task.title }}</p>
              <p class="mt-1 text-xs text-slate-500">{{ (task.description || 'No description').slice(0, 70) }}</p>
            </div>

            <span :class="statusClass(task.status)" class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-medium">
              {{ formatStatus(task.status) }}
            </span>

            <span :class="priorityClass(task.priority)" class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-medium">
              {{ capitalize(task.priority || 'n/a') }} Priority
            </span>

            <p class="text-xs text-slate-500">Due: {{ formatDate(task.due_at || task.due_date) }}</p>
          </div>
        </div>

        <div v-else class="px-5 py-10 text-center text-sm text-slate-500">
          No tasks available yet.
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useTaskStore } from '../stores/taskStore';

const taskStore = useTaskStore();
const authState = window.TaskAppAuth ?? {
  user: null,
};
const draftSearch = ref('');
const appliedSearch = ref('');
const draftRange = ref('7');
const appliedRange = ref('7');

onMounted(async () => {
  await taskStore.fetchAllTasks();
  await taskStore.fetchMyTasks();
});

const canCreateTasks = computed(() => ['admin', 'manager'].includes(authState?.user?.role));

const inSelectedRange = (task) => {
  if (appliedRange.value === 'all') return true;

  const days = Number.parseInt(appliedRange.value, 10);
  if (!Number.isFinite(days) || days <= 0) return true;

  const sourceDate = task.due_at || task.due_date || task.created_at || task.updated_at;
  if (!sourceDate) return true;

  const parsedDate = new Date(sourceDate);
  if (Number.isNaN(parsedDate.getTime())) return true;

  const cutoff = new Date();
  cutoff.setHours(0, 0, 0, 0);
  cutoff.setDate(cutoff.getDate() - days);

  return parsedDate >= cutoff;
};

const filteredTasks = computed(() => {
  const keyword = appliedSearch.value.toLowerCase();

  return taskStore.tasks.filter((task) => {
    const haystack = `${task.title || ''} ${task.description || ''}`.toLowerCase();
    const matchesSearch = !keyword || haystack.includes(keyword);

    return matchesSearch && inSelectedRange(task);
  });
});

const applyFilters = () => {
  appliedSearch.value = draftSearch.value;
  appliedRange.value = draftRange.value;
};

const recentTasks = computed(() => filteredTasks.value.slice(0, 6));
const total = computed(() => filteredTasks.value.length);

const pendingCount = computed(() => filteredTasks.value.filter(task => task.status === 'pending').length);
const inProgressCount = computed(() => filteredTasks.value.filter(task => task.status === 'in_progress').length);
const completedCount = computed(() => filteredTasks.value.filter(task => task.status === 'completed').length);

const percent = (value) => {
  if (!total.value) return 0;
  return Math.round((value / total.value) * 100);
};

const pendingPercent = computed(() => percent(pendingCount.value));
const inProgressPercent = computed(() => percent(inProgressCount.value));
const completedPercent = computed(() => percent(completedCount.value));
const completionRate = computed(() => completedPercent.value);

const weekLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const activityBars = computed(() => {
  const base = [36, 42, 38, 54, 48, 60, 52];
  const loadBoost = Math.min(20, total.value * 2);
  return base.map(value => Math.min(95, value + Math.round(loadBoost * (value / 60))));
});

const formatStatus = (status) => {
  if (status === 'in_progress') return 'In Progress';
  if (!status) return 'Unknown';
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const capitalize = (value) => value.charAt(0).toUpperCase() + value.slice(1);

const statusClass = (status) => {
  if (status === 'completed') return 'bg-emerald-100 text-emerald-700';
  if (status === 'in_progress') return 'bg-blue-100 text-blue-700';
  return 'bg-slate-100 text-slate-700';
};

const priorityClass = (priority) => {
  if (priority === 'high') return 'bg-red-100 text-red-700';
  if (priority === 'medium') return 'bg-amber-100 text-amber-700';
  if (priority === 'low') return 'bg-emerald-100 text-emerald-700';
  return 'bg-slate-100 text-slate-700';
};

const formatDate = (value) => {
  if (!value) return 'No date';
  const parsedDate = new Date(value);
  if (Number.isNaN(parsedDate.getTime())) return 'No date';
  return parsedDate.toLocaleDateString();
};
</script>
