import { FlashOptions } from '@/types/interfaces';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

/**
 * Composable for displaying flash messages from Inertia.js as toast notifications.
 *
 * @param options - Configuration options for flash messages.
 */
export function useFlashMessages(options: FlashOptions) {
  const { t: $t } = useI18n();
  const toast = useToast();
  const page = usePage();

  const lastShownMessage = ref<{ type?: string; detail?: string } | null>(null);

  const showFlashMessage = (
    flash: { success?: string; error?: string } | null | undefined,
  ) => {
    let messageType: string | undefined;
    let messageDetail: string | undefined;
    let severity: 'success' | 'error' | undefined;

    if (flash && flash.success) {
      messageType = 'success';
      messageDetail = flash.success;
      severity = 'success';
    } else if (flash && flash.error) {
      messageType = 'error';
      messageDetail = flash.error;
      severity = 'error';
    }

    if (!messageDetail) {
      lastShownMessage.value = null;
      return;
    }

    if (
      lastShownMessage.value?.type === messageType &&
      lastShownMessage.value?.detail === messageDetail
    ) {
      return;
    }

    if (severity) {
      toast.add({
        severity: severity,
        summary: $t(`${options.i18nPrefix}.${severity}`),
        detail: messageDetail,
        life: 3000,
      });
      lastShownMessage.value = { type: messageType, detail: messageDetail };
    }
  };

  onMounted(() => {
    const flash = page.props.flash as { success?: string; error?: string };
    if (flash && (flash.success || flash.error)) {
      showFlashMessage(flash);
    }
  });

  watch(
    () => page.props.flash,
    (newFlashValue) => {
      showFlashMessage(
        newFlashValue as
          | { success?: string; error?: string }
          | null
          | undefined,
      );
    },
    { deep: true },
  );

  return {};
}
