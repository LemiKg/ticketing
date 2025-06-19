<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatDate } from '@/utils/dateUtils';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

import Button from 'primevue/button';
import Card from 'primevue/card';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';

interface Permission {
  id: number;
  name: string;
}

interface User {
  id: number;
  name: string;
  email: string;
}

interface Role {
  id: number;
  name: string;
  permissions: Permission[];
  users?: User[];
  created_at: string;
}

interface Props {
  role: Role;
}

const props = defineProps<Props>();

const { t: $t } = useI18n();

const showAllUsers = ref(false);

const isSystemRole = computed(() => {
  const systemRoles = [
    'super-admin',
    'timehive_admin',
    'timehive_sales',
    'admin',
    'employee',
  ];
  return systemRoles.includes(props.role.name);
});

const groupedPermissions = computed(() => {
  const groups: Record<string, Permission[]> = {};

  props.role.permissions.forEach((permission) => {
    const parts = permission.name.split(' ');
    const group = parts.length > 1 ? parts[1] : 'general';

    if (!groups[group]) {
      groups[group] = [];
    }
    groups[group].push(permission);
  });

  return groups;
});

const getGroupIcon = (group: string): string => {
  const icons: Record<string, string> = {
    users: 'pi pi-users',
    roles: 'pi pi-user-edit',
    permissions: 'pi pi-key',
    general: 'pi pi-cog',
  };
  return icons[group] || 'pi pi-circle';
};
</script>

<template>
  <Head :title="$t('roles.view_role')" />

  <AuthenticatedLayout>
    <div class="mx-auto max-w-6xl p-4">
      <!-- Page Header -->
      <div class="mb-6 flex items-center gap-4">
        <Button
          icon="pi pi-arrow-left"
          severity="secondary"
          outlined
          @click="router.visit(route('roles.index'))"
          v-tooltip.top="$t('common.back')"
        />
        <div class="flex-1">
          <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">{{ role.name }}</h1>
            <Tag
              v-if="isSystemRole"
              :value="$t('roles.system_role')"
              severity="info"
            />
          </div>
          <p class="mt-1 text-gray-600">
            {{ $t('roles.view_role_description') }}
          </p>
        </div>
        <div class="flex gap-2">
          <Button
            :label="$t('common.edit')"
            icon="pi pi-pencil"
            severity="warning"
            @click="router.visit(route('roles.edit', role.id))"
            :disabled="role.name === 'super-admin'"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Role Information -->
        <div class="space-y-6 lg:col-span-1">
          <!-- Basic Information Card -->
          <Card>
            <template #title>
              <div class="flex items-center gap-2">
                <i class="pi pi-info-circle text-blue-600"></i>
                {{ $t('roles.role_information') }}
              </div>
            </template>
            <template #content>
              <div class="space-y-4">
                <div>
                  <label class="text-sm font-medium text-gray-700">{{
                    $t('roles.name')
                  }}</label>
                  <p class="font-semibold text-gray-900">{{ role.name }}</p>
                </div>

                <div>
                  <label class="text-sm font-medium text-gray-700">{{
                    $t('roles.permissions_count')
                  }}</label>
                  <p class="font-semibold text-gray-900">
                    {{ role.permissions.length }}
                  </p>
                </div>

                <div>
                  <label class="text-sm font-medium text-gray-700">{{
                    $t('roles.users_assigned')
                  }}</label>
                  <p class="font-semibold text-gray-900">
                    {{ role.users?.length || 0 }}
                  </p>
                </div>

                <div>
                  <label class="text-sm font-medium text-gray-700">{{
                    $t('common.created_at')
                  }}</label>
                  <p class="text-gray-900">{{ formatDate(role.created_at) }}</p>
                </div>

                <div v-if="isSystemRole" class="rounded-lg bg-orange-50 p-3">
                  <div class="mb-1 flex items-center gap-2">
                    <i class="pi pi-shield text-orange-600"></i>
                    <span class="font-medium text-orange-900">{{
                      $t('roles.system_role')
                    }}</span>
                  </div>
                  <p class="text-sm text-orange-800">
                    {{ $t('roles.system_role_description') }}
                  </p>
                </div>
              </div>
            </template>
          </Card>

          <!-- Users with this Role Card -->
          <Card v-if="role.users && role.users.length > 0">
            <template #title>
              <div class="flex items-center gap-2">
                <i class="pi pi-users text-green-600"></i>
                {{ $t('roles.assigned_users') }}
              </div>
            </template>
            <template #content>
              <div class="space-y-3">
                <div
                  v-for="user in role.users.slice(0, 5)"
                  :key="user.id"
                  class="flex items-center gap-3 rounded bg-gray-50 p-2"
                >
                  <div
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600"
                  >
                    <span class="text-sm font-medium text-white">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">{{ user.name }}</p>
                    <p class="text-sm text-gray-600">{{ user.email }}</p>
                  </div>
                </div>

                <div v-if="role.users.length > 5" class="text-center">
                  <Button
                    :label="
                      $t('roles.view_all_users', { count: role.users.length })
                    "
                    severity="info"
                    text
                    size="small"
                    @click="showAllUsers = true"
                  />
                </div>
              </div>
            </template>
          </Card>
        </div>

        <!-- Permissions Section -->
        <div class="lg:col-span-2">
          <Card>
            <template #title>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <i class="pi pi-shield text-purple-600"></i>
                  {{ $t('roles.permissions') }}
                  <span
                    class="rounded bg-gray-100 px-2 py-1 text-sm text-gray-500"
                  >
                    {{ role.permissions.length }}
                  </span>
                </div>
                <Button
                  :label="$t('roles.manage_permissions')"
                  icon="pi pi-cog"
                  size="small"
                  @click="router.visit(route('roles.edit', role.id))"
                  :disabled="role.name === 'super-admin'"
                />
              </div>
            </template>
            <template #content>
              <div
                v-if="role.permissions.length === 0"
                class="py-8 text-center"
              >
                <i class="pi pi-shield mb-4 text-4xl text-gray-400"></i>
                <p class="text-gray-600">{{ $t('roles.no_permissions') }}</p>
                <Button
                  :label="$t('roles.assign_permissions')"
                  icon="pi pi-plus"
                  severity="info"
                  @click="router.visit(route('roles.edit', role.id))"
                  class="mt-4"
                  :disabled="role.name === 'super-admin'"
                />
              </div>

              <div v-else class="space-y-6">
                <div
                  v-for="(groupPermissions, group) in groupedPermissions"
                  :key="group"
                  class="rounded-lg border border-gray-200 p-4"
                >
                  <!-- Group Header -->
                  <div class="mb-4 flex items-center gap-2">
                    <i :class="getGroupIcon(group)" class="text-gray-600"></i>
                    <h4 class="font-semibold text-gray-800 capitalize">
                      {{ $t(`permissions.groups.${group}`, group) }}
                    </h4>
                    <span
                      class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-500"
                    >
                      {{ groupPermissions.length }}
                    </span>
                  </div>

                  <!-- Group Permissions -->
                  <div
                    class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3"
                  >
                    <div
                      v-for="permission in groupPermissions"
                      :key="permission.id"
                      class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 p-2"
                    >
                      <i class="pi pi-check text-sm text-green-600"></i>
                      <span class="text-sm font-medium text-green-800">
                        {{ permission.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </Card>
        </div>
      </div>

      <!-- All Users Modal -->
      <Dialog
        v-model:visible="showAllUsers"
        :header="$t('roles.all_assigned_users')"
        :style="{ width: '50rem' }"
        :modal="true"
      >
        <div v-if="role.users && role.users.length > 0" class="space-y-3">
          <div
            v-for="user in role.users"
            :key="user.id"
            class="flex items-center justify-between rounded-lg border border-gray-200 p-3"
          >
            <div class="flex items-center gap-3">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600"
              >
                <span class="font-medium text-white">
                  {{ user.name.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <p class="font-medium text-gray-900">{{ user.name }}</p>
                <p class="text-sm text-gray-600">{{ user.email }}</p>
              </div>
            </div>
            <Button
              :label="$t('users.view')"
              severity="info"
              text
              size="small"
              @click="router.visit(route('users.show', user.id))"
            />
          </div>
        </div>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>
