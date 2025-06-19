import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

/**
 * Composable for automatically translating validation error messages
 *
 * This composable provides a function to translate error messages that come from Laravel validation.
 * It automatically detects if a message is a translation key and translates it accordingly.
 *
 * @returns Object with translateError function
 */
export function useErrorTranslation() {
  const { t: $t } = useI18n();

  /**
   * Translate an error message if it's a translation key
   *
   * @param message - The error message (could be plain text or translation key)
   * @returns Translated message or original message if not a translation key
   */
  const translateError = (message: string | undefined): string => {
    if (!message) return '';

    const isTranslationKey =
      message.includes('.') &&
      (message.startsWith('admin.') ||
        message.startsWith('validation.') ||
        message.startsWith('auth.') ||
        message.startsWith('passwords.'));

    if (isTranslationKey) {
      try {
        const translated = $t(message);

        if (translated && translated !== message) {
          return translated;
        }
      } catch (error) {
        console.warn(`Translation failed for key: ${message}`, error);
      }
    }

    return message;
  };

  /**
   * Create a computed property that translates an error message
   *
   * @param errorRef - Reactive reference to the error message
   * @returns Computed property with translated error message
   */
  const useTranslatedError = (errorRef: () => string | undefined) => {
    return computed(() => translateError(errorRef()));
  };

  return {
    translateError,
    useTranslatedError,
  };
}
