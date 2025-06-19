import { CrudOptions } from '@/types/interfaces';
import { router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { useI18n } from 'vue-i18n';

/**
 * Composable for handling common CRUD (Create, Read, Update, Delete) operations.
 *
 * @param options - Configuration options for the CRUD operations.
 * @returns An object with functions for navigating to create/edit/show pages, and for confirming and performing delete operations.
 */
export function useCrud(options: CrudOptions) {
  const { t: $t } = useI18n();
  const confirm = useConfirm();
  const toast = useToast();

  /**
   * Navigates to the create page for the resource.
   */
  const navigateToCreate = () => {
    router.visit(route(`${options.resourceRouteName}.create`));
  };

  /**
   * Navigates to the show page for a specific item of the resource.
   *
   * @param id - The ID of the item to view.
   */
  const viewItem = (id: number) => {
    router.visit(route(`${options.resourceRouteName}.show`, id));
  };

  /**
   * Navigates to the edit page for a specific item of the resource.
   *
   * @param id - The ID of the item to edit.
   */
  const editItem = (id: number) => {
    router.visit(route(`${options.resourceRouteName}.edit`, id));
  };

  /**
   * Displays a confirmation dialog before deleting an item.
   * If confirmed, it calls the `deleteItem` function.
   *
   * @param id - The ID of the item to delete.
   */
  const confirmDelete = (id: number) => {
    confirm.require({
      message: $t(`${options.i18nPrefix}.delete_confirm`),
      header: $t(`${options.i18nPrefix}.delete_header`),
      icon: 'pi pi-info-circle',
      rejectClass: 'p-button-secondary p-button-outlined',
      acceptClass: 'p-button-danger',
      accept: () => {
        deleteItem(id);
      },
    });
  };

  /**
   * Performs the actual deletion of an item via an API call.
   * Shows success or error toasts based on the outcome.
   *
   * @param id - The ID of the item to delete.
   */
  const deleteItem = (id: number) => {
    router.delete(route(`${options.resourceRouteName}.destroy`, id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.add({
          severity: 'success',
          summary: $t(`${options.i18nPrefix}.deleted`),
          detail: $t(`${options.i18nPrefix}.delete_success`),
          life: 3000,
        });

        if (options.onSuccess) {
          options.onSuccess();
        }
      },
      onError: (errors) => {
        console.error(`Error deleting ${options.resourceName}:`, errors);
        toast.add({
          severity: 'error',
          summary: $t(`${options.i18nPrefix}.error`),
          detail: $t(`${options.i18nPrefix}.delete_error`),
          life: 3000,
        });
      },
    });
  };

  return {
    navigateToCreate,
    viewItem,
    editItem,
    confirmDelete,
    deleteItem,
  };
}
