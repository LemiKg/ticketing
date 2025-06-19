# TimeHive Frontend Architecture

This document describes the frontend architecture of the TimeHive application, based on the analysis of existing components. Use this as a reference when implementing new UI features.

## Table of Contents

1. [Overview](#1-overview)
2. [Technology Stack](#2-technology-stack)
3. [Directory Structure](#3-directory-structure)
4. [Component Architecture](#4-component-architecture)
5. [UI Patterns](#5-ui-patterns)
6. [Form Handling](#6-form-handling)
7. [Data Table Implementation](#7-data-table-implementation)
8. [Internationalization](#8-internationalization)
9. [Notifications and Dialogs](#9-notifications-and-dialogs)
10. [Responsive Design](#10-responsive-design)
11. [Creating New Pages](#11-creating-new-pages)

## 1. Overview

TimeHive's frontend is built with Vue 3, TypeScript, and Inertia.js, providing a modern SPA-like experience while leveraging Laravel's backend. The UI uses PrimeVue components combined with Tailwind CSS for styling, creating a consistent, responsive, and accessible user interface.

## 2. Technology Stack

- **Vue 3**: JavaScript framework using the Composition API
- **TypeScript**: For type safety and better developer experience
- **Inertia.js**: For SPA-like navigation without requiring a separate API
- **PrimeVue**: UI component library providing rich UI elements
- **Tailwind CSS**: Utility-first CSS framework for custom styling
- **Vue I18n**: For internationalization support

## 3. Directory Structure

```
resources/
├── js/
│   ├── Components/
│   │   └── EntityName/
│   │       └── EntityNameForm.vue
│   ├── Composables/
│   │   └── useDebounce.ts
│   ├── Layouts/
│   │   └── AuthenticatedLayout.vue
│   ├── Pages/
│   │   └── dashboard/
│   │       └── EntityName/
│   │           ├── Index.vue
│   │           ├── CreateOrEditEntity.vue
│   │           └── ShowEntity.vue
│   ├── app.ts
```

## 4. Component Architecture

### 4.1. Page Components

Pages are top-level components rendered by Inertia.js. They typically:

1. Use an `AuthenticatedLayout` component
2. Receive data from the backend as props
3. Implement page-specific logic
4. May delegate to smaller, reusable components
5. Use composables for common functionality like data tables and CRUD operations

Example structure:

```vue
<template>
  <AuthenticatedLayout>
    <div class="mx-auto max-w-7xl">
      <Card>
        <!-- Page content -->
      </Card>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
// Imports and implementation
</script>
```

### 4.2. Form Components

Forms are separated into their own components to promote reusability and separation of concerns. They handle:

1. Data binding
2. Validation
3. Submission to backend
4. Error display

### 4.3. Composables

Composables provide reusable functionality across components:

#### 4.3.1. Data Table Composable (`useDataTable`)

Handles all data table functionality including:

- Pagination
- Sorting
- Searching and filtering
- Debouncing filters
- Loading states

```typescript
const {
  loading, // Loading state for DataTable
  filters, // Reactive filters object
  localSortField, // Current sort field
  localSortOrder, // Current sort order ('asc' or 'desc')
  onFilter, // Handler for filter changes (with debounce)
  onPage, // Handler for page changes
  onSort, // Handler for sort changes
  updateFilter, // Update a specific filter
  syncWithProps, // Sync state with incoming props
  refreshData, // Refresh data with current parameters
} = useDataTable<EntityType>(paginatedData, {
  routeName: 'entities.index',
  initialSortField: props.sortField,
  initialSortOrder: props.sortOrder,
  initialFilters: {
    searchName: props.searchName,
  },
});
```

#### 4.3.2. CRUD Operations Composable (`useCrud`)

Handles common CRUD operations:

- Navigation to create/edit pages
- Delete operations with confirmation
- Standard entity operations

```typescript
const {
  navigateToCreate, // Navigate to create page
  editItem, // Navigate to edit page
  viewItem, // Navigate to view/show page
  confirmDelete, // Show delete confirmation dialog
} = useCrud({
  resourceName: 'entity',
  resourceRouteName: 'entities',
  i18nPrefix: 'entities',
});
```

#### 4.3.3. Flash Messages Composable (`useFlashMessages`)

Handles automatic toast notifications from flash messages:

```typescript
// Automatically shows toasts for success, error, etc.
useFlashMessages({ i18nPrefix: 'entities' });
```

## 5. UI Patterns

### 5.1. Layout Structure

- `AuthenticatedLayout`: Wrapper for authenticated pages with navigation
- Content area: Centered with max width (`mx-auto max-w-7xl`)
- Cards: Used to group related content with consistent styling

### 5.2. Common UI Components

TimeHive uses PrimeVue components:

- `Card`: Container for content sections
- `DataTable`: For displaying tabular data with sorting/pagination
- `Button`: For actions with various styles and icons
- `InputText` and other form controls: For user input
- `Toast`: For notifications
- `ConfirmDialog`: For confirmation prompts

### 5.3. Visual Hierarchy

- Page title: Text size `text-xl` with proper contrast
- Card titles: `text-lg` with flex layout for title + action buttons
- Content spacing: Consistent padding and margins with Tailwind utilities
- Responsive grids: Using Tailwind's grid system for different screen sizes

## 6. Form Handling

### 6.1. Form Implementation

Forms use Inertia.js's `useForm` composable:

```javascript
const form =
  useForm <
  EntityFormData >
  {
    property1: props.entity?.property1 || '',
    property2: props.entity?.property2 || null,
  };
```

### 6.2. Input Components

- `FloatLabel`: Used to wrap inputs for floating labels
- Form controls like `InputText`, `Textarea`, etc.
- Error display below each input field

### 6.3. Submit Handlers

```javascript
const submit = () => {
  if (props.formMethod === 'put' && props.entity?.id) {
    form.put(props.formActionUrl, {
      preserveScroll: true,
    });
  } else {
    form.post(props.formActionUrl, {
      preserveScroll: true,
    });
  }
};
```

## 7. Data Table Implementation

### 7.1. Basic Structure

```vue
<DataTable
  :value="entities.data"
  :loading="loading"
  lazy
  paginator
  :rows="entities.per_page || 15"
  :totalRecords="entities.total"
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
  <Column field="id" :header="$t('entity.id')" :sortable="true"></Column>
  <!-- Additional columns -->
</DataTable>
```

### 7.2. Using the DataTable Composable

For consistent data table implementation across pages, use the `useDataTable` composable:

```typescript
// Initialize the data table functionality
const {
  loading,
  filters,
  localSortField,
  localSortOrder,
  onFilter,
  onPage,
  onSort,
  syncWithProps,
} = useDataTable<EntityType>(props.entities, {
  routeName: 'entities.index',
  initialSortField: props.sortField,
  initialSortOrder: props.sortOrder,
  initialFilters: {
    searchName: props.searchName,
  },
});

// Keep local state in sync with props
watch(
  () => [props.sortField, props.sortOrder, props.searchName],
  () => {
    syncWithProps({
      sortField: props.sortField,
      sortOrder: props.sortOrder,
      searchName: props.searchName,
    });
  },
  { immediate: true, deep: true },
);
```

### 7.3. Server-Side Pagination and Sorting

- `lazy` mode for server-side processing
- Event handlers provided by the composable
- Data fetching handled automatically by the composable

The composable handles the implementation details:

```javascript
const refreshData = (options = {}) => {
  loading.value = true;
  router.get(
    route(options.routeName),
    {
      sortField: localSortField.value,
      sortOrder: localSortOrder.value,
      page: options.page || paginatedData.current_page || 1,
      rows: options.rows || paginatedData.per_page || 15,
      ...collectFilterValues(),
    },
    {
      preserveState: true,
      replace: true,
      onFinish: () => {
        loading.value = false;
      },
    },
  );
};
```

### 7.4. Search and Filtering

Search inputs with debounced filtering are handled by the composable:

```vue
<!-- Global filter input -->
<InputText
  id="searchGlobal"
  v-model="filters.global.value"
  :placeholder="$t('entity.search_placeholder')"
  class="w-full"
  @input="onFilter"
/>
```

The composable handles:

- Debouncing filter changes
- Extracting filter values
- Resetting pagination when filters change
- Supporting multiple filter types

## 8. Internationalization

Using Vue I18n for translations:

```javascript
const { t: $t } = useI18n();
```

All UI text is accessed via translation keys:

```vue
<span class="text-xl">{{ $t('entity.title') }}</span>
```

## 9. Notifications and Dialogs

### 9.1. Toast Notifications Using Composables

For automatic flash message handling, use the `useFlashMessages` composable:

```javascript
// Will automatically show toasts for success, error, etc. flash messages
useFlashMessages({ i18nPrefix: 'entities' });
```

For manual toast notifications:

```javascript
const toast = useToast();
toast.add({
  severity: 'success',
  summary: $t('entity.success'),
  detail: successMessage,
  life: 3000,
});
```

### 9.2. Confirmation Dialogs Using CRUD Composable

For common CRUD operations with confirmation dialogs, use the `useCrud` composable:

```javascript
// Get the CRUD operations with built-in confirmation
const {
  confirmDelete
} = useCrud({
  resourceName: 'entity',
  resourceRouteName: 'entities',
  i18nPrefix: 'entities',
});

// In template, use the provided method
<Button
  icon="pi pi-trash"
  v-tooltip.top="$t('entities.delete')"
  @click="confirmDelete(entity.id)"
/>
```

For custom confirmation dialogs:

```javascript
const confirm = useConfirm();
confirm.require({
  message: $t('entity.delete_confirm'),
  header: $t('entity.delete_header'),
  icon: 'pi pi-info-circle',
  acceptClass: 'p-button-danger',
  accept: () => {
    deleteEntity(id);
  },
});
```

## 10. Responsive Design

- Tailwind CSS classes used for responsive layouts
- Mobile-first approach with breakpoints:
  - `sm`: 640px and up
  - `md`: 768px and up
  - `lg`: 1024px and up

Example:

```html
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"></div>
```

## 11. Creating New Pages

To create a new feature UI following this architecture:

1. Create page components in `resources/js/Pages/dashboard/FeatureName/`
2. Create reusable components in `resources/js/Components/FeatureName/`
3. Follow the established patterns:
   - Index page with DataTable for listing (using `useDataTable` composable)
   - CreateOrEdit page for forms
   - Separate Form component for reusability
4. Integrate with backend via Inertia.js props and form submissions
5. Use proper internationalization for all user-facing text
6. Implement consistent styling with PrimeVue components and Tailwind CSS
7. Leverage composables for common functionality

### Example Implementation Flow:

1. Create data interfaces for your entity
2. Implement the Index page using:
   - `useDataTable` for table functionality
   - `useCrud` for navigation and delete operations
   - `useFlashMessages` for notifications
3. Implement the Create/Edit form component
4. Ensure responsive design works on all screen sizes
5. Add internationalization entries for all text

### Sample Index Page Template:

```typescript
<script setup lang="ts">
import { useCrud } from '@/Composables/useCrud';
import { useDataTable } from '@/Composables/useDataTable';
import { useFlashMessages } from '@/Composables/useFlashMessages';

// Define your entity interfaces
interface Entity {
  id: number;
  name: string;
  // other properties
}

// Define props from backend
const props = defineProps<{
  entities: { /* paginated data structure */ },
  sortField: string,
  sortOrder: 'asc' | 'desc',
  searchName?: string,
}>();

// Use the data table composable
const {
  loading,
  filters,
  localSortField,
  localSortOrder,
  onFilter,
  onPage,
  onSort,
  syncWithProps,
} = useDataTable<Entity>(props.entities, {
  routeName: 'entities.index',
  initialSortField: props.sortField,
  initialSortOrder: props.sortOrder,
  initialFilters: {
    searchName: props.searchName,
  },
});

// Use the CRUD operations composable
const {
  navigateToCreate,
  editItem,
  viewItem,
  confirmDelete,
} = useCrud({
  resourceName: 'entity',
  resourceRouteName: 'entities',
  i18nPrefix: 'entities',
});

// Handle flash messages automatically
useFlashMessages({ i18nPrefix: 'entities' });
</script>
```

By following these patterns and leveraging composables, you'll maintain consistency across the TimeHive application while efficiently implementing new features with less code duplication.
