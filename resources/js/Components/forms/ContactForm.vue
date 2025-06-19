<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

interface FormData {
  name: string;
  email: string;
  message: string;
}

const form = ref<FormData>({
  name: '',
  email: '',
  message: '',
});

const submitted = ref(false);
const loading = ref(false);
const error = ref('');

const submitForm = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await fetch('/contact', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN':
          document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content') || '',
      },
      body: JSON.stringify(form.value),
    });

    const result = await response.json();

    if (result.success) {
      submitted.value = true;
      // Clear form data
      form.value.name = '';
      form.value.email = '';
      form.value.message = '';
    } else {
      error.value = result.message || 'An error occurred';
    }
  } catch (err) {
    error.value = 'Network error. Please try again.';
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  submitted.value = false;
  error.value = '';
};
</script>
<template>
  <div
    class="dark:border-primary-900 rounded-3xl border bg-gray-200 p-6 shadow-xl dark:bg-gray-800"
  >
    <!-- Show success message when submitted -->
    <div
      v-if="submitted"
      class="flex h-full flex-col items-center justify-center space-y-4 text-center"
    >
      <div class="text-green-600 dark:text-green-400">
        <svg
          class="mx-auto h-16 w-16"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M5 13l4 4L19 7"
          ></path>
        </svg>
      </div>
      <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
        {{ t('website.contact.contactForm.successTitle') }}
      </h3>
      <p class="text-gray-600 dark:text-gray-400">
        {{ t('website.contact.contactForm.alertMessage') }}
      </p>
      <button
        @click="resetForm"
        class="bg-primary-600 dark:bg-secondary-400 hover:bg-primary-700 dark:hover:bg-primary-400 rounded-lg px-6 py-2 text-center font-bold text-gray-900 transition-colors duration-600"
      >
        {{ t('website.contact.contactForm.sendAnotherButton') }}
      </button>
    </div>

    <!-- Show error message -->
    <div
      v-else-if="error"
      class="mb-4 rounded-lg bg-red-100 p-4 text-red-700 dark:bg-red-900 dark:text-red-100"
    >
      {{ error }}
      <button @click="error = ''" class="ml-2 text-red-500 hover:text-red-700">
        ×
      </button>
    </div>

    <!-- Show form when not submitted -->
    <form
      v-else
      @submit.prevent="submitForm"
      class="flex h-full flex-col justify-between gap-4 space-y-8"
    >
      <div class="h-full">
        <div>
          <label class="mb-1 block text-gray-700 dark:text-gray-300" for="name">
            {{ t('website.contact.contactForm.nameLabel') }}
          </label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            class="w-full rounded-sm border p-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            :placeholder="t('website.contact.contactForm.placeholder.name')"
            required
          />
        </div>
        <div>
          <label
            class="mb-1 block text-gray-700 dark:text-gray-300"
            for="email"
          >
            {{ t('website.contact.contactForm.emailLabel') }}
          </label>
          <input
            id="email"
            type="email"
            v-model="form.email"
            class="w-full rounded-sm border p-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            :placeholder="t('website.contact.contactForm.placeholder.email')"
            required
          />
        </div>
        <div>
          <label
            class="mb-1 block text-gray-700 dark:text-gray-300"
            for="message"
          >
            {{ t('website.contact.contactForm.messageLabel') }}
          </label>
          <textarea
            id="message"
            v-model="form.message"
            class="h-full w-full rounded-sm border p-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            :placeholder="t('website.contact.contactForm.placeholder.message')"
            rows="4"
            required
          ></textarea>
        </div>
      </div>
      <button
        type="submit"
        :disabled="loading"
        class="bg-primary-600 dark:bg-secondary-400 hover:bg-primary-700 dark:hover:bg-primary-400 w-full rounded-lg px-4 py-2 text-center font-bold text-gray-900 transition-colors duration-600 disabled:cursor-not-allowed disabled:opacity-50"
      >
        <span v-if="loading">Sending...</span>
        <span v-else>{{ t('website.contact.contactForm.submitButton') }}</span>
      </button>
    </form>
  </div>
</template>
