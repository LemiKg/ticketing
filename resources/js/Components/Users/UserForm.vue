<template>
  <Card>
    <Toast />
    <template #content>
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- Name Field -->
          <div class="col-span-2 sm:col-span-1">
            <FloatLabel>
              <InputText
                id="name"
                v-model="form.name"
                type="text"
                class="w-full"
                :class="{ 'p-invalid': form.errors.name }"
              />
              <label for="name">{{ $t('users.form.name') }}</label>
            </FloatLabel>
            <small v-if="form.errors.name" class="p-error">{{
              form.errors.name
            }}</small>
          </div>

          <!-- Email Field -->
          <div class="col-span-2 sm:col-span-1">
            <FloatLabel>
              <InputText
                id="email"
                v-model="form.email"
                type="email"
                class="w-full"
                :class="{ 'p-invalid': form.errors.email }"
              />
              <label for="email">{{ $t('users.form.email') }}</label>
            </FloatLabel>
            <small v-if="form.errors.email" class="p-error">{{
              form.errors.email
            }}</small>
          </div>

          <!-- Password Field (only for new users or when changing password) -->
          <div class="col-span-2 sm:col-span-1">
            <FloatLabel>
              <Password
                id="password"
                v-model="form.password"
                :feedback="false"
                toggleMask
                class="w-full"
                :class="{ 'p-invalid': form.errors.password }"
              />
              <label for="password">{{ $t('users.form.password') }}</label>
            </FloatLabel>
            <small v-if="!user" class="text-gray-500 dark:text-gray-400">
              {{ $t('users.form.password_required_new') }}
            </small>
            <small v-else class="text-gray-500 dark:text-gray-400">
              {{ $t('users.form.password_help_update') }}
            </small>
            <small v-if="form.errors.password" class="p-error">{{
              form.errors.password
            }}</small>
          </div>

          <!-- Password Confirmation Field -->
          <div class="col-span-2 sm:col-span-1">
            <FloatLabel>
              <Password
                id="password_confirmation"
                v-model="form.password_confirmation"
                :feedback="false"
                toggleMask
                class="w-full"
                :class="{ 'p-invalid': form.errors.password_confirmation }"
              />
              <label for="password_confirmation">{{
                $t('users.form.password_confirmation')
              }}</label>
            </FloatLabel>
            <small v-if="form.errors.password_confirmation" class="p-error">{{
              form.errors.password_confirmation
            }}</small>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <Button
            type="button"
            :label="$t('admin.common.cancel')"
            class="p-button-secondary"
            @click="cancelForm"
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
    </template>
  </Card>
</template>

<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();
const toast = useToast();

interface UserFormData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  [key: string]: any;
}

const props = defineProps<{
  user?: UserFormData & { id?: number };
  formActionUrl: string;
  formMethod: string;
}>();

const submitButtonText = computed(() => {
  return props.user ? $t('users.form.update') : $t('users.form.create');
});

const form = useForm({
  name: props.user?.name || '',
  email: props.user?.email || '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  if (!form.name) {
    form.setError('name', 'Name is required');
    return;
  }

  if (!form.email) {
    form.setError('email', 'Email is required');
    return;
  }

  if (!props.user && !form.password) {
    form.setError('password', 'Password is required for new users');
    return;
  }

  if (props.formMethod === 'put' && props.user?.id) {
    form.put(props.formActionUrl, {
      preserveScroll: true,
      preserveState: true,
      onError: (errors) => console.error('Update errors:', errors),
      onSuccess: () => {
        toast.add({
          severity: 'success',
          summary: $t('users.updated'),
          detail: $t('users.update_success'),
          life: 3000,
        });
        router.visit(route('users.index'));
      },
    });
  } else {
    form.post(route('users.store'), {
      preserveScroll: true,
      preserveState: true,
      onError: (errors) => {
        console.error('Create errors:', errors);
      },
      onSuccess: () => {
        toast.add({
          severity: 'success',
          summary: $t('users.created'),
          detail: $t('users.create_success'),
          life: 3000,
        });
        router.visit(route('users.index'));
      },
    });
  }
};

const cancelForm = () => {
  router.visit(route('users.index'));
};
</script>
