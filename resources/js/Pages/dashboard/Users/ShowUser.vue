<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types/interfaces';
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();

const props = defineProps<{
  user: User;
}>();

const formatDate = (dateString: string | undefined): string => {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleString();
};

const editUser = () => {
  router.visit(route('users.edit', props.user.id));
};

const backToList = () => {
  router.visit(route('users.index'));
};

// Helper functions for user actions

// Additional helper functions could go here
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="$t('users.view_user')" />
    <div class="mx-auto max-w-7xl">
      <Card class="dark:border-primary-900 mb-4 shadow-xl dark:border">
        <template #title>
          <div class="flex items-center justify-between">
            <span class="text-xl">{{ $t('users.user_details') }}</span>
            <div>
              <Button
                :label="$t('users.edit')"
                icon="pi pi-pencil"
                class="p-button-warning mr-2"
                @click="editUser"
              />
              <Button
                :label="$t('users.back_to_list')"
                icon="pi pi-arrow-left"
                class="p-button-secondary"
                @click="backToList"
              />
            </div>
          </div>
        </template>
        <template #content>
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Basic Information -->
            <div>
              <h3
                class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300"
              >
                {{ $t('users.basic_information') }}
              </h3>

              <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700">
                <div class="grid grid-cols-1 gap-4">
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $t('users.id') }}
                    </p>
                    <p class="text-base font-medium">{{ user.id }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $t('users.name') }}
                    </p>
                    <p class="text-base font-medium">{{ user.name }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $t('users.email') }}
                    </p>
                    <p class="text-base font-medium">{{ user.email }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Information -->
            <div>
              <h3
                class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300"
              >
                Additional Information
              </h3>

              <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700">
                <div class="grid grid-cols-1 gap-4">
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      Member Since
                    </p>
                    <p class="text-base font-medium">
                      {{ formatDate(user.created_at) }}
                    </p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      Email Status
                    </p>
                    <p
                      class="text-base font-medium"
                      :class="
                        user.email_verified_at
                          ? 'text-green-500'
                          : 'text-red-500'
                      "
                    >
                      {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Additional User Actions -->
          <div class="mt-6">
            <h3
              class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300"
            >
              Quick Actions
            </h3>
            <div class="flex gap-2">
              <Button
                label="Edit User"
                icon="pi pi-pencil"
                class="p-button-secondary"
                @click="editUser"
              />
            </div>
          </div>

          <!-- Timestamps -->
          <div class="mt-6">
            <h3
              class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300"
            >
              {{ $t('users.timestamps') }}
            </h3>

            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700">
              <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('users.created_at') }}
                  </p>
                  <p class="text-base font-medium">
                    {{ formatDate(user.created_at) }}
                  </p>
                </div>
                <div>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('users.updated_at') }}
                  </p>
                  <p class="text-base font-medium">
                    {{ formatDate(user.updated_at) }}
                  </p>
                </div>
                <div v-if="user.email_verified_at">
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('users.email_verified_at') }}
                  </p>
                  <p class="text-base font-medium">
                    {{ formatDate(user.email_verified_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
