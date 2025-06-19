<script setup lang="ts">
import ApplicationLogo from '@/Components/branding/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/language/LanguageSwitcher.vue';
import Sidebar from '@/Components/navigation/Sidebar.vue';
import ThemeToggle from '@/Components/theme/ThemeToggle.vue';
import { Link } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const storedCollapsed = localStorage.getItem('sidebarCollapsed');
const isCollapsed = ref(storedCollapsed ? storedCollapsed === 'true' : false);
const isDrawerVisible = ref(false);

/**
 * Toggles the mobile/tablet drawer visibility
 */
const toggleDrawer = () => {
  isDrawerVisible.value = !isDrawerVisible.value;
};

const menuItems = computed(() => [
  {
    title: t('menu.dashboard'),
    icon: 'pi pi-home',
    route: 'dashboard',
  },
  {
    title: t('menu.users.title'),
    icon: 'pi pi-users',
    children: [
      {
        title: t('menu.users.list'),
        route: 'users.index',
        icon: 'pi pi-list',
      },
      {
        title: t('menu.users.roles'),
        route: 'roles.index',
        icon: 'pi pi-user-edit',
      },
    ],
  },
  {
    title: t('menu.profile.title'),
    icon: 'pi pi-user',
    children: [
      {
        title: t('menu.profile.edit'),
        route: 'profile.edit',
        icon: 'pi pi-user-edit',
      },
      {
        title: t('menu.profile.signOut'),
        route: 'logout',
        icon: 'pi pi-power-off',
      },
    ],
  },
]);
</script>

<template>
  <div
    class="h-screen min-h-screen overflow-hidden bg-gray-100 dark:bg-gray-800"
  >
    <div class="flex">
      <Sidebar
        v-model:isCollapsed="isCollapsed"
        v-model:isDrawerVisible="isDrawerVisible"
        :menuItems="menuItems"
      >
        <template #branding>
          <ApplicationLogo />
        </template>
      </Sidebar>
      <main class="flex-1">
        <nav
          class="border-b border-gray-100 bg-white dark:border-gray-800 dark:bg-gray-900"
        >
          <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between xl:justify-end">
              <!-- Mobile menu button (visible on xl and below) -->
              <div class="flex items-center xl:hidden">
                <button
                  @click="toggleDrawer"
                  class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-inset dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                >
                  <i class="pi pi-bars text-xl"></i>
                </button>
              </div>

              <div class="flex items-center space-x-4">
                <Link
                  href="/"
                  class="pointer flex items-center space-x-2 rounded-md px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                >
                  <i class="pi pi-external-link text-sm"></i>
                  <span>{{ t('menu.website') }}</span>
                </Link>
                <LanguageSwitcher />
                <ThemeToggle />
              </div>
            </div>
          </div>
        </nav>
        <div class="max-h-[calc(100vh-4rem)] overflow-y-scroll p-4">
          <slot />
        </div>
      </main>
    </div>
    <Toast />
  </div>
</template>
