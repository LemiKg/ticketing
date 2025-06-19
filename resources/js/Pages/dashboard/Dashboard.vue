<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types/interfaces';
import { Head } from '@inertiajs/vue3';
import Card from 'primevue/card';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  user: User;
}>();

const { t: $t } = useI18n();
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2
        class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200"
      >
        {{ $t('common.dashboard') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          <Card class="dark:border-primary-900 dark:border">
            <template #title>
              <div class="flex items-center justify-between">
                <span
                  class="text-lg font-semibold text-gray-700 dark:text-gray-300"
                  >{{ $t('common.welcome') }}</span
                >
                <i class="pi pi-user text-primary-500 text-2xl"></i>
              </div>
            </template>
            <template #content>
              <p class="text-gray-900 dark:text-gray-100">
                {{ $t('common.welcome_message') }}, {{ user.name }}!
              </p>
              <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ new Date().toLocaleDateString() }}
              </p>
            </template>
          </Card>

          <Card class="dark:border-primary-900 dark:border">
            <template #title>
              <div class="flex items-center justify-between">
                <span
                  class="text-lg font-semibold text-gray-700 dark:text-gray-300"
                  >{{ $t('common.quick_start') }}</span
                >
                <i class="pi pi-bolt text-primary-500 text-2xl"></i>
              </div>
            </template>
            <template #content>
              <p class="text-gray-900 dark:text-gray-100">
                {{ $t('common.quick_start_message') }}
              </p>
            </template>
          </Card>

          <Card class="dark:border-primary-900 dark:border">
            <template #title>
              <div class="flex items-center justify-between">
                <span
                  class="text-lg font-semibold text-gray-700 dark:text-gray-300"
                  >{{ $t('common.system_status') }}</span
                >
                <i class="pi pi-check-circle text-green-500 text-2xl"></i>
              </div>
            </template>
            <template #content>
              <p class="text-green-600 dark:text-green-400 font-semibold">
                {{ $t('common.system_operational') }}
              </p>
            </template>
          </Card>
        </div>

        <!-- Main content area for future customization -->
        <Card class="dark:border-primary-900 dark:border">
          <template #title>
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
              {{ $t('common.getting_started') }}
            </h3>
          </template>
          <template #content>
            <div class="space-y-4">
              <p class="text-gray-600 dark:text-gray-400">
                {{ $t('common.boilerplate_message') }}
              </p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                  <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">
                    {{ $t('common.frontend_stack') }}
                  </h4>
                  <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>• Vue 3 + TypeScript</li>
                    <li>• Inertia.js</li>
                    <li>• PrimeVue Components</li>
                    <li>• Tailwind CSS</li>
                    <li>• Vue I18n</li>
                  </ul>
                </div>
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                  <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">
                    {{ $t('common.backend_stack') }}
                  </h4>
                  <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>• Laravel 11</li>
                    <li>• Service Layer Architecture</li>
                    <li>• Spatie Permissions</li>
                    <li>• Laravel Breeze Auth</li>
                    <li>• Docker Ready</li>
                  </ul>
                </div>
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
