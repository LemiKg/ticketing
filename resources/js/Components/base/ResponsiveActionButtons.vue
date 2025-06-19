<script setup lang="ts">
import type { ActionMenuItem } from '@/types/interfaces';
import Button from 'primevue/button';
import Menu from 'primevue/menu';
import { ref } from 'vue';

/**
 * ResponsiveActionButtons Component
 *
 * Shows action buttons inline on desktop (xl and up)
 * Collapses to a three-dots menu on laptop and below
 *
 * Props:
 * - menuItems: Array of menu items for mobile view { label, icon, command }
 *
 * Usage:
 * <ResponsiveActionButtons :menuItems="actionMenuItems">
 *   <Button icon="pi pi-eye" @click="view()" v-tooltip.top="'View'" />
 *   <Button icon="pi pi-pencil" @click="edit()" v-tooltip.top="'Edit'" />
 *   <Button icon="pi pi-trash" @click="delete()" v-tooltip.top="'Delete'" />
 * </ResponsiveActionButtons>
 */

const props = withDefaults(
  defineProps<{
    menuItems: ActionMenuItem[];
  }>(),
  {
    menuItems: () => [],
  },
);

const menu = ref();

const showMenu = (event: Event) => {
  menu.value.toggle(event);
};
</script>

<template>
  <div class="relative">
    <div class="hidden flex-wrap justify-center gap-1 xl:flex">
      <slot />
    </div>

    <div class="flex justify-center xl:hidden">
      <Button
        icon="pi pi-ellipsis-v"
        class="p-button-rounded p-button-text"
        @click="showMenu"
        aria-label="Actions menu"
      />
      <Menu ref="menu" :model="menuItems" :popup="true" class="min-w-[8rem]" />
    </div>
  </div>
</template>
