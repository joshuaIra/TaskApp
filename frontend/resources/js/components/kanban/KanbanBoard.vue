<template>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    <BoardColumn
      v-for="col in columns"
      :key="col.key"
      :title="col.title"
      :status="col.key"
      :tasks="tasksByStatus(col.key)"
      @move="onMove"
    />
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useTaskStore } from "../../stores/taskStore";
import BoardColumn from "./BoardColumn.vue";

const taskStore = useTaskStore();

const columns = [
  { key: "todo", title: "Todo" },
  { key: "in_progress", title: "In Progress" },
  { key: "review", title: "Review" },
  { key: "done", title: "Done" },
];

const tasksByStatus = (status) => taskStore.tasksByStatus(status);

onMounted(async () => {
  // Safe: if your backend endpoint isn't ready, your store can still show mock tasks.
  await taskStore.fetchTasks?.();
});

const onMove = async ({ taskId, toStatus }) => {
  await taskStore.moveTask(taskId, toStatus);
};
</script>