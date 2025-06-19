<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
  if (input.value?.hasAttribute('autofocus')) {
    input.value?.focus();
  }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
  <input
    class="focus:border-secondary-500 focus:ring-secondary-500 dark:focus:border-secondary-600 dark:focus:ring-secondary-600 dark:border-primary-900 rounded-md border-gray-300 bg-gray-100 shadow-xs dark:bg-gray-900 dark:text-gray-300"
    v-model="model"
    ref="input"
  />
</template>
