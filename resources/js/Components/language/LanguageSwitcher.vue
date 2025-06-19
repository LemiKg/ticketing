<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();

const languages = [
  { code: 'en', name: 'English', flag: '/images/flags/flagEN.png' },
  { code: 'sr', name: 'Srpski', flag: '/images/flags/flagSR.png' },
  { code: 'de', name: 'Deutsch', flag: '/images/flags/flagDE.png' },
  { code: 'it', name: 'Italiano', flag: '/images/flags/flagIT.png' },
];

const selectedLanguage = ref(
  languages.find((lang) => lang.code === locale.value) || languages[0],
);
const isOpen = ref(false);

const changeLanguage = (lang: { code: string; name: string; flag: string }) => {
  locale.value = lang.code;
  console.log(lang);
  selectedLanguage.value = lang;
  isOpen.value = false;
};
</script>

<template>
  <div class="relative">
    <button
      @click="isOpen = !isOpen"
      class="flex items-center space-x-2 rounded-md bg-white px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
    >
      <img :src="selectedLanguage.flag" alt="" class="h-4 w-6 object-cover" />

      <i style="font-size: 0.9rem" class="pi pi-chevron-down ml-1"></i>
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white py-1 shadow-lg dark:bg-gray-800"
    >
      <div
        v-for="lang in languages"
        :key="lang.code"
        class="cursor-pointer px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
        :class="{
          'bg-secondary-100 dark:bg-secondary-900':
            selectedLanguage.code === lang.code,
        }"
        @click="changeLanguage(lang)"
      >
        <div class="flex items-center space-x-2">
          <img :src="lang.flag" alt="" class="h-4 w-6 object-cover" />
          <span>{{ lang.name }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.3s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
