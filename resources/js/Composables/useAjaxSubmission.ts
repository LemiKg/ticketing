import { useToast } from 'primevue/usetoast';
import { useI18n } from 'vue-i18n';

interface AjaxSubmissionOptions {
  url: string;
  method: 'POST' | 'PUT';
  data: any;
  onSuccess: () => void;
  onValidationError: (errors: Record<string, string[]>) => void;
  i18nPrefix: string;
  isEditing: boolean;
}

interface AjaxResponse {
  success: boolean;
  message?: string;
  errors?: Record<string, string[]>;
}

/**
 * Composable for handling AJAX form submissions with CSRF protection and error handling.
 *
 * @returns {Object} An object containing the submitForm function.
 */
export function useAjaxSubmission() {
  const toast = useToast();
  const { t: $t } = useI18n();

  let csrfToken: string | null = null;
  const getCsrfToken = (): string => {
    if (!csrfToken) {
      csrfToken =
        document
          .querySelector('meta[name="csrf-token"]')
          ?.getAttribute('content') || '';
    }
    return csrfToken;
  };

  /**
   * Submits a form via AJAX and handles the response.
   *
   * @param {AjaxSubmissionOptions}
   * options - The options for the AJAX submission.
   * @param {string} options.url - The URL to submit the form to.
   * @param {string} options.method - The HTTP method to use (POST or PUT).
   * @param {any} options.data - The data to submit with the form.
   * @param {Function} options.onSuccess - Callback function to call on successful submission.
   * @param {Function} options.onValidationError - Callback function to call on validation errors.
   * @param {string} options.i18nPrefix - The i18n prefix for translation keys.
   * @param {boolean} options.isEditing - Whether the form is for editing an existing resource.
   * @return {Promise<void>} A promise that resolves when the submission is complete.
   * @throws {Error} Throws an error if the AJAX request fails or if the response is not successful.
   **/
  const submitForm = async (options: AjaxSubmissionOptions): Promise<void> => {
    const {
      url,
      method,
      data,
      onSuccess,
      onValidationError,
      i18nPrefix,
      isEditing,
    } = options;

    try {
      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          Accept: 'application/json',
        },
        body: JSON.stringify(data),
      });

      const result: AjaxResponse = await response.json();

      if (response.ok && result.success) {
        toast.add({
          severity: 'success',
          summary: $t(`${i18nPrefix}.${isEditing ? 'updated' : 'created'}`),
          detail: $t(
            `${i18nPrefix}.${isEditing ? 'updated_success' : 'created_success'}`,
          ),
          life: 3000,
        });
        onSuccess();
      } else {
        if (result.errors) {
          onValidationError(result.errors);
        }

        toast.add({
          severity: 'error',
          summary: $t(`${i18nPrefix}.error`),
          detail:
            result.message ||
            $t(`${i18nPrefix}.error_${isEditing ? 'updating' : 'creating'}`),
          life: 3000,
        });
      }
    } catch (error) {
      toast.add({
        severity: 'error',
        summary: $t(`${i18nPrefix}.error`),
        detail: $t(
          `${i18nPrefix}.error_${isEditing ? 'updating' : 'creating'}`,
        ),
        life: 3000,
      });
    }
  };

  return {
    submitForm,
  };
}
