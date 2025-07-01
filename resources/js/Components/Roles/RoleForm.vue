<template>
  <form @submit.prevent="$emit('submit')" class="space-y-6">
    <!-- Role Name Section -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <div class="lg:col-span-1">
        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('roles.basic_information') }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">
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
          <small
            v-if="isSystemRole"
            class="mt-1 block text-orange-600 dark:text-orange-400"
          >
            {{ $t('roles.system_role_name_readonly') }}
          </small>
        </div>

        <div v-if="role" class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
          <div class="mb-2 flex items-center gap-2">
            <i class="pi pi-info-circle text-blue-600"></i>
            <span class="font-medium text-gray-900 dark:text-gray-100">{{
              $t('roles.role_statistics')
            }}</span>
          </div>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-gray-600 dark:text-gray-300"
                >{{ $t('roles.permissions_count') }}:</span
              >
              <span class="ml-1 font-medium">{{
                selectedPermissionsCount
              }}</span>
            </div>
            <div>
              <span class="text-gray-600 dark:text-gray-300"
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
        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('roles.permissions') }}
        </h3>
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
          {{ $t('roles.permissions_description') }}
        </p>

        <!-- Permission Summary -->
        <div class="rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
          <div class="mb-2 flex items-center gap-2">
            <i class="pi pi-shield text-blue-600"></i>
            <span class="font-medium text-blue-900 dark:text-blue-100">{{
              $t('roles.selected_permissions')
            }}</span>
          </div>
          <div class="text-sm text-blue-800 dark:text-blue-200">
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
            class="rounded-lg border border-gray-200 p-4 dark:border-gray-700"
          >
            <!-- Group Header -->
            <div class="mb-3 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i
                  :class="getGroupIcon(group)"
                  class="text-gray-600 dark:text-gray-300"
                ></i>
                <h4
                  class="font-semibold text-gray-800 capitalize dark:text-gray-200"
                >
                  {{ $t(`permissions.groups.${group}`, group) }}
                </h4>
                <span
                  class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-500 dark:bg-gray-700 dark:text-gray-400"
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
                class="flex items-center rounded p-2 hover:bg-gray-50 dark:hover:bg-gray-700"
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
        class="p-button-secondary p-button-outlined"
        @click="$emit('cancel')"
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

<script setup lang="ts">
import type { Permission, Role, RoleFormErrors } from '@/types/interfaces';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Divider from 'primevue/divider';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
  form: any; // Inertia form
  role?: Role;
  permissions: Record<string, Permission[]>;
  isSystemRole: boolean;
  submitButtonLabel: string;
}

defineEmits<{
  submit: [];
  cancel: [];
}>();

const props = defineProps<Props>();

const { t: $t } = useI18n();

// Type-safe errors accessor
const formErrors = computed(() => props.form.errors as RoleFormErrors);

const selectedPermissionsCount = computed(() => props.form.permissions.length);

const totalPermissionsCount = computed(() => {
  return Object.values(props.permissions).flat().length;
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

const getGroupPermissionsCount = (group: string): string => {
  const groupPerms = props.permissions[group] || [];
  const selectedInGroup = groupPerms.filter((p) =>
    props.form.permissions.includes(p.name),
  ).length;
  return `${selectedInGroup}/${groupPerms.length}`;
};

const selectAllPermissions = () => {
  props.form.permissions = Object.values(props.permissions)
    .flat()
    .map((p) => p.name);
};

const clearAllPermissions = () => {
  props.form.permissions = [];
};

const selectGroupPermissions = (group: string) => {
  const groupPermissions = props.permissions[group] || [];
  const groupPermissionNames = groupPermissions.map((p) => p.name);

  groupPermissionNames.forEach((permName) => {
    if (!props.form.permissions.includes(permName)) {
      props.form.permissions.push(permName);
    }
  });
};

const clearGroupPermissions = (group: string) => {
  const groupPermissions = props.permissions[group] || [];
  const groupPermissionNames = groupPermissions.map((p) => p.name);

  props.form.permissions = props.form.permissions.filter(
    (permName: string) => !groupPermissionNames.includes(permName),
  );
};
</script>
