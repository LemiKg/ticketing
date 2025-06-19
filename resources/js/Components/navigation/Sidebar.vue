<script setup lang="ts">
import PrimaryButton from '@/Components/buttons/PrimaryButton.vue';
import { MenuItem, PrimeVueMenuItem } from '@/types/interfaces';
import { Link, usePage } from '@inertiajs/vue3';
import Drawer from 'primevue/drawer';
import PanelMenu from 'primevue/panelmenu';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps<{
  menuItems: MenuItem[];
  isCollapsed?: boolean;
  isDrawerVisible?: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:isCollapsed', value: boolean): void;
  (e: 'update:isDrawerVisible', value: boolean): void;
}>();

const page = usePage();
const expandedKeys = ref<Record<string, boolean>>({});
const panelMenuRef = ref<any>();

/**
 * Initializes the sidebar state from localStorage on component mount.
 * Loads saved expanded menu items and collapsed state.
 */
onMounted(() => {
  const savedKeys = localStorage.getItem('sidebarExpandedItems');
  const savedCollapsed = localStorage.getItem('sidebarCollapsed');
  if (savedKeys) {
    try {
      expandedKeys.value = JSON.parse(savedKeys);
    } catch (e) {
      console.error('Failed to parse sidebarExpandedItems', e);
    }
  }
  if (savedCollapsed !== null) {
    emit('update:isCollapsed', savedCollapsed === 'true');
  }
});

/**
 * Watches for changes in expandedKeys and persists them to localStorage.
 */
watch(
  expandedKeys,
  (newVal) => {
    localStorage.setItem('sidebarExpandedItems', JSON.stringify(newVal));
  },
  { deep: true },
);

/**
 * Watches for changes in isCollapsed prop and persists the state to localStorage.
 */
watch(
  () => props.isCollapsed,
  (newVal) => {
    localStorage.setItem('sidebarCollapsed', String(newVal));
  },
);

/**
 * Checks if a given route is currently active.
 * @param routeName - The name of the route to check
 * @returns boolean indicating if the route is active
 */
const isActive = (routeName?: string): boolean => {
  if (!routeName) return false;
  if (routeName.startsWith('http')) return page.url.startsWith(routeName);
  try {
    return route().current(routeName);
  } catch (e) {
    console.warn(`Failed to generate route for: ${routeName}`, e);
    return false;
  }
};

/**
 * Checks if any child menu items of the given item are active.
 * @param item - The menu item to check
 * @returns boolean indicating if any child items are active
 */
const hasActiveChild = (item: MenuItem): boolean => {
  return (
    !!item.children &&
    item.children.some(
      (child) =>
        (child.route && isActive(child.route)) || hasActiveChild(child),
    )
  );
};

/**
 * Toggles the sidebar's collapsed state.
 */
const toggleSidebar = () => {
  emit('update:isCollapsed', !props.isCollapsed);
};

/**
 * Closes the drawer on mobile/tablet
 */
const closeDrawer = () => {
  emit('update:isDrawerVisible', false);
};

/**
 * Handles menu item clicks in drawer - closes drawer on navigation
 */
const handleDrawerMenuClick = () => {
  closeDrawer();
};

/**
 * Converts the menu items structure to the format expected by PrimeVue's PanelMenu.
 * @param items - Array of menu items to convert
 * @param prefix - Optional prefix for generating unique keys
 * @returns Array of menu items in PrimeVue format
 */
const convertToPrimeVueMenu = (
  items: MenuItem[],
  prefix = '',
): PrimeVueMenuItem[] => {
  return items.map((item, index) => {
    const key = prefix + index;
    return {
      key,
      label: item.title,
      icon: item.icon,
      to: item.route ? route(item.route) : undefined,
      rawRoute: item.route || undefined,
      items: item.children
        ? convertToPrimeVueMenu(item.children, key + '_')
        : undefined,
    };
  });
};

/**
 * Computed property that converts the menu items to PrimeVue format.
 */
const convertedMenu = computed(() => convertToPrimeVueMenu(props.menuItems));

/**
 * Computed property for drawer visibility with proper v-model support
 */
const drawerVisible = computed({
  get: () => props.isDrawerVisible || false,
  set: (value: boolean) => emit('update:isDrawerVisible', value),
});
</script>

<template>
  <!-- Desktop Sidebar (hidden on lg and below) -->
  <aside
    class="sidebar hidden h-screen overflow-y-hidden transition-all xl:block"
    :class="[
      'bg-white shadow-lg dark:bg-gray-900',
      isCollapsed ? 'w-16' : 'w-64',
    ]"
  >
    <div
      class="flex h-16 items-center justify-center px-1 dark:border-gray-700"
    >
      <slot name="branding">
        <h1 class="text-xl font-bold text-gray-800 dark:text-white">
          Boilerplate
        </h1>
      </slot>
    </div>

    <div class="flex justify-end px-3">
      <PrimaryButton @click="toggleSidebar" class="p-2 focus:outline-hidden">
        <i :class="isCollapsed ? 'pi pi-caret-right' : 'pi pi-caret-left'"></i>
      </PrimaryButton>
    </div>

    <nav
      class="mt-4 max-h-[calc(100vh-9rem)] overflow-x-hidden overflow-y-auto"
    >
      <PanelMenu
        ref="panelMenuRef"
        :model="convertedMenu"
        :multiple="true"
        v-model:expandedKeys="expandedKeys"
        :pt="{
          rootList: isCollapsed ? 'p-0!' : '',
          root: 'p-1 overflow-y-auto',
          panel: 'dark:!border-primary-900 dark:border',
        }"
        :class="[isCollapsed ? 'p-panelmenu-minimized' : '']"
      >
        <template #item="slotProps">
          <Link
            v-if="slotProps.item.to"
            :href="slotProps.item.to"
            :method="slotProps.item.rawRoute === 'logout' ? 'post' : 'get'"
            class="flex items-center rounded-md px-4 py-2 text-sm font-medium"
            :class="[
              isActive(slotProps.item.rawRoute)
                ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white'
                : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
              isCollapsed ? 'justify-center' : '',
            ]"
          >
            <i
              :class="[slotProps.item.icon, isCollapsed ? 'text-xl' : 'mr-3']"
            />
            <span v-if="!isCollapsed" class="whitespace-nowrap transition-all">
              {{ slotProps.item.label }}
            </span>
          </Link>
          <div
            v-else
            class="flex items-center justify-between rounded-md px-4 py-2 text-sm font-medium"
            :class="[
              'text-gray-600 dark:text-gray-300',
              isCollapsed ? 'justify-center' : '',
              slotProps.hasSubmenu && isCollapsed ? 'px-2!' : '',
            ]"
          >
            <div class="flex items-center">
              <i
                :class="[slotProps.item.icon, isCollapsed ? 'text-xl' : 'mr-3']"
              />
              <span class="whitespace-nowrap" v-if="!isCollapsed">{{
                slotProps.item.label
              }}</span>
            </div>
            <div
              v-if="slotProps.item.items"
              class="bg-primary-50 dark:bg-primary-950 flex items-center rounded-xs"
            >
              <i
                :class="
                  expandedKeys[slotProps.item.key!]
                    ? 'pi pi-angle-down'
                    : 'pi pi-angle-right'
                "
                class="cursor-pointer"
              ></i>
            </div>
          </div>
        </template>
      </PanelMenu>
    </nav>
  </aside>

  <!-- Mobile/Tablet Drawer -->
  <Drawer
    v-model:visible="drawerVisible"
    position="left"
    class="xl:hidden"
    :style="{ width: '20rem' }"
    :pt="{
      mask: 'xl:hidden',
      root: 'xl:hidden',
      header: '!hidden',
    }"
  >
    <div class="flex h-full flex-col">
      <!-- Drawer Header with branding -->
      <div
        class="flex h-16 items-center justify-between border-b px-4 dark:border-gray-700"
      >
        <slot name="branding">
          <h1 class="text-xl font-bold text-gray-800 dark:text-white">
            Boilerplate
          </h1>
        </slot>
        <PrimaryButton @click="closeDrawer" class="p-2 focus:outline-hidden">
          <i class="pi pi-times"></i>
        </PrimaryButton>
      </div>

      <!-- Drawer Navigation -->
      <nav class="flex-1 overflow-y-auto p-4">
        <PanelMenu
          :model="convertedMenu"
          :multiple="true"
          v-model:expandedKeys="expandedKeys"
          :pt="{
            root: 'p-0',
            panel: 'dark:!border-primary-900 dark:border',
          }"
        >
          <template #item="slotProps">
            <Link
              v-if="slotProps.item.to"
              :href="slotProps.item.to"
              :method="slotProps.item.rawRoute === 'logout' ? 'post' : 'get'"
              class="flex items-center rounded-md px-4 py-2 text-sm font-medium"
              :class="[
                isActive(slotProps.item.rawRoute)
                  ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white'
                  : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
              ]"
              @click="handleDrawerMenuClick"
            >
              <i :class="[slotProps.item.icon, 'mr-3']" />
              <span class="whitespace-nowrap transition-all">
                {{ slotProps.item.label }}
              </span>
            </Link>
            <div
              v-else
              class="flex items-center justify-between rounded-md px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300"
            >
              <div class="flex items-center">
                <i :class="[slotProps.item.icon, 'mr-3']" />
                <span class="whitespace-nowrap">{{
                  slotProps.item.label
                }}</span>
              </div>
              <div
                v-if="slotProps.item.items"
                class="bg-primary-50 dark:bg-primary-950 flex items-center rounded-xs"
              >
                <i
                  :class="
                    expandedKeys[slotProps.item.key!]
                      ? 'pi pi-angle-down'
                      : 'pi pi-angle-right'
                  "
                  class="cursor-pointer"
                ></i>
              </div>
            </div>
          </template>
        </PanelMenu>
      </nav>
    </div>
  </Drawer>
</template>

<style>
.sidebar nav {
  scrollbar-width: none !important;
  scrollbar-color: #cbd5e1 transparent !important;
}
</style>
