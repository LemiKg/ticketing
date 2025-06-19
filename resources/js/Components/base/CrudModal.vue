<template>
  <Dialog
    v-model:visible="isVisible"
    :modal="true"
    :dismissableMask="dismissableMask"
    :header="modalTitle"
    :style="modalStyle"
    :maximizable="maximizable"
    class="p-fluid"
    @hide="$emit('hide')"
  >
    <form @submit.prevent="submit" class="mt-5 space-y-6">
      <slot :form="form" :isEditing="isEditing" />

      <div class="flex justify-end space-x-3 pt-4">
        <Button
          type="button"
          :label="cancelLabel"
          class="p-button-text"
          @click="$emit('hide')"
        />
        <Button
          type="submit"
          :label="submitButtonText"
          :loading="form.processing"
          icon="pi pi-check"
          class="p-button-primary"
        />
      </div>
    </form>
  </Dialog>
</template>

<script setup lang="ts" generic="T extends Record<string, any>">
import { useAjaxSubmission } from '@/Composables/useAjaxSubmission';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import { useToast } from 'primevue/usetoast';
import { computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
  visible: boolean;
  item?: T | null;
  resourceName?: string; // e.g., 'users', 'roles' - optional for custom routes
  i18nPrefix: string; // e.g., 'users', 'roles'
  formSchema: T;
  modalStyle?: string;
  maximizable?: boolean;
  dismissableMask?: boolean;
  transformFormData?: (data: T) => any;
  useAjax?: boolean; // Use AJAX for form submission instead of Inertia form submission
  // Custom route support
  createRoute?: string;
  updateRoute?: string;
  customRouteParams?: any;
}

const props = withDefaults(defineProps<Props>(), {
  modalStyle: '{ width: "90vw", maxWidth: "600px" }',
  maximizable: true,
  dismissableMask: true,
  useAjax: false,
});

const emit = defineEmits<{
  hide: [];
  success: [];
  formPopulated: [form: any];
  formReset: [form: any];
}>();

const { t: $t } = useI18n();
const toast = useToast();
const { submitForm } = useAjaxSubmission();

const form = useForm<T>(props.formSchema);

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
    ? $t(`${props.i18nPrefix}.edit`)
    : $t(`${props.i18nPrefix}.create`);
});

const submitButtonText = computed(() => {
  return isEditing.value
    ? $t(`${props.i18nPrefix}.update`)
    : $t(`${props.i18nPrefix}.create`);
});

const cancelLabel = computed(() => {
  return $t(`admin.common.cancel`);
});

// Watch for modal visibility and item changes
watch(
  () => [props.visible, props.item] as const,
  ([visible, item]) => {
    if (visible) {
      form.clearErrors();
      if (item) {
        // Populate form with item data - emit event for custom population
        emit('formPopulated', { form, item });
      } else {
        // Reset form - emit event for custom reset
        emit('formReset', { form });
      }
    }
  },
  { immediate: true },
);

const submit = async () => {
  const formData = props.transformFormData
    ? props.transformFormData(form.data() as T)
    : form.data();

  if (props.useAjax) {
    // Use AJAX composable for dashboard and other scenarios that need to avoid page refresh
    form.processing = true;
    form.clearErrors();

    const routeName =
      isEditing.value && props.item?.id
        ? props.updateRoute || `${props.resourceName}.update`
        : props.createRoute || `${props.resourceName}.store`;

    const routeParams =
      isEditing.value && props.item?.id
        ? props.customRouteParams || props.item.id
        : undefined;

    const url = routeParams ? route(routeName, routeParams) : route(routeName);
    const method = isEditing.value ? 'PUT' : 'POST';

    await submitForm({
      url,
      method,
      data: formData,
      onSuccess: () => {
        emit('success');
        form.processing = false;
      },
      onValidationError: (errors) => {
        // Handle validation errors by setting them on the form
        for (const [key, messages] of Object.entries(errors)) {
          if (Array.isArray(messages) && messages.length > 0) {
            // Use type assertion since we know the key should be valid for our form
            (form as any).setError(key, messages[0]);
          }
        }
        form.processing = false;
      },
      i18nPrefix: props.i18nPrefix,
      isEditing: isEditing.value,
    });
  } else {
    // Use Inertia form submission (default behavior)
    if (isEditing.value && props.item?.id) {
      // Update
      const updateRouteName =
        props.updateRoute || `${props.resourceName}.update`;
      const routeParams = props.customRouteParams || props.item.id;

      form.put(route(updateRouteName, routeParams), {
        preserveScroll: true,
        onSuccess: () => {
          toast.add({
            severity: 'success',
            summary: $t(`${props.i18nPrefix}.updated`),
            detail: $t(`${props.i18nPrefix}.updated_success`),
            life: 3000,
          });
          emit('success');
        },
        onError: () => {
          toast.add({
            severity: 'error',
            summary: $t(`${props.i18nPrefix}.error`),
            detail: $t(`${props.i18nPrefix}.error_updating`),
            life: 3000,
          });
        },
      });
    } else {
      // Create
      const createRouteName =
        props.createRoute || `${props.resourceName}.store`;

      form.post(route(createRouteName), {
        preserveScroll: true,
        onSuccess: () => {
          toast.add({
            severity: 'success',
            summary: $t(`${props.i18nPrefix}.created`),
            detail: $t(`${props.i18nPrefix}.created_success`),
            life: 3000,
          });
          emit('success');
        },
        onError: () => {
          toast.add({
            severity: 'error',
            summary: $t(`${props.i18nPrefix}.error`),
            detail: $t(`${props.i18nPrefix}.error_creating`),
            life: 3000,
          });
        },
      });
    }
  }
};

// Expose form and methods to slot
defineExpose({
  form,
  isEditing,
  submit,
});
</script>
