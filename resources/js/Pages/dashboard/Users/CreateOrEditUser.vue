<script setup lang="ts">
import UserForm from '@/Components/Users/UserForm.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { User } from '@/types/interfaces';
import { Head } from '@inertiajs/vue3';
import Card from 'primevue/card';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t: $t } = useI18n();

const props = defineProps<{
  user?: User;
}>();

const formActionUrl = computed(() => {
  return props.user
    ? route('users.update', props.user.id)
    : route('users.store');
});

const formMethod = computed(() => {
  return props.user ? 'put' : 'post';
});
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="user ? $t('users.edit_user') : $t('users.create_user')" />
    <div class="mx-auto max-w-7xl">
      <Card class="dark:border-primary-900 mb-4 shadow-xl dark:border">
        <template #title>
          <span class="text-xl">{{
            user ? $t('users.edit_user') : $t('users.create_user')
          }}</span>
        </template>
        <template #content>
          <UserForm
            :user="user as any"
            :formActionUrl="formActionUrl"
            :formMethod="formMethod"
          />
        </template>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
