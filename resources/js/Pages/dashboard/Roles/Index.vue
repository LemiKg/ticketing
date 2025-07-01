<script setup lang="ts">
import ClickableName from '@/Components/base/ClickableName.vue';
import QuickPermissionsModal from '@/Components/Roles/QuickPermissionsModal.vue';
import ResponsiveActionButtons from '@/Components/base/ResponsiveActionButtons.vue';
import { useCrud } from '@/Composables/useCrud';
import { useDataTable } from '@/Composables/useDataTable';
import { useFlashMessages } from '@/Composables/useFlashMessages';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ActionMenuItem, PaginatedData } from '@/types/interfaces';
import { formatDate } from '@/utils/dateUtils';
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Tooltip from 'primevue/tooltip';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

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

interface Props {
  roles: PaginatedData<Role>;
  permissions: Record<string, Permission[]>;
  filters: {
    search?: string;
    sortField?: string;
    sortOrder?: string;
  };
}

const { t: $t } = useI18n();
const vTooltip = Tooltip;

const props = defineProps<Props>();

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

const searchValue = computed({
  get: () => {
    const searchFilter = (filters.value as any)?.search;
    return searchFilter?.value || '';
  },
  set: (value: string) => {
    updateFilter('search', value);
  },
});

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
  updateFilter('search', '');
  onFilter();
};

// Create menu items for the responsive action buttons
const createActionMenuItems = (role: Role): ActionMenuItem[] => [
  {
    label: $t('common.view'),
    icon: 'pi pi-eye',
    command: () => crud.viewItem(role.id),
  },
  {
    label: $t('common.edit'),
    icon: 'pi pi-pencil',
    command: () => crud.editItem(role.id),
  },
  {
    label: $t('roles.manage_permissions'),
    icon: 'pi pi-cog',
    command: () => openQuickAssignModal(role),
  },
  {
    label: $t('common.delete'),
    icon: 'pi pi-trash',
    command: () => crud.confirmDelete(role.id),
  },
];

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
    <Head :title="$t('roles.title')" />
    <div class="mx-auto">
      <!-- Page Header and Filters Card -->
      <Card class="dark:border-primary-900 mb-4 dark:border">
        <template #title>
          <div class="flex items-center justify-between">
            <div>
              <span class="text-xl">{{ $t('roles.title') }}</span>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                {{ $t('roles.subtitle') }}
              </p>
            </div>
            <div class="flex gap-2">
              <Button
                :label="$t('common.clear_filters')"
                icon="pi pi-filter-slash"
                severity="secondary"
                @click="clearFilters"
              />
              <Button
                :label="$t('roles.create')"
                icon="pi pi-plus"
                @click="crud.navigateToCreate()"
              />
            </div>
          </div>
        </template>
        <template #content>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="flex flex-col">
              <label
                for="searchRoles"
                class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                {{ $t('roles.search') }}
              </label>
              <InputText
                id="searchRoles"
                v-model="searchValue"
                :placeholder="$t('roles.search_placeholder')"
                class="w-full"
                @input="onFilter"
              />
            </div>
          </div>
        </template>
      </Card>

      <!-- Roles Data Table -->
      <Card class="dark:border-primary-900 dark:border">
        <template #content>
          <DataTable
            :value="roles.data"
            :loading="loading"
            lazy
            paginator
            responsiveLayout="scroll"
            breakpoint="768px"
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
          >
            <Column
              field="name"
              :header="$t('roles.name')"
              :sortable="true"
              style="width: 25%"
            >
              <template #body="{ data }">
                <div class="flex items-center gap-2">
                  <i class="pi pi-users text-blue-600"></i>
                  <ClickableName
                    :displayName="data.name"
                    :onClick="() => crud.viewItem(data.id)"
                  />
                  <Tag
                    v-if="isSystemRole(data.name)"
                    :value="$t('roles.system_role')"
                    severity="info"
                    class="text-xs"
                  />
                </div>
              </template>
            </Column>

            <Column :header="$t('roles.permissions')" style="width: 35%">
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
              style="width: 15%"
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
              style="width: 15%"
            >
              <template #body="{ data }">
                {{ formatDate(data.created_at) }}
              </template>
            </Column>

            <Column
              :header="$t('common.actions')"
              style="width: 20%; text-align: center"
              :exportable="false"
            >
              <template #body="{ data }">
                <ResponsiveActionButtons
                  :menuItems="createActionMenuItems(data)"
                >
                  <Button
                    icon="pi pi-eye"
                    class="p-button-rounded p-button-info p-button-text"
                    v-tooltip.top="$t('common.view')"
                    @click="crud.viewItem(data.id)"
                  />
                  <Button
                    icon="pi pi-pencil"
                    class="p-button-rounded p-button-warning p-button-text"
                    v-tooltip.top="$t('common.edit')"
                    @click="crud.editItem(data.id)"
                    :disabled="data.name === 'super-admin'"
                  />
                  <Button
                    icon="pi pi-cog"
                    class="p-button-rounded p-button-secondary p-button-text"
                    v-tooltip.top="$t('roles.manage_permissions')"
                    @click="openQuickAssignModal(data)"
                  />
                  <Button
                    icon="pi pi-trash"
                    class="p-button-rounded p-button-danger p-button-text"
                    v-tooltip.top="$t('common.delete')"
                    @click="crud.confirmDelete(data.id)"
                    :disabled="isSystemRole(data.name)"
                  />
                </ResponsiveActionButtons>
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>

      <!-- Role Quick Assignment Modal -->
      <QuickPermissionsModal
        v-model:visible="showQuickAssignModal"
        :role="selectedRole"
        :grouped-permissions="groupedPermissions"
        v-model:selected-permissions="selectedPermissions"
        :updating="updatingPermissions"
        @hide="closeQuickAssignModal"
        @update="updateRolePermissions"
      />
    </div>

    <ConfirmDialog />
  </AuthenticatedLayout>
</template>
