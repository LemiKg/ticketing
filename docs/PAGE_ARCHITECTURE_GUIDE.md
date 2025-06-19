# TimeHive Architecture Guide

## Creating Pages Following Existing Patterns

This guide provides comprehensive documentation on how to create new pages in the TimeHive project that follow the established architecture patterns. Use this as a reference for maintaining consistency across the application.

## Table of Contents

1. [Project Structure Overview](#project-structure-overview)
2. [Core Composables](#core-composables)
3. [Base Components](#base-components)
4. [Page Structure Patterns](#page-structure-patterns)
5. [Form Handling Patterns](#form-handling-patterns)
6. [Data Table Patterns](#data-table-patterns)
7. [Navigation and Layout](#navigation-and-layout)
8. [Error Handling and Notifications](#error-handling-and-notifications)
9. [Internationalization](#internationalization)
10. [Best Practices](#best-practices)

## Project Structure Overview

The TimeHive project follows a Vue 3 + TypeScript + Inertia.js architecture with these key directories:

```
resources/js/
├── Components/
│   ├── base/           # Reusable base components
│   └── ui/             # UI-specific components
├── Composables/        # Vue composables for shared logic
├── Layouts/            # Layout components
├── Pages/              # Page components (Inertia.js pages)
│   └── dashboard/      # Dashboard pages
├── types/              # TypeScript type definitions
└── utils/              # Utility functions
```

## Core Composables

### 1. useCrud Composable

The `useCrud` composable provides standardized CRUD operations for any resource.

**Usage:**
```typescript
import { useCrud } from '@/Composables/useCrud';

const crud = useCrud({
  resourceRouteName: 'users',    // Laravel route name prefix
  i18nPrefix: 'users'           // Translation key prefix
});

// Available methods:
crud.navigateToCreate()          // Navigate to create page
crud.viewItem(id)               // Navigate to show page
crud.editItem(id)               // Navigate to edit page
crud.confirmDelete(id)          // Show delete confirmation
```

**Options Interface:**
```typescript
interface CrudOptions {
  resourceRouteName: string;     // Laravel route name (e.g., 'users')
  i18nPrefix: string;           // Translation prefix (e.g., 'users')
}
```

### 2. useDataTable Composable

Handles data table functionality including pagination, filtering, and sorting.

**Usage:**
```typescript
import { useDataTable } from '@/Composables/useDataTable';

const {
  filters,
  searchTerm,
  handleFilter,
  clearFilters
} = useDataTable();
```

### 3. useFlashMessages Composable

Manages flash messages and notifications from server responses.

**Usage:**
```typescript
import { useFlashMessages } from '@/Composables/useFlashMessages';

// Automatically handles flash messages from Inertia.js
useFlashMessages();
```

### 4. useForm (Inertia.js)

Handles form state, validation, and submission.

**Usage:**
```typescript
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  // ... other form fields
});

const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      // Handle success
    }
  });
};
```

## Base Components

### 1. CrudModal Component

A generic modal component for CRUD operations.

**Usage:**
```vue
<template>
  <CrudModal
    v-model:visible="showModal"
    :item="selectedItem"
    :resource-route-name="'users'"
    :i18n-prefix="'users'"
    @hide="showModal = false"
  >
    <template #default="{ form, isEditing }">
      <!-- Form fields go here -->
      <InputText v-model="form.name" :placeholder="$t('users.name')" />
      <InputText v-model="form.email" :placeholder="$t('users.email')" />
    </template>
  </CrudModal>
</template>
```

**Props:**
```typescript
interface CrudModalProps {
  visible: boolean;
  item?: any;                    // Item to edit (null for create)
  resourceRouteName: string;     // Laravel route name
  i18nPrefix: string;           // Translation prefix
  modalStyle?: string;          // Custom modal styles
  maximizable?: boolean;        // Allow modal maximization
  dismissableMask?: boolean;    // Allow dismissal by clicking mask
}
```

### 2. FilterableSelect Component

A dropdown component with filtering capabilities.

**Usage:**
```vue
<FilterableSelect
  v-model="selectedValue"
  :options="options"
  option-label="name"
  option-value="id"
  :placeholder="$t('select_option')"
/>
```

### 3. ResponsiveActionButtons Component

Responsive action buttons that adapt to screen size.

**Usage:**
```vue
<ResponsiveActionButtons
  :actions="[
    { label: 'Edit', icon: 'pi pi-pencil', action: () => crud.editItem(item.id) },
    { label: 'Delete', icon: 'pi pi-trash', action: () => crud.confirmDelete(item.id), severity: 'danger' }
  ]"
/>
```

## Page Structure Patterns

### Standard Page Template

```vue
<template>
  <Head :title="$t('page.title')" />
  
  <AuthenticatedLayout>
    <div class="container mx-auto p-4">
      <!-- Page Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $t('page.title') }}</h1>
        <Button
          :label="$t('actions.create')"
          icon="pi pi-plus"
          @click="crud.navigateToCreate()"
        />
      </div>

      <!-- Filters/Search -->
      <Card class="mb-6">
        <template #content>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <InputText
              v-model="searchTerm"
              :placeholder="$t('search_placeholder')"
              icon="pi pi-search"
            />
            <!-- Additional filters -->
          </div>
        </template>
      </Card>

      <!-- Data Table -->
      <DataTable
        :value="data.data"
        :loading="loading"
        :rows="data.per_page"
        :totalRecords="data.total"
        lazy
        paginator
        @page="handlePageChange"
      >
        <!-- Table columns -->
      </DataTable>

      <!-- Modal/Forms -->
      <CrudModal
        v-model:visible="showModal"
        :item="selectedItem"
        :resource-route-name="'resource-name'"
        :i18n-prefix="'resource'"
        @hide="showModal = false"
      >
        <!-- Form content -->
      </CrudModal>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { useCrud } from '@/Composables/useCrud';
import { useDataTable } from '@/Composables/useDataTable';
import { useFlashMessages } from '@/Composables/useFlashMessages';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

// Props from Inertia.js
interface Props {
  data: PaginatedData<YourModelType>;
  filters?: any;
}

const props = defineProps<Props>();

// Composables
const { t: $t } = useI18n();
const crud = useCrud({
  resourceRouteName: 'your-resource',
  i18nPrefix: 'your_resource'
});
const { searchTerm, handleFilter } = useDataTable();
useFlashMessages();

// Reactive state
const showModal = ref(false);
const selectedItem = ref(null);
const loading = ref(false);

// Methods
const handlePageChange = (event: any) => {
  // Handle pagination
};
</script>
```

## Form Handling Patterns

### Basic Form Pattern

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

interface FormData {
  name: string;
  email: string;
  // ... other fields
}

const form = useForm<FormData>({
  name: '',
  email: '',
});

const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      // Reset form or redirect
      form.reset();
    },
    onError: (errors) => {
      // Handle validation errors
      console.error('Validation errors:', errors);
    }
  });
};
</script>

<template>
  <form @submit.prevent="submit">
    <div class="space-y-4">
      <div>
        <label for="name">{{ $t('users.name') }}</label>
        <InputText
          id="name"
          v-model="form.name"
          :class="{ 'p-invalid': form.errors.name }"
        />
        <small v-if="form.errors.name" class="p-error">
          {{ form.errors.name }}
        </small>
      </div>
      
      <!-- More fields... -->
      
      <Button
        type="submit"
        :label="$t('actions.save')"
        :loading="form.processing"
      />
    </div>
  </form>
</template>
```

### Form with File Upload

```vue
<script setup lang="ts">
const form = useForm({
  name: '',
  avatar: null as File | null,
});

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    form.avatar = target.files[0];
  }
};
</script>

<template>
  <FileUpload
    mode="basic"
    accept="image/*"
    :maxFileSize="1000000"
    @select="handleFileUpload"
  />
</template>
```

## Data Table Patterns

### Standard Data Table with Actions

```vue
<template>
  <DataTable
    :value="data.data"
    :loading="loading"
    :rows="data.per_page"
    :totalRecords="data.total"
    lazy
    paginator
    :rowsPerPageOptions="[10, 25, 50]"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :currentPageReportTemplate="$t('table.pagination_info')"
    @page="handlePageChange"
    @sort="handleSort"
  >
    <Column field="name" :header="$t('users.name')" sortable />
    <Column field="email" :header="$t('users.email')" sortable />
    <Column field="created_at" :header="$t('common.created_at')" sortable>
      <template #body="{ data }">
        {{ formatDate(data.created_at) }}
      </template>
    </Column>
    
    <!-- Actions Column -->
    <Column :header="$t('common.actions')" class="w-32">
      <template #body="{ data }">
        <ResponsiveActionButtons
          :actions="[
            {
              label: $t('actions.view'),
              icon: 'pi pi-eye',
              action: () => crud.viewItem(data.id)
            },
            {
              label: $t('actions.edit'),
              icon: 'pi pi-pencil',
              action: () => editItem(data)
            },
            {
              label: $t('actions.delete'),
              icon: 'pi pi-trash',
              action: () => crud.confirmDelete(data.id),
              severity: 'danger'
            }
          ]"
        />
      </template>
    </Column>
  </DataTable>
</template>
```

### Table with Custom Filters

```vue
<template>
  <!-- Filter Card -->
  <Card class="mb-6">
    <template #content>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <InputText
          v-model="filters.search"
          :placeholder="$t('search_placeholder')"
          @input="handleFilter"
        />
        
        <FilterableSelect
          v-model="filters.status"
          :options="statusOptions"
          option-label="label"
          option-value="value"
          :placeholder="$t('filters.status')"
          @change="handleFilter"
        />
        
        <DatePicker
          v-model="filters.date_from"
          :placeholder="$t('filters.date_from')"
          @date-select="handleFilter"
        />
        
        <Button
          :label="$t('actions.clear_filters')"
          icon="pi pi-filter-slash"
          class="p-button-outlined"
          @click="clearFilters"
        />
      </div>
    </template>
  </Card>
</template>
```

## Navigation and Layout

### Layout Structure

All pages should use the `AuthenticatedLayout` component:

```vue
<template>
  <Head :title="$t('page.title')" />
  
  <AuthenticatedLayout>
    <!-- Page content -->
  </AuthenticatedLayout>
</template>
```

### Navigation with Breadcrumbs

```vue
<template>
  <AuthenticatedLayout>
    <!-- Breadcrumbs -->
    <Breadcrumb :model="breadcrumbItems" class="mb-4" />
    
    <!-- Page content -->
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
const breadcrumbItems = [
  { label: $t('dashboard.title'), route: 'dashboard' },
  { label: $t('users.title'), route: 'users.index' },
  { label: $t('users.create') }
];
</script>
```

## Error Handling and Notifications

### Flash Messages

Flash messages are automatically handled by the `useFlashMessages` composable. Just include it in your component:

```typescript
useFlashMessages();
```

### Manual Notifications

```typescript
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const showSuccess = (message: string) => {
  toast.add({
    severity: 'success',
    summary: $t('common.success'),
    detail: message,
    life: 3000
  });
};

const showError = (message: string) => {
  toast.add({
    severity: 'error',
    summary: $t('common.error'),
    detail: message,
    life: 5000
  });
};
```

### Confirmation Dialogs

```typescript
import { useConfirm } from 'primevue/useconfirm';

const confirm = useConfirm();

const confirmAction = () => {
  confirm.require({
    message: $t('common.confirm_message'),
    header: $t('common.confirmation'),
    icon: 'pi pi-exclamation-triangle',
    accept: () => {
      // Perform action
    }
  });
};
```

## Internationalization

### Translation Keys Structure

Follow this naming convention for translation keys:

```
resource_name:
  title: "Resource Title"
  create: "Create Resource"
  edit: "Edit Resource"
  delete_confirm: "Are you sure you want to delete this resource?"
  fields:
    name: "Name"
    email: "Email"
    created_at: "Created At"
```

### Using Translations

```vue
<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();
</script>

<template>
  <h1>{{ $t('users.title') }}</h1>
  <Button :label="$t('users.create')" />
</template>
```

## Best Practices

### 1. Component Organization

- Keep components focused and single-purpose
- Use composition API for all new components
- Extract reusable logic into composables
- Use TypeScript interfaces for props and data structures

### 2. State Management

- Use reactive references for local state
- Leverage Inertia.js for server state
- Use composables for shared logic between components

### 3. Performance

- Use `lazy` loading for data tables with large datasets
- Implement proper pagination
- Use `v-memo` for expensive computations when needed

### 4. Accessibility

- Include proper ARIA labels
- Use semantic HTML elements
- Ensure keyboard navigation works
- Provide meaningful error messages

### 5. Error Handling

- Always handle form validation errors
- Provide user-friendly error messages
- Use try-catch blocks for async operations
- Log errors for debugging

### 6. Code Style

- Follow Vue 3 Composition API patterns
- Use TypeScript for type safety
- Implement proper prop validation
- Use meaningful variable and function names

## Example: Complete User Management Page

```vue
<template>
  <Head :title="$t('users.title')" />
  
  <AuthenticatedLayout>
    <div class="container mx-auto p-4">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $t('users.title') }}</h1>
        <Button
          :label="$t('users.create')"
          icon="pi pi-plus"
          @click="showCreateModal"
        />
      </div>

      <!-- Filters -->
      <Card class="mb-6">
        <template #content>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <InputText
              v-model="searchTerm"
              :placeholder="$t('users.search_placeholder')"
              @input="handleFilter"
            />
            <FilterableSelect
              v-model="filters.role"
              :options="roleOptions"
              option-label="name"
              option-value="id"
              :placeholder="$t('users.filter_by_role')"
              @change="handleFilter"
            />
            <Button
              :label="$t('actions.clear_filters')"
              icon="pi pi-filter-slash"
              class="p-button-outlined"
              @click="clearFilters"
            />
          </div>
        </template>
      </Card>

      <!-- Data Table -->
      <DataTable
        :value="users.data"
        :loading="loading"
        :rows="users.per_page"
        :totalRecords="users.total"
        lazy
        paginator
        @page="handlePageChange"
      >
        <Column field="name" :header="$t('users.name')" sortable />
        <Column field="email" :header="$t('users.email')" sortable />
        <Column field="role.name" :header="$t('users.role')" />
        <Column :header="$t('common.actions')" class="w-32">
          <template #body="{ data }">
            <ResponsiveActionButtons
              :actions="[
                {
                  label: $t('actions.edit'),
                  icon: 'pi pi-pencil',
                  action: () => editUser(data)
                },
                {
                  label: $t('actions.delete'),
                  icon: 'pi pi-trash',
                  action: () => crud.confirmDelete(data.id),
                  severity: 'danger'
                }
              ]"
            />
          </template>
        </Column>
      </DataTable>

      <!-- User Modal -->
      <CrudModal
        v-model:visible="showModal"
        :item="selectedUser"
        :resource-route-name="'users'"
        :i18n-prefix="'users'"
        @hide="hideModal"
      >
        <template #default="{ form, isEditing }">
          <div class="space-y-4">
            <div>
              <label for="name">{{ $t('users.name') }}</label>
              <InputText
                id="name"
                v-model="form.name"
                :class="{ 'p-invalid': form.errors.name }"
              />
              <small v-if="form.errors.name" class="p-error">
                {{ form.errors.name }}
              </small>
            </div>
            
            <div>
              <label for="email">{{ $t('users.email') }}</label>
              <InputText
                id="email"
                v-model="form.email"
                type="email"
                :class="{ 'p-invalid': form.errors.email }"
              />
              <small v-if="form.errors.email" class="p-error">
                {{ form.errors.email }}
              </small>
            </div>

            <div>
              <label for="role">{{ $t('users.role') }}</label>
              <FilterableSelect
                id="role"
                v-model="form.role_id"
                :options="roleOptions"
                option-label="name"
                option-value="id"
                :class="{ 'p-invalid': form.errors.role_id }"
              />
              <small v-if="form.errors.role_id" class="p-error">
                {{ form.errors.role_id }}
              </small>
            </div>
          </div>
        </template>
      </CrudModal>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { useCrud } from '@/Composables/useCrud';
import { useDataTable } from '@/Composables/useDataTable';
import { useFlashMessages } from '@/Composables/useFlashMessages';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PaginatedData, User, Role } from '@/types/interfaces';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
  users: PaginatedData<User>;
  roles: Role[];
  filters?: any;
}

const props = defineProps<Props>();

// Composables
const { t: $t } = useI18n();
const crud = useCrud({
  resourceRouteName: 'users',
  i18nPrefix: 'users'
});
const { 
  filters, 
  searchTerm, 
  handleFilter, 
  clearFilters 
} = useDataTable();
useFlashMessages();

// State
const showModal = ref(false);
const selectedUser = ref<User | null>(null);
const loading = ref(false);

// Computed
const roleOptions = computed(() => props.roles);

// Methods
const showCreateModal = () => {
  selectedUser.value = null;
  showModal.value = true;
};

const editUser = (user: User) => {
  selectedUser.value = user;
  showModal.value = true;
};

const hideModal = () => {
  showModal.value = false;
  selectedUser.value = null;
};

const handlePageChange = (event: any) => {
  // Handle pagination logic
};
</script>
```

This guide provides a comprehensive foundation for creating new pages that follow the established TimeHive architecture patterns. Always refer to existing code examples and maintain consistency with the established patterns throughout the application.
