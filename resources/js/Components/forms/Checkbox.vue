<script setup lang="ts">
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps<{
  checked: boolean;
  value?: any;
}>();

const proxyChecked = computed({
  get() {
    return props.checked;
  },

  set(val) {
    emit('update:checked', val);
  },
});
</script>

<template>
  <input
    type="checkbox"
    :value="value"
    v-model="proxyChecked"
    class="text-secondary-500 focus:ring-secondary-500 dark:focus:ring-secondary-500 border-primary-300 rounded-sm shadow-lg dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-offset-gray-800"
  />
</template>
