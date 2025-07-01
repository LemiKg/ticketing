<script setup lang="ts">
import RoleForm from '@/Components/Roles/RoleForm.vue';
import { useFlashMessages } from '@/Composables/useFlashMessages';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Permission, Role, RoleFormData } from '@/types/interfaces';
import { Head, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Tooltip from 'primevue/tooltip';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

interface Props {
  role?: Role;
  permissions: Record<string, Permission[]>;
  formMethod: 'post' | 'put';
  formActionUrl: string;
}

const props = defineProps<Props>();

const { t: $t } = useI18n();
const vTooltip = Tooltip;
useFlashMessages({ i18nPrefix: 'roles' });

const form = useForm<RoleFormData>({
  name: props.role?.name || '',
  permissions: props.role?.permissions?.map((p) => p.name) || [],
});

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

const goBack = () => {
  router.visit(route('roles.index'));
};
</script>

<template>
  <Head :title="pageTitle" />

  <AuthenticatedLayout>
    <Head :title="pageTitle" />
    <div class="mx-auto max-w-4xl">
      <!-- Page Header -->
      <Card class="dark:border-primary-900 mb-4 dark:border">
        <template #title>
          <div class="flex items-center gap-4">
            <Button
              icon="pi pi-arrow-left"
              class="p-button-rounded p-button-secondary p-button-text"
              @click="goBack"
              v-tooltip.top="$t('common.back')"
            />
            <div>
              <span class="text-xl">{{ pageTitle }}</span>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                {{ pageSubtitle }}
              </p>
            </div>
          </div>
        </template>
      </Card>

      <!-- Role Form -->
      <Card class="dark:border-primary-900 dark:border">
        <template #content>
          <RoleForm
            :form="form"
            :role="role"
            :permissions="permissions"
            :is-system-role="isSystemRole"
            :submit-button-label="submitButtonLabel"
            @submit="submit"
            @cancel="goBack"
          />
        </template>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
