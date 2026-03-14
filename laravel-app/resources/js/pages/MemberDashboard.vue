<template>
  <div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Member View</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900">My Tasks Dashboard</h1>
        <p class="mt-2 text-sm text-slate-600">Focus on your assignments, deadlines, and what to finish next.</p>
        <div class="mt-4">
          <a
            href="/dashboard"
            class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
          >
            Back to Main Dashboard
          </a>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Assigned to Me</p>
          <p class="mt-3 text-3xl font-bold text-slate-900">{{ myTaskCount }}</p>
        </article>
        <article class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">In Progress</p>
          <p class="mt-3 text-3xl font-bold text-blue-700">{{ myInProgressCount }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Completed</p>
          <p class="mt-3 text-3xl font-bold text-emerald-700">{{ myCompletedCount }}</p>
        </article>
        <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Pending</p>
          <p class="mt-3 text-3xl font-bold text-amber-700">{{ myPendingCount }}</p>
        </article>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
          <h2 class="text-lg font-semibold text-slate-900">My Active Work</h2>
          <router-link to="/tasks" class="text-sm font-medium text-blue-600 hover:text-blue-700">View my tasks</router-link>
        </div>

        <div v-if="myTasks.length" class="divide-y divide-slate-200">
          <div
            v-for="task in myTasks"
            :key="task.id"
            class="grid grid-cols-1 gap-2 px-5 py-4 md:grid-cols-[2fr_1fr_1fr] md:items-center hover:bg-slate-50"
          >
            <div>
              <p class="text-sm font-semibold text-slate-900">{{ task.title }}</p>
              <p class="mt-1 text-xs text-slate-500">{{ task.description || 'No description' }}</p>
            </div>
            <span class="text-xs text-slate-600">{{ formatStatus(task.status) }}</span>
            <span class="text-xs font-medium" :class="priorityClass(task.priority)">{{ capitalize(task.priority) }} priority</span>
          </div>
        </div>

        <div v-else class="px-5 py-8 text-center text-sm text-slate-500">No assigned tasks yet.</div>
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

const myTasks = computed(() => (taskStore.myTasks.length ? taskStore.myTasks : taskStore.tasks).slice(0, 10));
const myTaskCount = computed(() => myTasks.value.length);
const myPendingCount = computed(() => myTasks.value.filter((task) => task.status === 'pending').length);
const myInProgressCount = computed(() => myTasks.value.filter((task) => task.status === 'in_progress').length);
const myCompletedCount = computed(() => myTasks.value.filter((task) => task.status === 'completed').length);

const formatStatus = (status) => {
  if (status === 'in_progress') return 'In Progress';
  if (!status) return 'Unknown';
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const capitalize = (value) => {
  if (!value) return 'Unknown';
  return value.charAt(0).toUpperCase() + value.slice(1);
};

const priorityClass = (priority) => {
  if (priority === 'high') return 'text-red-600';
  if (priority === 'medium') return 'text-amber-600';
  return 'text-emerald-600';
};
</script>
