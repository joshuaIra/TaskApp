<template>
  <div>
    <p class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ label }}</p>

    <div :class="containerClass || 'flex flex-wrap gap-2'">
      <button
        v-for="option in options"
        :key="option.value"
        type="button"
        :class="buttonClass(option)"
        @click="toggle(option.value)"
      >
        <slot name="icon" :selected="isSelected(option.value)" :option="option" />

        <slot name="label" :option="option" :selected="isSelected(option.value)">
          {{ option.label }}
        </slot>
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: [String, Number, Array], default: '' },
  label: { type: String, default: '' },
  options: { type: Array, default: () => [] },
  multiple: { type: Boolean, default: false },
  containerClass: { type: String, default: '' },
  baseBtn: {
    type: String,
    default: 'px-4 py-2.5 rounded-xl border text-sm font-medium transition-all duration-200',
  },
})

const emit = defineEmits(['update:modelValue'])

const isSelected = (value) => {
  if (props.multiple) {
    return Array.isArray(props.modelValue) && props.modelValue.includes(value)
  }

  return props.modelValue === value
}

const buttonClass = (option) => {
  const selected = isSelected(option.value)

  if (selected) {
    return `${props.baseBtn} ${option.activeClass || 'bg-blue-100 text-blue-700 border-blue-500 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-500'}`
  }

  return `${props.baseBtn} border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700`
}

const toggle = (value) => {
  if (!props.multiple) {
    emit('update:modelValue', value)
    return
  }

  const current = Array.isArray(props.modelValue) ? [...props.modelValue] : []
  const idx = current.indexOf(value)

  if (idx >= 0) {
    current.splice(idx, 1)
  } else {
    current.push(value)
  }

  emit('update:modelValue', current)
}
</script>
