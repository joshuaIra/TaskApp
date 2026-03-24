<template>
  <div>
    <label :for="id" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
      {{ label }}<span v-if="required"> *</span>
    </label>

    <component
      :is="fieldTag"
      :id="id"
      :value="modelValue"
      :type="as === 'input' ? type : undefined"
      :rows="as === 'textarea' ? rows : undefined"
      :required="required"
      @input="onInput"
      class="w-full rounded-xl border px-4 py-2.5 text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:bg-gray-800 dark:text-white"
      :class="error
        ? 'border-red-500 focus:border-red-500 focus:ring-red-100 dark:border-red-500'
        : 'border-gray-300 dark:border-gray-700'"
    >
      <slot v-if="as === 'select'" />
    </component>

    <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  id: { type: String, required: true },
  label: { type: String, required: true },
  as: { type: String, default: 'input' },
  type: { type: String, default: 'text' },
  rows: { type: Number, default: 3 },
  required: { type: Boolean, default: false },
  error: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const fieldTag = computed(() => {
  if (props.as === 'textarea') return 'textarea'
  if (props.as === 'select') return 'select'
  return 'input'
})

const onInput = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>
