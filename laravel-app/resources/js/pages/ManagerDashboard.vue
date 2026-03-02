<template>
  <div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Manager View</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900">Team Workload Dashboard</h1>
        <p class="mt-2 text-sm text-slate-600">Monitor team execution, unblock pending work, and keep delivery on track.</p>
      </section>

      <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Team Tasks</p>
          <p class="mt-3 text-3xl font-bold text-slate-900">{{ taskStore.taskCount }}</p>
        </article>
        <article class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">In Progress</p>
          <p class="mt-3 text-3xl font-bold text-blue-700">{{ inProgressCount }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Completed</p>
          <p class="mt-3 text-3xl font-bold text-emerald-700">{{ completedCount }}</p>
        </article>
        <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Pending</p>
          <p class="mt-3 text-3xl font-bold text-amber-700">{{ pendingCount }}</p>
        </article>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
          <h2 class="text-lg font-semibold text-slate-900">Priority Queue</h2>
          <router-link to="/tasks" class="text-sm font-medium text-blue-600 hover:text-blue-700">Open task board</router-link>
        </div>

        <div v-if="topTasks.length" class="divide-y divide-slate-200">
          <div
            v-for="task in topTasks"
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

        <div v-else class="px-5 py-8 text-center text-sm text-slate-500">No tasks available.</div>
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
});

const pendingCount = computed(() => taskStore.tasks.filter((task) => task.status === 'pending').length);
const inProgressCount = computed(() => taskStore.tasks.filter((task) => task.status === 'in_progress').length);
const completedCount = computed(() => taskStore.tasks.filter((task) => task.status === 'completed').length);

const topTasks = computed(() => {
  const ranked = [...taskStore.tasks].sort((firstTask, secondTask) => {
    const priorityOrder = { high: 0, medium: 1, low: 2 };
    return (priorityOrder[firstTask.priority] ?? 3) - (priorityOrder[secondTask.priority] ?? 3);
  });
  return ranked.slice(0, 8);
});

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
