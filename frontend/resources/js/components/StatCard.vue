<template>
  <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 transition-all duration-200" :class="hoverBorderClass">
    <div class="flex items-start justify-between gap-4">
      <div>
        <p class="text-xs text-gray-400 mb-2">{{ label }}</p>
        <p class="text-3xl font-bold text-white">{{ value }}</p>
      </div>

      <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="iconBgClass">
        <slot name="icon" />
      </div>
    </div>

    <div class="mt-4">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  value: {
    type: [String, Number],
    default: 0,
  },
  hoverBorderClass: {
    type: String,
    default: '',
  },
  iconBgClass: {
    type: String,
    default: 'bg-gray-700/40',
  },
})
</script>
