<script setup lang="ts">
import FooterWebsite from '@/Components/base/FooterWebsite.vue';
import HeaderWebsite from '@/Components/base/HeaderWebsite.vue';
</script>

<template>
  <HeaderWebsite />
  <slot />
  <FooterWebsite />
</template>
