<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <div class="w-full p-4 sm:p-6 lg:p-8 space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-900/70">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <p class="text-xs uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">Overview</p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-semibold">Task Performance Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A template-style view of activity, workload, and execution.</p>
          </div>

          <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-3 lg:w-auto">
            <input
              type="text"
              placeholder="Search task"
              class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-slate-400 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:placeholder:text-slate-500 dark:focus:border-slate-500"
            />
            <select class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-400 focus:outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:focus:border-slate-500">
              <option>Last 7 days</option>
              <option>Last 30 days</option>
              <option>This quarter</option>
            </select>
            <button class="rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 transition dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
              Apply
            </button>
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <p class="text-xs uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Total Tasks</p>
          <p class="mt-3 text-3xl font-semibold">{{ taskStore.taskCount }}</p>
          <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Across all projects</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <p class="text-xs uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Completed</p>
          <p class="mt-3 text-3xl font-semibold text-emerald-400">{{ completedCount }}</p>
          <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Finished successfully</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <p class="text-xs uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">In Progress</p>
          <p class="mt-3 text-3xl font-semibold text-blue-400">{{ inProgressCount }}</p>
          <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Actively being worked</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <p class="text-xs uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Completion Rate</p>
          <p class="mt-3 text-3xl font-semibold text-violet-400">{{ completionRate }}%</p>
          <div class="mt-3 h-2 w-full rounded-full bg-slate-200 dark:bg-slate-800">
            <div class="h-2 rounded-full bg-violet-400" :style="{ width: completionRate + '%' }"></div>
          </div>
        </article>
      </section>

      <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 xl:col-span-2 dark:border-slate-800 dark:bg-slate-900">
          <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold">Weekly Activity</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Template chart grid</p>
          </div>

          <div class="mt-5 grid grid-cols-7 gap-2 sm:gap-3">
            <div v-for="(bar, index) in activityBars" :key="index" class="space-y-2">
              <div class="h-40 rounded-xl bg-slate-100 border border-slate-200 p-2 flex items-end dark:bg-slate-950 dark:border-slate-800">
                <div class="w-full rounded-lg bg-cyan-400/80" :style="{ height: bar + '%' }"></div>
              </div>
              <p class="text-center text-[11px] text-slate-500 dark:text-slate-400">{{ weekLabels[index] }}</p>
            </div>
          </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <h2 class="text-base font-semibold">Task Mix</h2>

          <div class="mt-5 space-y-4">
            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-300">Pending</span>
                <span class="text-slate-700 dark:text-slate-200">{{ pendingCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-800">
                <div class="h-2 rounded-full bg-slate-400" :style="{ width: pendingPercent + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-300">In progress</span>
                <span class="text-slate-700 dark:text-slate-200">{{ inProgressCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-800">
                <div class="h-2 rounded-full bg-blue-400" :style="{ width: inProgressPercent + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-300">Completed</span>
                <span class="text-slate-700 dark:text-slate-200">{{ completedCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-800">
                <div class="h-2 rounded-full bg-emerald-400" :style="{ width: completedPercent + '%' }"></div>
              </div>
            </div>
          </div>

          <div class="mt-6 rounded-xl border border-slate-200 bg-slate-100 p-4 dark:border-slate-800 dark:bg-slate-950">
            <p class="text-xs uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Due soon</p>
            <p class="mt-2 text-2xl font-semibold text-amber-300">{{ pendingCount }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pending tasks requiring attention</p>
          </div>
        </article>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white overflow-hidden dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center justify-between dark:border-slate-800">
          <h2 class="text-base font-semibold">Recent Tasks</h2>
          <router-link to="/tasks" class="text-sm text-cyan-300 hover:text-cyan-200 transition">View all</router-link>
        </div>

        <div v-if="recentTasks.length" class="divide-y divide-slate-200 dark:divide-slate-800">
          <div
            v-for="task in recentTasks"
            :key="task.id"
            class="px-5 py-4 grid grid-cols-1 gap-2 md:grid-cols-[2fr_1fr_1fr] md:items-center hover:bg-slate-100 transition dark:hover:bg-slate-800/40"
          >
            <div>
              <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ task.title }}</p>
              <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ (task.description || 'No description').slice(0, 70) }}</p>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300">{{ formatStatus(task.status) }}</p>
            <p class="text-xs text-slate-600 dark:text-slate-300">Priority: {{ capitalize(task.priority || 'n/a') }}</p>
          </div>
        </div>

        <div v-else class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
          No tasks available yet.
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useTaskStore } from '../stores/taskStore';

const taskStore = useTaskStore();

onMounted(async () => {
  await taskStore.fetchAllTasks();
  await taskStore.fetchMyTasks();
});

const recentTasks = computed(() => taskStore.tasks.slice(0, 6));
const total = computed(() => taskStore.tasks.length);

const pendingCount = computed(() => taskStore.tasks.filter(task => task.status === 'pending').length);
const inProgressCount = computed(() => taskStore.tasks.filter(task => task.status === 'in_progress').length);
const completedCount = computed(() => taskStore.tasks.filter(task => task.status === 'completed').length);

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
</script>
