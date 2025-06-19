<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
<template>
  <div class="font-sans text-gray-800 dark:text-white">
    <p class="mb-6 text-lg leading-relaxed font-bold">
      {{ t('website.contact.contactMeta.title') }}
    </p>
    <ul class="mb-6 list-inside list-disc text-base">
      <li>
        {{ t('website.contact.contactMeta.item1') }}
      </li>
      <li>
        {{ t('website.contact.contactMeta.item2') }}
      </li>
      <li>
        {{ t('website.contact.contactMeta.item3') }}
      </li>
      <li>
        {{ t('website.contact.contactMeta.item4') }}
      </li>
      <li>
        {{ t('website.contact.contactMeta.item5') }}
      </li>
      <li>
        {{ t('website.contact.contactMeta.item6') }}
      </li>
    </ul>
    <p class="mb-2 text-base font-bold text-gray-800 dark:text-gray-400">
      {{ t('website.contact.contactMeta.item7') }}
    </p>
    <p class="dark:text-secondary-400 text-base font-bold text-gray-800">
      {{ t('website.contact.contactMeta.item8') }}
    </p>
  </div>
</template>
