<script setup lang="ts">
import ApplicationLogo from '@/Components/branding/ApplicationLogo.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage();

const isLandingPage = computed(() => {
  return (
    page.url === '/' || page.url === '' || page.component === 'website/Landing'
  );
});
</script>
<template>
  <footer class="bg-white pt-6 dark:bg-gray-800">
    <div class="mx-auto max-w-6xl px-4 text-center">
      <div
        class="align-center mx-auto grid grid-cols-1 gap-4 py-8 sm:grid-cols-2 md:w-full md:grid-cols-3"
      >
        <div class="flex flex-col justify-center gap-4 px-6">
          <div>
            <ApplicationLogo class="h-10 w-24" />
          </div>
          <p class="text-base text-gray-600 dark:text-gray-300">
            TimeHive is a time tracking app that helps you track your time and
            improve your productivity.
          </p>
        </div>
        <div v-if="isLandingPage" class="flex w-full flex-col gap-4 px-6">
          <h3
            class="text-start text-lg font-bold text-gray-600 dark:text-gray-300"
          >
            Quick Links
          </h3>
          <nav class="flex flex-col gap-2 text-start">
            <a
              href="#features"
              class="dark:hover:text-secondary-400 text-gray-600 hover:rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
              {{ t('website.nav.features') }}
            </a>
            <a
              href="#pricing"
              class="dark:hover:text-secondary-400 text-gray-600 hover:rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
              {{ t('website.nav.pricing') }}
            </a>
            <a
              href="#support"
              class="dark:hover:text-secondary-400 text-gray-600 hover:rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
              {{ t('website.nav.support') }}
            </a>
            <a
              href="#contact"
              class="dark:hover:text-secondary-400 text-gray-600 hover:rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
              {{ t('website.nav.contact') }}
            </a>
          </nav>
        </div>
        <div class="flex w-full flex-col gap-4 px-6 text-start">
          <h3
            class="text-start text-lg font-bold text-gray-600 dark:text-gray-300"
          >
            Contact
          </h3>
          <p class="text-gray-600 dark:text-gray-300">
            <span><i class="pi pi-envelope"></i></span>
            support@timehive.tech
          </p>
          <p class="text-gray-600 dark:text-gray-300">
            <span><i class="pi pi-phone"></i></span>
            +381 65 372 41 64
          </p>
        </div>
      </div>

      <div class="flex justify-start gap-4 border-t border-gray-400 py-4">
        <p class="text-xs text-gray-600 dark:text-gray-300">
          &copy; {{ new Date().getFullYear() }} TimeHive. All rights reserved
        </p>
        <a href="/privacy" class="text-xs text-gray-600 dark:text-gray-300"
          >Privacy Policy</a
        >
        <a href="/cookies" class="text-xs text-gray-600 dark:text-gray-300"
          >Cookie Policy</a
        >
        <a href="/terms" class="text-xs text-gray-600 dark:text-gray-300"
          >Terms and Conditions</a
        >
      </div>
    </div>
  </footer>
</template>
