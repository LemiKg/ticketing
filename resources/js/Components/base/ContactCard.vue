<template>
  <div
    class="flex flex-col items-start gap-2 rounded-xl bg-white px-6 py-8 shadow-sm transition-shadow duration-300 hover:shadow-xl dark:bg-gray-700"
  >
    <i :class="`pi ${icon} mb-6 text-2xl`" style="font-size: 3rem"></i>
    <h4 class="mb-1 text-3xl font-bold text-gray-800 dark:text-white">
      {{ title }}
    </h4>
    <p v-if="!link" class="text-lg text-gray-800 dark:text-white">
      {{ description }}
    </p>
    <a
      v-else
      href="#"
      class="text-primary text-xl text-gray-800 underline dark:text-white"
    >
      {{ description }}
    </a>
  </div>
</template>

<script setup lang="ts">
defineProps({
  icon: String,
  title: String,
  description: String,
  link: {
    type: Boolean,
    default: false,
  },
});
</script>
