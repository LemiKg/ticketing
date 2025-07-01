<template>
  <Dialog
    v-model:visible="isVisible"
    :header="$t('roles.quick_assign_permissions')"
    :style="{ width: '50rem' }"
    :maximizable="true"
    :modal="true"
    @hide="$emit('hide')"
  >
    <div v-if="role" class="space-y-4">
      <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
        <h4 class="mb-2 font-medium text-blue-900 dark:text-blue-100">
          {{ $t('roles.editing_role') }}: {{ role.name }}
        </h4>
        <p class="text-sm text-blue-700 dark:text-blue-200">
          {{ $t('roles.quick_assign_description') }}
        </p>
      </div>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div
          v-for="(groupPermissions, group) in groupedPermissions"
          :key="group"
          class="rounded-lg border border-gray-200 p-4 dark:border-gray-700"
        >
          <h5
            class="mb-3 font-semibold text-gray-800 capitalize dark:text-gray-200"
          >
            {{ $t(`permissions.groups.${group}`, group) }}
          </h5>
          <div class="space-y-2">
            <div
              v-for="permission in groupPermissions"
              :key="permission.id"
              class="flex items-center"
            >
              <Checkbox
                :id="`perm-${permission.id}`"
                v-model="localSelectedPermissions"
                :value="permission.name"
              />
              <label
                :for="`perm-${permission.id}`"
                class="ml-2 cursor-pointer text-sm"
              >
                {{ permission.name }}
              </label>
            </div>
          </div>
        </div>
      </div>

      <div
        class="flex justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-700"
      >
        <Button
          :label="$t('common.cancel')"
          class="p-button-secondary p-button-outlined"
          @click="$emit('hide')"
        />
        <Button
          :label="$t('roles.update_permissions')"
          icon="pi pi-save"
          :loading="updating"
          @click="$emit('update')"
        />
      </div>
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import type { Permission, Role } from '@/types/interfaces';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
  visible: boolean;
  role: Role | null;
  groupedPermissions: Record<string, Permission[]>;
  selectedPermissions: string[];
  updating: boolean;
}

const emit = defineEmits<{
  hide: [];
  update: [];
  'update:selectedPermissions': [permissions: string[]];
}>();

const props = defineProps<Props>();

const { t: $t } = useI18n();

const isVisible = computed({
  get: () => props.visible,
  set: (value) => {
    if (!value) emit('hide');
  },
});

const localSelectedPermissions = computed({
  get: () => props.selectedPermissions,
  set: (value: string[]) => {
    emit('update:selectedPermissions', value);
  },
});
</script>
