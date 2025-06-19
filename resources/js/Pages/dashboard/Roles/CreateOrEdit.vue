<script setup lang="ts">
import { useFlashMessages } from '@/Composables/useFlashMessages';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type {
  Permission,
  Role,
  RoleFormData,
  RoleFormErrors,
} from '@/types/interfaces';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

import Button from 'primevue/button';
import Card from 'primevue/card';
import Checkbox from 'primevue/checkbox';
import Divider from 'primevue/divider';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';

interface Props {
  role?: Role;
  permissions: Record<string, Permission[]>;
  formMethod: 'post' | 'put';
  formActionUrl: string;
}

const props = defineProps<Props>();

const { t: $t } = useI18n();
useFlashMessages({ i18nPrefix: 'roles' });

const form = useForm<RoleFormData>({
  name: props.role?.name || '',
  permissions: props.role?.permissions?.map((p) => p.name) || [],
});

// Type-safe errors accessor
const formErrors = computed(() => form.errors as RoleFormErrors);

const pageTitle = computed(() =>
  props.role ? $t('roles.edit_role') : $t('roles.create_role'),
);

const pageSubtitle = computed(() =>
  props.role
    ? $t('roles.edit_role_description')
    : $t('roles.create_role_description'),
);

const submitButtonLabel = computed(() =>
  props.role ? $t('roles.update_role') : $t('roles.create_role'),
);

const isSystemRole = computed(() => {
  if (!props.role) return false;
  const systemRoles = ['timehive_admin', 'timehive_sales', 'admin', 'employee'];
  return systemRoles.includes(props.role.name);
});

const selectedPermissionsCount = computed(() => form.permissions.length);

const totalPermissionsCount = computed(() => {
  return Object.values(props.permissions).flat().length;
});

const submit = () => {
  if (props.formMethod === 'put' && props.role?.id) {
    form.put(props.formActionUrl, {
      preserveScroll: true,
    });
  } else {
    form.post(props.formActionUrl, {
      preserveScroll: true,
    });
  }
};

const getGroupIcon = (group: string): string => {
  const icons: Record<string, string> = {
    users: 'pi pi-users',
    roles: 'pi pi-user-edit',
    permissions: 'pi pi-key',
    general: 'pi pi-cog',
  };
  return icons[group] || 'pi pi-circle';
};

const getGroupPermissionsCount = (group: string): string => {
  const groupPerms = props.permissions[group] || [];
  const selectedInGroup = groupPerms.filter((p) =>
    form.permissions.includes(p.name),
  ).length;
  return `${selectedInGroup}/${groupPerms.length}`;
};

const selectAllPermissions = () => {
  form.permissions = Object.values(props.permissions)
    .flat()
    .map((p) => p.name);
};

const clearAllPermissions = () => {
  form.permissions = [];
};

const selectGroupPermissions = (group: string) => {
  const groupPermissions = props.permissions[group] || [];
  const groupPermissionNames = groupPermissions.map((p) => p.name);

  groupPermissionNames.forEach((permName) => {
    if (!form.permissions.includes(permName)) {
      form.permissions.push(permName);
    }
  });
};

const clearGroupPermissions = (group: string) => {
  const groupPermissions = props.permissions[group] || [];
  const groupPermissionNames = groupPermissions.map((p) => p.name);

  form.permissions = form.permissions.filter(
    (permName: string) => !groupPermissionNames.includes(permName),
  );
};
</script>

<template>
  <Head :title="pageTitle" />

  <AuthenticatedLayout>
    <div class="mx-auto max-w-4xl p-4">
      <!-- Page Header -->
      <div class="mb-6 flex items-center gap-4">
        <Button
          icon="pi pi-arrow-left"
          severity="secondary"
          outlined
          @click="router.visit('roles.index')"
          v-tooltip.top="$t('common.back')"
        />
        <div>
          <h1 class="text-2xl font-bold text-gray-900">{{ pageTitle }}</h1>
          <p class="mt-1 text-gray-600">{{ pageSubtitle }}</p>
        </div>
      </div>

      <!-- Role Form -->
      <Card>
        <template #content>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Role Name Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <div class="lg:col-span-1">
                <h3 class="mb-2 text-lg font-semibold text-gray-900">
                  {{ $t('roles.basic_information') }}
                </h3>
                <p class="text-sm text-gray-600">
                  {{ $t('roles.basic_information_description') }}
                </p>
              </div>

              <div class="space-y-4 lg:col-span-2">
                <div>
                  <FloatLabel>
                    <InputText
                      id="roleName"
                      v-model="form.name"
                      :class="{ 'p-invalid': formErrors.name }"
                      class="w-full"
                      :disabled="isSystemRole"
                    />
                    <label for="roleName">{{ $t('roles.name') }} *</label>
                  </FloatLabel>
                  <small v-if="formErrors.name" class="p-error mt-1 block">
                    {{ formErrors.name }}
                  </small>
                  <small v-if="isSystemRole" class="mt-1 block text-orange-600">
                    {{ $t('roles.system_role_name_readonly') }}
                  </small>
                </div>

                <div v-if="role" class="rounded-lg bg-gray-50 p-4">
                  <div class="mb-2 flex items-center gap-2">
                    <i class="pi pi-info-circle text-blue-600"></i>
                    <span class="font-medium text-gray-900">{{
                      $t('roles.role_statistics')
                    }}</span>
                  </div>
                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="text-gray-600"
                        >{{ $t('roles.permissions_count') }}:</span
                      >
                      <span class="ml-1 font-medium">{{
                        selectedPermissionsCount
                      }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600"
                        >{{ $t('roles.users_assigned') }}:</span
                      >
                      <span class="ml-1 font-medium">{{
                        role.users?.length || 0
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <Divider />

            <!-- Permissions Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <div class="lg:col-span-1">
                <h3 class="mb-2 text-lg font-semibold text-gray-900">
                  {{ $t('roles.permissions') }}
                </h3>
                <p class="mb-4 text-sm text-gray-600">
                  {{ $t('roles.permissions_description') }}
                </p>

                <!-- Permission Summary -->
                <div class="rounded-lg bg-blue-50 p-3">
                  <div class="mb-2 flex items-center gap-2">
                    <i class="pi pi-shield text-blue-600"></i>
                    <span class="font-medium text-blue-900">{{
                      $t('roles.selected_permissions')
                    }}</span>
                  </div>
                  <div class="text-sm text-blue-800">
                    {{ selectedPermissionsCount }} {{ $t('roles.of') }}
                    {{ totalPermissionsCount }}
                    {{ $t('roles.permissions_selected') }}
                  </div>
                  <div class="mt-2 space-y-1">
                    <Button
                      :label="$t('roles.select_all')"
                      size="small"
                      severity="info"
                      text
                      @click="selectAllPermissions"
                      class="p-1 text-xs"
                    />
                    <Button
                      :label="$t('roles.clear_all')"
                      size="small"
                      severity="secondary"
                      text
                      @click="clearAllPermissions"
                      class="ml-2 p-1 text-xs"
                    />
                  </div>
                </div>
              </div>

              <div class="lg:col-span-2">
                <div class="space-y-4">
                  <div
                    v-for="(groupPermissions, group) in permissions"
                    :key="group"
                    class="rounded-lg border border-gray-200 p-4"
                  >
                    <!-- Group Header -->
                    <div class="mb-3 flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <i
                          :class="getGroupIcon(group)"
                          class="text-gray-600"
                        ></i>
                        <h4 class="font-semibold text-gray-800 capitalize">
                          {{ $t(`permissions.groups.${group}`, group) }}
                        </h4>
                        <span
                          class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-500"
                        >
                          {{ getGroupPermissionsCount(group) }}
                        </span>
                      </div>
                      <div class="flex gap-2">
                        <Button
                          :label="$t('roles.select_all')"
                          size="small"
                          severity="info"
                          text
                          @click="selectGroupPermissions(group)"
                          class="text-xs"
                        />
                        <Button
                          :label="$t('roles.clear')"
                          size="small"
                          severity="secondary"
                          text
                          @click="clearGroupPermissions(group)"
                          class="text-xs"
                        />
                      </div>
                    </div>

                    <!-- Group Permissions -->
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                      <div
                        v-for="permission in groupPermissions"
                        :key="permission.id"
                        class="flex items-center rounded p-2 hover:bg-gray-50"
                      >
                        <Checkbox
                          :id="`perm-${permission.id}`"
                          v-model="form.permissions"
                          :value="permission.name"
                        />
                        <label
                          :for="`perm-${permission.id}`"
                          class="ml-2 flex-1 cursor-pointer text-sm"
                        >
                          {{ permission.name }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
                <small v-if="formErrors.permissions" class="p-error mt-2 block">
                  {{ formErrors.permissions }}
                </small>
              </div>
            </div>

            <Divider />

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4">
              <Button
                :label="$t('common.cancel')"
                severity="secondary"
                outlined
                @click="router.visit('roles.index')"
                type="button"
              />
              <Button
                :label="submitButtonLabel"
                icon="pi pi-save"
                :loading="form.processing"
                type="submit"
              />
            </div>
          </form>
        </template>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
