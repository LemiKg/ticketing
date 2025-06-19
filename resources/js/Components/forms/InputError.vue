<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  message?: string;
}>();

const { t: $t } = useI18n();

/**
 * Automatically translate validation error messages if they are translation keys
 * Translation keys are detected by checking if they match our validation key patterns
 */
const translatedMessage = computed(() => {
  if (!props.message) return '';

  const isTranslationKey =
    props.message.includes('.') &&
    (props.message.startsWith('admin.') ||
      props.message.startsWith('validation.') ||
      props.message.startsWith('auth.') ||
      props.message.startsWith('passwords.'));

  if (isTranslationKey) {
    try {
      const translated = $t(props.message);

      if (translated && translated !== props.message) {
        return translated;
      }
    } catch (error) {
      console.warn(`Translation failed for key: ${props.message}`, error);
    }
  }

  return props.message;
});
</script>

<template>
  <div v-show="message">
    <p class="text-sm text-red-600 dark:text-red-400">
      {{ translatedMessage }}
    </p>
  </div>
</template>
