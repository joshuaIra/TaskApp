<template>
  <div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl space-y-6">
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Manager View</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900">Assigned Tasks Dashboard</h1>
        <p class="mt-2 text-sm text-slate-600">Focus on tasks assigned to you by admin and track your fulfillment progress.</p>
        <div class="mt-4 flex flex-wrap items-center gap-2">
          <a
            href="/dashboard"
            class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
          >
            Back to Main Dashboard
          </a>
          <a
            v-if="canCreateTasks"
            href="/tasks/create"
            class="inline-flex items-center rounded-xl bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
          >
            Create New Task
          </a>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Managed Tasks</p>
          <p class="mt-3 text-3xl font-bold text-slate-900">{{ totalTasks }}</p>
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
        <article class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Overdue</p>
          <p class="mt-3 text-3xl font-bold text-red-700">{{ overdueCount }}</p>
        </article>
        <article class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Completion Rate</p>
          <p class="mt-3 text-3xl font-bold text-violet-700">{{ completionRate }}%</p>
        </article>
      </section>

      <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Status Graph</h2>
            <span class="text-xs text-slate-500">Completion ring</span>
          </div>

          <div class="mt-4 flex items-center justify-center">
            <div class="relative h-[190px] w-[190px]">
              <svg viewBox="0 0 190 190" class="h-full w-full -rotate-90">
                <circle cx="95" cy="95" :r="chartRadius" class="fill-none stroke-slate-200" stroke-width="16" />
                <circle
                  cx="95"
                  cy="95"
                  :r="chartRadius"
                  class="fill-none stroke-emerald-500"
                  stroke-width="16"
                  stroke-linecap="round"
                  :stroke-dasharray="`${completedArc} ${chartCircumference}`"
                  stroke-dashoffset="0"
                />
                <circle
                  cx="95"
                  cy="95"
                  :r="chartRadius"
                  class="fill-none stroke-blue-500"
                  stroke-width="16"
                  stroke-linecap="round"
                  :stroke-dasharray="`${inProgressArc} ${chartCircumference}`"
                  :stroke-dashoffset="inProgressOffset"
                />
                <circle
                  cx="95"
                  cy="95"
                  :r="chartRadius"
                  class="fill-none stroke-amber-500"
                  stroke-width="16"
                  stroke-linecap="round"
                  :stroke-dasharray="`${pendingArc} ${chartCircumference}`"
                  :stroke-dashoffset="pendingOffset"
                />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <p class="text-3xl font-bold text-slate-900">{{ completionRate }}%</p>
                <p class="text-xs text-slate-500">Completed</p>
              </div>
            </div>
          </div>

          <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
            <div class="rounded-lg bg-emerald-50 px-2 py-2 text-emerald-700">Done {{ completedCount }}</div>
            <div class="rounded-lg bg-blue-50 px-2 py-2 text-blue-700">Active {{ inProgressCount }}</div>
            <div class="rounded-lg bg-amber-50 px-2 py-2 text-amber-700">Pending {{ pendingCount }}</div>
          </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">7-Day Activity Graph</h2>
            <span class="text-xs text-slate-500">Tasks created/updated</span>
          </div>

          <div class="mt-5 grid grid-cols-7 gap-2 sm:gap-3">
            <div v-for="day in weeklyActivity" :key="day.label" class="space-y-2">
              <div class="h-36 rounded-xl bg-slate-50 border border-slate-200 p-2 flex items-end">
                <div
                  class="w-full rounded-lg bg-blue-500"
                  :style="{ height: `${Math.max((day.count / maxWeeklyActivity) * 100, day.count ? 10 : 0)}%` }"
                ></div>
              </div>
              <p class="text-center text-[11px] text-slate-500">{{ day.label }}</p>
              <p class="text-center text-[11px] font-semibold text-slate-700">{{ day.count }}</p>
            </div>
          </div>
        </article>
      </section>

      <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Managed Workload Health</h2>
            <span class="text-xs text-slate-500">Live status distribution</span>
          </div>

          <div class="mt-5 space-y-4">
            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="text-slate-600">Completed</span>
                <span class="font-medium text-slate-800">{{ completedCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-emerald-500" :style="{ width: completedPercent + '%' }"></div>
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
                <span class="text-slate-600">Pending</span>
                <span class="font-medium text-slate-800">{{ pendingCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-amber-500" :style="{ width: pendingPercent + '%' }"></div>
              </div>
            </div>

            <div>
              <div class="mb-1 flex items-center justify-between text-sm">
                <span class="text-slate-600">Overdue (open)</span>
                <span class="font-medium text-slate-800">{{ overdueCount }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-red-500" :style="{ width: overduePercent + '%' }"></div>
              </div>
            </div>
          </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 class="text-lg font-semibold">Delivery Snapshot</h2>
          <div class="mt-5 space-y-3 text-sm">
            <div class="flex items-center justify-between text-slate-600">
              <span>Due in next 3 days</span>
              <span class="font-semibold text-slate-900">{{ dueSoonCount }}</span>
            </div>
            <div class="flex items-center justify-between text-slate-600">
              <span>Completed this week</span>
              <span class="font-semibold text-emerald-600">{{ completedThisWeek }}</span>
            </div>
            <div class="flex items-center justify-between text-slate-600">
              <span>Open workload</span>
              <span class="font-semibold text-blue-600">{{ openTasksCount }}</span>
            </div>
          </div>

          <div class="mt-6 rounded-xl border border-slate-200 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Manager focus</p>
            <p class="mt-2 text-sm text-slate-700">Prioritize overdue and due-soon tasks, then close in-progress work.</p>
          </div>
        </article>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
          <h2 class="text-lg font-semibold text-slate-900">Overdue & Due Soon</h2>
          <router-link to="/tasks" class="text-sm font-medium text-blue-600 hover:text-blue-700">Open task board</router-link>
        </div>

        <div v-if="attentionTasks.length" class="divide-y divide-slate-200">
          <div
            v-for="task in attentionTasks"
            :key="task.id"
            class="grid grid-cols-1 gap-2 px-5 py-4 md:grid-cols-[2fr_1fr_1fr_1fr] md:items-center hover:bg-slate-50"
          >
            <div>
              <p class="text-sm font-semibold text-slate-900">{{ task.title }}</p>
              <p class="mt-1 text-xs text-slate-500">{{ task.description || 'No description' }}</p>
            </div>
            <span class="text-xs text-slate-600">{{ formatStatus(task.status) }}</span>
            <span class="text-xs font-medium" :class="priorityClass(task.priority)">{{ capitalize(task.priority) }} priority</span>
            <span class="text-xs" :class="task.overdue ? 'text-red-600 font-semibold' : 'text-amber-600 font-semibold'">
              {{ task.overdue ? 'Overdue' : 'Due Soon' }}
            </span>
          </div>
        </div>

        <div v-else class="px-5 py-8 text-center text-sm text-slate-500">No overdue or due-soon tasks.</div>
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
          <h2 class="text-lg font-semibold text-slate-900">Recently Completed</h2>
          <router-link to="/tasks" class="text-sm font-medium text-blue-600 hover:text-blue-700">View all tasks</router-link>
        </div>

        <div v-if="recentCompleted.length" class="divide-y divide-slate-200">
          <div
            v-for="task in recentCompleted"
            :key="task.id"
            class="grid grid-cols-1 gap-2 px-5 py-4 md:grid-cols-[2fr_1fr_1fr] md:items-center hover:bg-slate-50"
          >
            <div>
              <p class="text-sm font-semibold text-slate-900">{{ task.title }}</p>
              <p class="mt-1 text-xs text-slate-500">{{ task.description || 'No description' }}</p>
            </div>
            <span class="text-xs text-slate-600">Completed</span>
            <span class="text-xs text-slate-500">Updated: {{ formatDate(task.updated_at) }}</span>
          </div>
        </div>

        <div v-else class="px-5 py-8 text-center text-sm text-slate-500">No recently completed tasks.</div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useTaskStore } from '../stores/taskStore';

const taskStore = useTaskStore();
const authState = window.TaskAppAuth ?? {
  user: null,
};

onMounted(async () => {
  await taskStore.fetchAllTasks(true);
});

const managedTasks = computed(() => {
  const userId = authState?.user?.id;
  if (!userId) return taskStore.tasks;

  return taskStore.tasks.filter((task) => {
    const createdByManager = Number(task.creator_id) === Number(userId);
    const assignedToManager = Array.isArray(task.assignees)
      && task.assignees.some((assignee) => Number(assignee.id) === Number(userId));

    return createdByManager || assignedToManager;
  });
});

const totalTasks = computed(() => managedTasks.value.length);
const pendingCount = computed(() => managedTasks.value.filter((task) => task.status === 'pending').length);
const inProgressCount = computed(() => managedTasks.value.filter((task) => task.status === 'in_progress').length);
const completedCount = computed(() => managedTasks.value.filter((task) => task.status === 'completed').length);
const canCreateTasks = computed(() => ['admin', 'manager'].includes(authState?.user?.role));

const isOverdueTask = (task) => {
  if (!task?.due_at || task.status === 'completed') return false;
  const dueDate = new Date(task.due_at);
  if (Number.isNaN(dueDate.getTime())) return false;
  return dueDate < new Date();
};

const isDueSoonTask = (task) => {
  if (!task?.due_at || task.status === 'completed') return false;
  const now = new Date();
  const dueDate = new Date(task.due_at);
  if (Number.isNaN(dueDate.getTime())) return false;
  const threeDaysLater = new Date(now);
  threeDaysLater.setDate(threeDaysLater.getDate() + 3);
  return dueDate >= now && dueDate <= threeDaysLater;
};

const overdueCount = computed(() => managedTasks.value.filter(isOverdueTask).length);
const dueSoonCount = computed(() => managedTasks.value.filter(isDueSoonTask).length);
const openTasksCount = computed(() => managedTasks.value.filter((task) => task.status !== 'completed').length);

const completedThisWeek = computed(() => {
  const sevenDaysAgo = new Date();
  sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

  return managedTasks.value.filter((task) => {
    if (task.status !== 'completed') return false;
    const updatedAt = new Date(task.updated_at || task.created_at);
    if (Number.isNaN(updatedAt.getTime())) return false;
    return updatedAt >= sevenDaysAgo;
  }).length;
});

const percentage = (value) => {
  if (!totalTasks.value) return 0;
  return Math.round((value / totalTasks.value) * 100);
};

const completedPercent = computed(() => percentage(completedCount.value));
const inProgressPercent = computed(() => percentage(inProgressCount.value));
const pendingPercent = computed(() => percentage(pendingCount.value));
const overduePercent = computed(() => percentage(overdueCount.value));
const completionRate = computed(() => completedPercent.value);

const chartRadius = 72;
const chartCircumference = 2 * Math.PI * chartRadius;

const completedArc = computed(() => (completedPercent.value / 100) * chartCircumference);
const inProgressArc = computed(() => (inProgressPercent.value / 100) * chartCircumference);
const pendingArc = computed(() => (pendingPercent.value / 100) * chartCircumference);

const inProgressOffset = computed(() => -completedArc.value);
const pendingOffset = computed(() => -(completedArc.value + inProgressArc.value));

const weeklyActivity = computed(() => {
  const buckets = [];
  const now = new Date();

  for (let index = 6; index >= 0; index -= 1) {
    const date = new Date(now);
    date.setHours(0, 0, 0, 0);
    date.setDate(date.getDate() - index);

    buckets.push({
      key: date.toISOString().slice(0, 10),
      label: date.toLocaleDateString('en-US', { weekday: 'short' }),
      count: 0,
    });
  }

  const bucketMap = new Map(buckets.map((bucket) => [bucket.key, bucket]));

  managedTasks.value.forEach((task) => {
    const source = task.updated_at || task.created_at;
    if (!source) return;

    const taskDate = new Date(source);
    if (Number.isNaN(taskDate.getTime())) return;

    const key = taskDate.toISOString().slice(0, 10);
    const bucket = bucketMap.get(key);

    if (bucket) {
      bucket.count += 1;
    }
  });

  return buckets;
});

const maxWeeklyActivity = computed(() => {
  return Math.max(1, ...weeklyActivity.value.map((day) => day.count));
});

const attentionTasks = computed(() => {
  const priorityOrder = { high: 0, medium: 1, low: 2 };

  return managedTasks.value
    .filter((task) => isOverdueTask(task) || isDueSoonTask(task))
    .map((task) => ({
      ...task,
      overdue: isOverdueTask(task),
    }))
    .sort((firstTask, secondTask) => {
      if (firstTask.overdue !== secondTask.overdue) {
        return firstTask.overdue ? -1 : 1;
      }

      return (priorityOrder[firstTask.priority] ?? 3) - (priorityOrder[secondTask.priority] ?? 3);
    })
    .slice(0, 10);
});

const recentCompleted = computed(() => {
  return managedTasks.value
    .filter((task) => task.status === 'completed')
    .sort((firstTask, secondTask) => {
      const firstDate = new Date(firstTask.updated_at || firstTask.created_at).getTime();
      const secondDate = new Date(secondTask.updated_at || secondTask.created_at).getTime();
      return secondDate - firstDate;
    })
    .slice(0, 8);
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

const formatDate = (value) => {
  if (!value) return 'No date';

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return 'No date';

  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};
</script>
