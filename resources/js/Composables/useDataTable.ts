import { useDebounce } from '@/Composables/useDebounce';
import {
  DataTableOptions,
  FilterValue,
  FilterValueObject,
  Filters,
  PaginatedData,
} from '@/types/interfaces';
import { router } from '@inertiajs/vue3';
import type {
  DataTablePageEvent,
  DataTableSortEvent,
} from 'primevue/datatable';
import { ref } from 'vue';

/**
 * Composable for managing PrimeVue DataTable state including pagination, sorting, and filtering.
 * It handles fetching data from a specified Inertia route and updating the table's state.
 *
 * @template T The type of the items in the paginated data.
 * @param {PaginatedData<T>} paginatedData Reactive reference to the initial paginated data.
 * @param {DataTableOptions} options Configuration options for the data table.
 * @returns An object containing reactive state and methods to interact with the data table.
 */
export function useDataTable<T>(
  paginatedData: PaginatedData<T>,
  options: DataTableOptions,
) {
  const loading = ref(false);
  const localSortField = ref(options.initialSortField || 'id');
  const localSortOrder = ref(options.initialSortOrder || 'asc');

  const filters = ref<Filters>({
    global: {
      value: options.initialFilters?.searchName || null,
      matchMode: 'contains',
    },
    ...Object.entries(options.initialFilters || {}).reduce(
      (acc, [key, value]) => {
        if (key !== 'searchName') {
          acc[key] = value;
        }
        return acc;
      },
      {} as Record<string, FilterValue>,
    ),
  });

  /**
   * Fetches data from the server with the current sort, filter, and pagination parameters.
   * @param {Record<string, any>} [pageOptions={}] Optional page and row settings.
   */
  const refreshData = (pageOptions: Record<string, any> = {}) => {
    loading.value = true;

    const params = {
      sortField: localSortField.value,
      sortOrder: localSortOrder.value,
      page: pageOptions.page || paginatedData.current_page || 1,
      rows: pageOptions.rows || paginatedData.per_page || 15,
      ...collectFilterValues(),
      ...options.additionalParams,
    };

    router.get(route(options.routeName), params, {
      preserveState: true,
      replace: true,
      onFinish: () => {
        loading.value = false;
      },
    });
  };
  /**
   * Collects all active filter values into an object suitable for a query string.
   * Global filter is mapped to 'searchName'.
   * Other filters are mapped according to the filterMappings option, or used as-is if no mapping exists.
   * @returns {Record<string, any>} An object mapping filter keys to their values.
   */
  const collectFilterValues = () => {
    const result: Record<string, any> = {};

    if (filters.value.global?.value) {
      result.searchName = filters.value.global.value;
    }

    Object.entries(filters.value).forEach(([key, value]) => {
      if (key !== 'global' && value !== null && value !== undefined) {
        const actualValue =
          typeof value === 'object' && value !== null && 'value' in value
            ? (value as FilterValueObject).value
            : value;

        if (actualValue === null || actualValue === undefined) {
          return;
        }

        const mappedKey =
          options.filterMappings && key in options.filterMappings
            ? options.filterMappings[key]
            : key;

        result[mappedKey] = actualValue;
      }
    });

    return result;
  };

  const debouncedFilter = useDebounce(() => {
    refreshData({ page: 1 });
  }, options.debounceTime || 300);

  /**
   * Handles filter events, typically from input fields.
   * It debounces the actual data refresh operation.
   */
  const onFilter = () => {
    debouncedFilter();
  };

  /**
   * Handles page change events from the DataTable.
   * @param {DataTablePageEvent} event The PrimeVue DataTable page event.
   */
  const onPage = (event: DataTablePageEvent) => {
    refreshData({ page: event.page + 1, rows: event.rows });
  };

  /**
   * Handles sort events from the DataTable.
   * @param {DataTableSortEvent} event The PrimeVue DataTable sort event.
   */
  const onSort = (event: DataTableSortEvent) => {
    localSortField.value = event.sortField as string;
    localSortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
    refreshData({ page: 1 });
  };

  /**
   * Updates a specific filter value and triggers a debounced data refresh.
   * @param {string} key The key of the filter to update (e.g., 'global', 'status').
   * @param {any} value The new value for the filter.
   */
  const updateFilter = (key: string, value: any) => {
    if (key === 'global') {
      filters.value.global.value = value;
    } else {
      if (
        typeof filters.value[key] === 'object' &&
        filters.value[key] !== null &&
        'value' in filters.value[key]
      ) {
        (filters.value[key] as FilterValueObject).value = value;
      } else {
        filters.value[key] = value;
      }
    }
    debouncedFilter();
  };

  /**
   * Synchronizes the composable's internal state (sort order, sort field, filters)
   * with new props passed from a parent component. This is useful when the initial
   * state or state changes are driven by props.
   * @param {Record<string, any>} newProps An object representing the new props.
   * Expected keys include `sortField`, `sortOrder`, `searchName` (for global filter),
   * and any other keys that exist in the filters object (e.g., `searchEmail`, `is_worker`).
   */
  const syncWithProps = (newProps: Record<string, any>) => {
    if (newProps.sortField && localSortField.value !== newProps.sortField) {
      localSortField.value = newProps.sortField;
    }
    if (newProps.sortOrder && localSortOrder.value !== newProps.sortOrder) {
      localSortOrder.value = newProps.sortOrder;
    }

    if (
      newProps.searchName !== undefined &&
      filters.value.global.value !== newProps.searchName
    ) {
      filters.value.global.value = newProps.searchName;
    }

    Object.entries(newProps).forEach(([key, value]) => {
      if (
        key !== 'sortField' &&
        key !== 'sortOrder' &&
        key !== 'searchName' &&
        key in filters.value &&
        value !== undefined &&
        filters.value[key] !== value
      ) {
        filters.value[key] = value;
      }
    });
  };

  return {
    /** Indicates if data is currently being loaded. */
    loading,
    /** Reactive object holding the current filter values. */
    filters,
    /** Reactive reference to the current sort field. */
    localSortField,
    /** Reactive reference to the current sort order ('asc' or 'desc'). */
    localSortOrder,
    /** Function to manually refresh the table data. Accepts page and row options. */
    refreshData,
    /** Callback function to be triggered when a filter event occurs. Applies debouncing. */
    onFilter,
    /** Callback function to handle page change events from the DataTable. */
    onPage,
    /** Callback function to handle sort events from the DataTable. */
    onSort,
    /** Function to update a specific filter value and refresh data. */
    updateFilter,
    /** Function to synchronize internal state with new props. */
    syncWithProps,
  };
}
