<script setup lang="ts">
import ApplicationLogo from '@/Components/branding/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/language/LanguageSwitcher.vue';
import NavLink from '@/Components/navigation/NavLink.vue';
import ThemeToggle from '@/Components/theme/ThemeToggle.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const isOpen = ref(false);
const page = usePage();

// Check if current route is the landing page
const isLandingPage = computed(() => {
  return (
    page.url === '/' || page.url === '' || page.component === 'website/Landing'
  );
});
</script>

<template>
  <header
    class="dark:bg-surface-800 fixed top-0 right-0 left-0 z-50 mb-32 bg-white shadow-sm"
  >
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="shrink-0">
          <NavLink
            href="/"
            class="text-2xl font-bold text-gray-800 dark:text-white"
          >
            <ApplicationLogo></ApplicationLogo>
          </NavLink>
        </div>

        <!-- Mobile menu button - only show on landing page -->
        <button
          v-if="isLandingPage"
          @click="isOpen = !isOpen"
          class="rounded-md p-2 text-gray-900 md:hidden dark:text-gray-400"
        >
          <span class="sr-only">Open menu</span>
          <i
            class="pi"
            :class="isOpen ? 'pi-times' : 'pi-bars'"
            style="font-size: 1.5rem"
          ></i>
        </button>

        <!-- Desktop navigation - only show on landing page -->
        <nav v-if="isLandingPage" class="hidden space-x-8 md:flex">
          <a
            href="#features"
            class="dark:hover:text-secondary-400 px-4 py-2 text-gray-600 hover:rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
          >
            {{ t('website.nav.features') }}
          </a>
          <a
            href="#pricing"
            class="dark:hover:text-secondary-400 px-4 py-2 text-gray-600 hover:rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
          >
            {{ t('website.nav.pricing') }}
          </a>
          <a
            href="#support"
            class="dark:hover:text-secondary-400 px-4 py-2 text-gray-600 hover:rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
          >
            {{ t('website.nav.support') }}
          </a>
          <a
            href="#contact"
            class="dark:hover:text-secondary-400 px-4 py-2 text-gray-600 hover:rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
          >
            {{ t('website.nav.contact') }}
          </a>
        </nav>

        <!-- Right side controls -->
        <div class="hidden items-center gap-3 md:flex">
          <NavLink
            class="rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700"
            href="/login"
            ><i class="pi pi-user" style="font-size: 1.2rem"></i
          ></NavLink>
          <LanguageSwitcher />
          <ThemeToggle />
        </div>

        <!-- Mobile controls for non-landing pages -->
        <div v-if="!isLandingPage" class="flex items-center gap-3 md:hidden">
          <NavLink
            class="rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700"
            href="/login"
            ><i class="pi pi-user" style="font-size: 1.2rem"></i
          ></NavLink>
          <LanguageSwitcher />
          <ThemeToggle />
        </div>
      </div>
    </div>

    <!-- Mobile menu modal - only show on landing page -->
    <Transition
      enter-active-class="duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="duration-100 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isOpen && isLandingPage"
        class="absolute inset-x-0 top-16 origin-top-right transform p-2 transition md:hidden"
      >
        <div
          class="ring-opacity-5 divide-y-2 divide-gray-50 rounded-lg bg-white shadow-lg ring-1 ring-black dark:divide-gray-600 dark:bg-gray-700"
        >
          <div class="px-5 pt-5 pb-6">
            <div class="flex flex-col gap-1 space-y-4">
              <a
                href="/"
                class="hover:text-primary-600 dark:hover:text-primary-400 block text-gray-600 dark:text-gray-300"
              >
                {{ t('website.nav.home') }}
              </a>
              <a
                href="#features"
                class="hover:text-primary-600 dark:hover:text-primary-400 block text-gray-600 dark:text-gray-300"
              >
                {{ t('website.nav.features') }}
              </a>
              <a
                href="#pricing"
                class="hover:text-primary-600 dark:hover:text-primary-400 block text-gray-600 dark:text-gray-300"
              >
                {{ t('website.nav.pricing') }}
              </a>
              <a
                href="#support"
                class="hover:text-primary-600 dark:hover:text-primary-400 block text-gray-600 dark:text-gray-300"
              >
                {{ t('website.nav.support') }}
              </a>
              <a
                href="#contact"
                class="hover:text-primary-600 dark:hover:text-primary-400 block text-gray-600 dark:text-gray-300"
              >
                {{ t('website.nav.contact') }}
              </a>
            </div>
            <div class="mt-6 flex items-center justify-between gap-4">
              <NavLink class="rounded-md shadow-xs" href="/login">
                <i class="pi pi-user" style="font-size: 1.2rem"></i>
              </NavLink>
              <LanguageSwitcher />
              <ThemeToggle />
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </header>
</template>

<style>
html {
  scroll-behavior: smooth;
}
</style>
