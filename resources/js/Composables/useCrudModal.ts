import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';

interface CrudModalOptions<T> {
  resourceName: string; // e.g., 'users', 'roles'
  i18nPrefix: string; // e.g., 'users', 'roles'
  formSchema: T;
  transformFormData?: (data: T) => any;
}

export function useCrudModal<T extends Record<string, any>>(
  options: CrudModalOptions<T>,
) {
  const { t: $t } = useI18n();
  const toast = useToast();

  const form = useForm<T>(options.formSchema);

  const createFormModal = (props: { visible: boolean; item?: any | null }) => {
    const isVisible = computed({
      get: () => props.visible,
      set: (value) => {
        if (!value) {
          emit('hide');
        }
      },
    });

    const isEditing = computed(() => !!props.item?.id);

    const modalTitle = computed(() => {
      return isEditing.value
        ? $t(`${options.i18nPrefix}.edit`)
        : $t(`${options.i18nPrefix}.create`);
    });

    const submitButtonText = computed(() => {
      return isEditing.value
        ? $t(`${options.i18nPrefix}.update`)
        : $t(`${options.i18nPrefix}.create`);
    });

    const emit = (event: 'hide' | 'success') => {
      // This would be defined by the component using this composable
    };

    // Watch for modal visibility and item changes
    const setupFormWatcher = (
      resetForm: () => void,
      populateForm: (item: any) => void,
    ) => {
      watch(
        () => [props.visible, props.item] as const,
        ([visible, item]) => {
          if (visible) {
            form.clearErrors();
            if (item) {
              populateForm(item);
            } else {
              resetForm();
            }
          }
        },
        { immediate: true },
      );
    };

    const submit = () => {
      const formData = options.transformFormData
        ? options.transformFormData(form.data() as T)
        : form.data();

      if (isEditing.value && props.item?.id) {
        // Update
        form.put(route(`${options.resourceName}.update`, props.item.id), {
          preserveScroll: true,
          onSuccess: () => {
            toast.add({
              severity: 'success',
              summary: $t(`${options.i18nPrefix}.updated`),
              detail: $t(`${options.i18nPrefix}.updated_success`),
              life: 3000,
            });
            emit('success');
          },
          onError: () => {
            toast.add({
              severity: 'error',
              summary: $t(`${options.i18nPrefix}.error`),
              detail: $t(`${options.i18nPrefix}.error_updating`),
              life: 3000,
            });
          },
        });
      } else {
        // Create
        form.post(route(`${options.resourceName}.store`), {
          preserveScroll: true,
          onSuccess: () => {
            toast.add({
              severity: 'success',
              summary: $t(`${options.i18nPrefix}.created`),
              detail: $t(`${options.i18nPrefix}.created_success`),
              life: 3000,
            });
            emit('success');
          },
          onError: () => {
            toast.add({
              severity: 'error',
              summary: $t(`${options.i18nPrefix}.error`),
              detail: $t(`${options.i18nPrefix}.error_creating`),
              life: 3000,
            });
          },
        });
      }
    };

    return {
      form,
      isVisible,
      isEditing,
      modalTitle,
      submitButtonText,
      submit,
      setupFormWatcher,
    };
  };

  return createFormModal;
}
