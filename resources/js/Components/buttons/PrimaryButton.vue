<script lang="ts" setup>
import Button from 'primevue/button';

defineProps<{
  type?: 'submit' | 'button' | 'reset';
}>();
</script>

<template>
  <Button :type="type" class="transition duration-150 ease-in-out">
    <slot />
  </Button>
</template>
