<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div
    class="from-primary-300 dark:from-primary-700 to-secondary-300 dark:to-secondary-700 flex min-h-screen flex-col items-center bg-linear-to-r pt-6 sm:justify-center sm:pt-0 dark:bg-gray-900"
  >
    <div
      class="dark:border-primary-900 mt-6 w-full overflow-hidden rounded-3xl bg-white px-6 py-8 shadow-xl sm:max-w-md dark:border dark:bg-gray-800"
    >
      <div class="flex w-full items-center justify-center">
        <Link href="/" class="w-1/3">
          <div class="py-4">
            <img
              src="/images/logo-dark.webp"
              class="flex w-full dark:hidden"
              alt="logo"
            />
            <img
              src="/images/logo-light.webp"
              class="hidden w-full dark:flex"
              alt="logo"
            />
          </div>
        </Link>
      </div>
      <slot />
    </div>
  </div>
</template>
