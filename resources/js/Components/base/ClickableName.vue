<template>
  <button
    @click="onClick"
    class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 cursor-pointer text-left font-medium transition-colors duration-200"
  >
    {{ displayName }}
  </button>
</template>

<script setup lang="ts">
interface Props {
  displayName: string;
  onClick: () => void;
}

defineProps<Props>();
</script>
