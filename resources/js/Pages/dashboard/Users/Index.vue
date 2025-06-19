<script setup lang="ts">
import ClickableName from '@/Components/base/ClickableName.vue';
import ResponsiveActionButtons from '@/Components/base/ResponsiveActionButtons.vue';
import { useCrud } from '@/Composables/useCrud';
import { useDataTable } from '@/Composables/useDataTable';
import { useFlashMessages } from '@/Composables/useFlashMessages';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FilterValue, PaginatedData, User } from '@/types/interfaces';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Tooltip from 'primevue/tooltip';
import { watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();
const vTooltip = Tooltip;

const props = defineProps<{
  users: PaginatedData<User>;
  sortField: string;
  sortOrder: string;
  searchName: string | null;
  searchEmail: string | null;
}>();

const dataTable = useDataTable<User>(props.users, {
  initialSortField: props.sortField || 'id',
  initialSortOrder: props.sortOrder || 'asc',
  initialFilters: {
    searchName: props.searchName,
    searchEmail: props.searchEmail,
  },
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
  refreshData,
} = dataTable;

const crudActions = useCrud({
  resourceRouteName: 'users',
  i18nPrefix: 'users',
});

const { navigateToCreate } = crudActions;

useFlashMessages({
  i18nPrefix: 'users',
});

watch(
  () => [props.sortField, props.sortOrder, props.searchName, props.searchEmail],
  ([newSortField, newSortOrder, newSearchName, newSearchEmail]) => {
    syncWithProps({
      sortField: newSortField,
      sortOrder: newSortOrder,
      searchName: newSearchName,
      searchEmail: newSearchEmail,
    });
  },
  { immediate: true },
);

const clearFilters = () => {
  updateFilter('global', '');
  updateFilter('searchEmail', '');
  refreshData();
};

// Create menu items for the responsive action buttons
const createActionMenuItems = (user: User) => [
  {
    label: $t('users.view'),
    icon: 'pi pi-eye',
    command: () => crudActions.viewItem(user.id),
  },
  {
    label: $t('users.edit'),
    icon: 'pi pi-pencil',
    command: () => crudActions.editItem(user.id),
  },
  {
    label: $t('users.delete'),
    icon: 'pi pi-trash',
    command: () => crudActions.confirmDelete(user.id),
  },
];
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Users" />
    <div class="mx-auto">
      <Card class="dark:border-primary-900 mb-4 dark:border">
        <template #title>
          <div class="flex items-center justify-between">
            <span class="text-xl">{{ $t('users.title') }}</span>
            <div class="flex gap-2">
              <Button
                :label="$t('users.clear_filters')"
                icon="pi pi-filter-slash"
                severity="secondary"
                @click="clearFilters"
              />
              <Button
                :label="$t('users.create')"
                icon="pi pi-plus"
                @click="navigateToCreate"
              />
            </div>
          </div>
        </template>
        <template #content>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="flex flex-col">
              <label
                for="searchName"
                class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                {{ $t('users.search_name') }}
              </label>
              <InputText
                id="searchName"
                :model-value="(filters.global as FilterValue)?.value || ''"
                @input="
                  (e: Event) =>
                    updateFilter('global', (e.target as HTMLInputElement).value)
                "
                :placeholder="$t('users.search_name_placeholder')"
                class="w-full"
              />
            </div>
            <div class="flex flex-col">
              <label
                for="searchEmail"
                class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                {{ $t('users.search_email') }}
              </label>
              <InputText
                id="searchEmail"
                :model-value="
                  typeof filters.searchEmail === 'string'
                    ? filters.searchEmail
                    : ''
                "
                @input="
                  (e: Event) =>
                    updateFilter(
                      'searchEmail',
                      (e.target as HTMLInputElement).value,
                    )
                "
                :placeholder="$t('users.search_email_placeholder')"
                class="w-full"
              />
            </div>
          </div>
        </template>
      </Card>

      <Card class="dark:border-primary-900 dark:border">
        <template #content>
          <DataTable
            :value="users.data"
            :loading="loading"
            lazy
            paginator
            responsiveLayout="scroll"
            breakpoint="768px"
            :rows="users.per_page || 15"
            :totalRecords="users.total"
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
              field="id"
              :header="$t('users.id')"
              :sortable="true"
              style="width: 15%"
            ></Column>
            <Column
              field="name"
              :header="$t('users.name')"
              :sortable="true"
              style="width: 30%"
            >
              <template #body="{ data }">
                <ClickableName
                  :displayName="data.name"
                  :onClick="() => crudActions.viewItem(data.id)"
                />
              </template>
            </Column>
            <Column
              field="email"
              :header="$t('users.email')"
              :sortable="true"
              style="width: 30%"
            ></Column>
            <Column
              field="status"
              :header="$t('users.status')"
              style="width: 15%"
            >
              <template #body="{ data }">
                <span
                  :class="{
                    'text-green-600': data.status === 'active',
                    'text-yellow-600': data.status === 'inactive',
                    'text-red-600': data.status === 'suspended',
                  }"
                  class="font-medium capitalize"
                >
                  {{ data.status || 'active' }}
                </span>
              </template>
            </Column>
            <Column
              :header="$t('users.actions')"
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
                    v-tooltip.top="$t('users.view')"
                    @click="crudActions.viewItem(data.id)"
                  />
                  <Button
                    icon="pi pi-pencil"
                    class="p-button-rounded p-button-warning p-button-text"
                    v-tooltip.top="$t('users.edit')"
                    @click="crudActions.editItem(data.id)"
                  />
                  <Button
                    icon="pi pi-trash"
                    class="p-button-rounded p-button-danger p-button-text"
                    v-tooltip.top="$t('users.delete')"
                    @click="crudActions.confirmDelete(data.id)"
                  />
                </ResponsiveActionButtons>
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>
    </div>

    <ConfirmDialog />
  </AuthenticatedLayout>
</template>
