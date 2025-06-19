<template>
  <Button
    type="button"
    @click="toggleTheme"
    class="h-8 w-8 cursor-pointer rounded-full p-2"
  >
    <span v-if="!isDark" class="pi pi-sun text-gray-900"></span>
    <span v-else class="pi pi-moon"></span>
  </Button>
</template>

<script setup lang="ts">
import Button from 'primevue/button';
import { onMounted, ref, watch } from 'vue';

const isDark = ref(false);

const toggleTheme = () => {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    isDark.value = true;
    document.documentElement.classList.add('dark');
  }
});

watch(isDark, (newVal) => {
  localStorage.setItem('theme', newVal ? 'dark' : 'light');
});
</script>

<style lang="pcss">
* {
  @apply transition-colors;
}
</style>
