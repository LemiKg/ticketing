<script setup lang="ts">
defineProps<{
  value?: string;
}>();
</script>

<template>
  <label class="block text-base font-bold text-gray-800 dark:text-gray-400">
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>
