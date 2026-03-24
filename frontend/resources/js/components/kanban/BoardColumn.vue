<template>
  <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-3">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-2">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ title }}</h2>
        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
          {{ tasks.length }}
        </span>
      </div>
    </div>

    <draggable
      :list="localList"
      group="tasks"
      item-key="id"
      class="min-h-[220px] space-y-3"
      @change="handleChange"
    >
      <template #item="{ element }">
        <TaskCard :task="element" />
      </template>

      <template #footer>
        <div class="text-xs text-gray-400 dark:text-gray-500 pt-2">
          Drop tasks here
        </div>
      </template>
    </draggable>
  </div>
</template>

<script setup>
import { computed } from "vue";
import draggable from "vuedraggable";
import TaskCard from "../TaskCard.vue";

const props = defineProps({
  title: { type: String, required: true },
  status: { type: String, required: true },
  tasks: { type: Array, required: true },
});

const emit = defineEmits(["move"]);

// vuedraggable mutates the list it receives; we pass a computed array.
// (It’s okay because we handle the move through store in @change)
const localList = computed(() => props.tasks);

const handleChange = (evt) => {
  // Only react when a task is added to this column
  if (evt?.added?.element?.id) {
    emit("move", { taskId: evt.added.element.id, toStatus: props.status });
  }
};
</script>