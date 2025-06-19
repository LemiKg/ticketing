<script setup lang="ts">
import { useCrud } from '@/Composables/useCrud';
import { useDataTable } from '@/Composables/useDataTable';
import { useFlashMessages } from '@/Composables/useFlashMessages';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatDate } from '@/utils/dateUtils';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

import Button from 'primevue/button';
import Card from 'primevue/card';
import Checkbox from 'primevue/checkbox';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';

interface Role {
  id: number;
  name: string;
  permissions: Permission[];
  users?: any[];
  created_at: string;
}

interface Permission {
  id: number;
  name: string;
}

interface PaginatedRoles {
  data: Role[];
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
}

interface Props {
  roles: PaginatedRoles;
  permissions: Record<string, Permission[]>;
  filters: {
    search?: string;
    sortField?: string;
    sortOrder?: string;
  };
}

const props = defineProps<Props>();

const { t: $t } = useI18n();
const crud = useCrud({
  resourceName: 'role',
  resourceRouteName: 'roles',
  i18nPrefix: 'roles',
});

const {
  loading,
  filters,
  localSortField,
  localSortOrder,
  onFilter,
  onPage,
  onSort,
  syncWithProps,
  updateFilter,
} = useDataTable<Role>(props.roles, {
  routeName: 'roles.index',
  initialSortField: props.filters.sortField || 'name',
  initialSortOrder: (props.filters.sortOrder as 'asc' | 'desc') || 'asc',
  initialFilters: {
    search: props.filters.search,
  },
});

useFlashMessages({ i18nPrefix: 'roles' });

const showQuickAssignModal = ref(false);
const selectedRole = ref<Role | null>(null);
const selectedPermissions = ref<string[]>([]);
const updatingPermissions = ref(false);

const groupedPermissions = computed(() => props.permissions);

const isSystemRole = (roleName: string): boolean => {
  const systemRoles = [
    'super-admin',
    'timehive_admin',
    'timehive_sales',
    'admin',
    'employee',
  ];
  return systemRoles.includes(roleName);
};

const clearFilters = () => {
  updateFilter('global', '');
  onFilter();
};

const openQuickAssignModal = (role: Role) => {
  selectedRole.value = role;
  selectedPermissions.value = role.permissions.map((p) => p.name);
  showQuickAssignModal.value = true;
};

const closeQuickAssignModal = () => {
  showQuickAssignModal.value = false;
  selectedRole.value = null;
  selectedPermissions.value = [];
  updatingPermissions.value = false;
};

const updateRolePermissions = async () => {
  if (!selectedRole.value) return;

  updatingPermissions.value = true;

  try {
    await router.post(
      route('roles.syncPermissions', selectedRole.value.id),
      { permissions: selectedPermissions.value },
      {
        preserveState: true,
        onSuccess: () => {
          closeQuickAssignModal();
        },
      },
    );
  } catch (error) {
    console.error('Failed to update permissions:', error);
  } finally {
    updatingPermissions.value = false;
  }
};

watch(
  () => [
    props.filters.sortField,
    props.filters.sortOrder,
    props.filters.search,
  ],
  () => {
    syncWithProps({
      sortField: props.filters.sortField || 'name',
      sortOrder: (props.filters.sortOrder as 'asc' | 'desc') || 'asc',
      search: props.filters.search,
    });
  },
  { immediate: true, deep: true },
);
</script>

<template>
  <Head :title="$t('roles.title')" />

  <AuthenticatedLayout>
    <div class="mx-auto max-w-7xl p-4">
      <!-- Page Header -->
      <div
        class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            {{ $t('roles.title') }}
          </h1>
          <p class="mt-1 text-gray-600">{{ $t('roles.subtitle') }}</p>
        </div>
        <Button
          :label="$t('roles.create')"
          icon="pi pi-plus"
          @click="crud.navigateToCreate()"
          class="w-full sm:w-auto"
        />
      </div>

      <!-- Search and Filters -->
      <Card class="mb-6">
        <template #content>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="flex flex-col">
              <label
                for="searchRoles"
                class="mb-2 text-sm font-medium text-gray-700"
              >
                {{ $t('roles.search') }}
              </label>
              <InputText
                id="searchRoles"
                v-model="filters.global.value"
                :placeholder="$t('roles.search_placeholder')"
                class="w-full"
                @input="onFilter"
              />
            </div>
            <div class="flex items-end">
              <Button
                :label="$t('common.clear_filters')"
                icon="pi pi-filter-slash"
                severity="secondary"
                outlined
                @click="clearFilters"
                class="w-full"
              />
            </div>
          </div>
        </template>
      </Card>

      <!-- Roles Data Table -->
      <Card>
        <template #content>
          <DataTable
            :value="roles.data"
            :loading="loading"
            lazy
            paginator
            :rows="roles.per_page || 15"
            :totalRecords="roles.total"
            @page="onPage($event)"
            @sort="onSort($event)"
            sortMode="single"
            :sortField="localSortField"
            :sortOrder="localSortOrder === 'asc' ? 1 : -1"
            dataKey="id"
            class="p-datatable-sm"
            showGridlines
            stripedRows
            responsiveLayout="scroll"
          >
            <Column
              field="name"
              :header="$t('roles.name')"
              :sortable="true"
              class="min-w-48"
            >
              <template #body="{ data }">
                <div class="flex items-center gap-2">
                  <i class="pi pi-users text-blue-600"></i>
                  <span class="font-medium">{{ data.name }}</span>
                  <Tag
                    v-if="isSystemRole(data.name)"
                    :value="$t('roles.system_role')"
                    severity="info"
                    class="text-xs"
                  />
                </div>
              </template>
            </Column>

            <Column :header="$t('roles.permissions')" class="min-w-64">
              <template #body="{ data }">
                <div class="flex flex-wrap gap-1">
                  <Tag
                    v-for="permission in data.permissions.slice(0, 3)"
                    :key="permission.id"
                    :value="permission.name"
                    severity="secondary"
                    class="text-xs"
                  />
                  <Tag
                    v-if="data.permissions.length > 3"
                    :value="`+${data.permissions.length - 3} more`"
                    severity="contrast"
                    class="text-xs"
                  />
                </div>
              </template>
            </Column>

            <Column
              field="users_count"
              :header="$t('roles.users_count')"
              :sortable="true"
              class="w-32"
            >
              <template #body="{ data }">
                <div class="flex items-center gap-2">
                  <i class="pi pi-user text-gray-500"></i>
                  <span>{{ data.users?.length || 0 }}</span>
                </div>
              </template>
            </Column>

            <Column
              field="created_at"
              :header="$t('common.created_at')"
              :sortable="true"
              class="w-40"
            >
              <template #body="{ data }">
                {{ formatDate(data.created_at) }}
              </template>
            </Column>

            <Column :header="$t('common.actions')" class="w-32">
              <template #body="{ data }">
                <div class="flex gap-2">
                  <Button
                    icon="pi pi-eye"
                    size="small"
                    severity="info"
                    outlined
                    v-tooltip.top="$t('common.view')"
                    @click="crud.viewItem(data.id)"
                  />
                  <Button
                    icon="pi pi-pencil"
                    size="small"
                    severity="warning"
                    outlined
                    v-tooltip.top="$t('common.edit')"
                    @click="crud.editItem(data.id)"
                    :disabled="data.name === 'super-admin'"
                  />
                  <Button
                    icon="pi pi-trash"
                    size="small"
                    severity="danger"
                    outlined
                    v-tooltip.top="$t('common.delete')"
                    @click="crud.confirmDelete(data.id)"
                    :disabled="isSystemRole(data.name)"
                  />
                </div>
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>

      <!-- Role Quick Assignment Modal -->
      <Dialog
        v-model:visible="showQuickAssignModal"
        :header="$t('roles.quick_assign_permissions')"
        :style="{ width: '50rem' }"
        :maximizable="true"
        :modal="true"
      >
        <div v-if="selectedRole" class="space-y-4">
          <div class="rounded-lg bg-blue-50 p-4">
            <h4 class="mb-2 font-medium text-blue-900">
              {{ $t('roles.editing_role') }}: {{ selectedRole.name }}
            </h4>
            <p class="text-sm text-blue-700">
              {{ $t('roles.quick_assign_description') }}
            </p>
          </div>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div
              v-for="(groupPermissions, group) in groupedPermissions"
              :key="group"
              class="rounded-lg border p-4"
            >
              <h5 class="mb-3 font-semibold text-gray-800 capitalize">
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
                    v-model="selectedPermissions"
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

          <div class="flex justify-end gap-3 border-t pt-4">
            <Button
              :label="$t('common.cancel')"
              severity="secondary"
              outlined
              @click="closeQuickAssignModal"
            />
            <Button
              :label="$t('roles.update_permissions')"
              icon="pi pi-save"
              :loading="updatingPermissions"
              @click="updateRolePermissions"
            />
          </div>
        </div>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>
