<template>
  <div>
    <div class="flex items-center justify-between mb-1.5">
      <span class="text-sm text-gray-300">{{ label }}</span>
      <span class="text-xs text-gray-400">{{ count }} ({{ percent }}%)</span>
    </div>

    <div class="w-full h-2 bg-gray-700 rounded-full overflow-hidden">
      <div class="h-full transition-all duration-500" :class="barClass" :style="{ width: `${normalizedPercent}%` }"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  count: {
    type: Number,
    default: 0,
  },
  percent: {
    type: Number,
    default: 0,
  },
  barClass: {
    type: String,
    default: 'bg-blue-400',
  },
})

const normalizedPercent = computed(() => {
  if (Number.isNaN(props.percent)) return 0
  return Math.min(100, Math.max(0, props.percent))
})
</script>
