<template>
  <div>
    <i :class="`pi ${icon} mb-8`" style="font-size: 3rem"></i>

    <h4 class="mb-1 text-2xl font-bold text-gray-800 dark:text-white">
      {{ title }}
    </h4>
    <p v-if="!link" class="text-base text-gray-800 dark:text-white">
      {{ description }}
    </p>
    <a
      v-else
      href="#"
      class="text-primary text-b text-gray-800 underline dark:text-white"
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
