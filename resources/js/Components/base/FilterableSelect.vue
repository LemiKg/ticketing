<script setup lang="ts">
/**
 * FilterableSelect - A reusable, configurable select component with filtering capabilities
 *
 * This component wraps PrimeVue's Select component to provide a consistent, filterable
 * dropdown interface with flexible display options including avatars, primary/secondary
 * fields, and conditional field rendering.
 *
 * @component
 * @example
 * Basic usage with employee data:
 * ```vue
 * <FilterableSelect
 *   v-model="selectedEmployee"
 *   :options="employees"
 *   option-label="name"
 *   option-value="id"
 *   placeholder="Select an employee"
 *   :display-config="{
 *     primaryField: 'name',
 *     secondaryFields: [
 *       { field: 'email', label: 'Email: ' },
 *       { field: 'department', label: 'Dept: ' }
 *     ],
 *     avatar: { field: 'name', fallback: 'Employee' }
 *   }"
 * />
 * ```
 *
 * @example
 * Advanced usage with conditional fields:
 * ```vue
 * <FilterableSelect
 *   v-model="selectedUser"
 *   :options="users"
 *   option-label="name"
 *   option-value="id"
 *   :display-config="{
 *     primaryField: 'name',
 *     secondaryFields: [
 *       { field: 'email', condition: 'email' }, // Only show if email exists
 *       { field: 'phone', condition: 'phone', label: 'Tel: ' }
 *     ],
 *     avatar: { field: 'name' }
 *   }"
 * />
 * ```
 *
 * @features
 * - Built-in filtering capability
 * - Flexible display configuration
 * - Avatar support with fallbacks
 * - Primary and secondary field display
 * - Conditional field rendering
 * - Dot notation support for nested fields
 * - TypeScript support with full type safety
 *
 * @requires PrimeVue Select component
 * @author TimeHive Development Team
 * @version 1.0.0
 */
import Select from 'primevue/select';

/**
 * Configuration for secondary fields displayed in the select option
 */
interface SecondaryFieldConfig {
  /** The field path to display (supports dot notation like 'user.email') */
  field: string;
  /** Optional label prefix for the field */
  label?: string;
  /** Optional condition field - secondary field only shows if this field has a truthy value */
  condition?: string;
}

/**
 * Configuration for avatar display in select options
 */
interface AvatarConfig {
  /** The field path to use for avatar text (supports dot notation) */
  field: string;
  /** Fallback text to use if the field is empty */
  fallback?: string;
}

/**
 * Display configuration for the FilterableSelect component
 */
interface DisplayConfig {
  /** The primary field to display prominently (supports dot notation like 'name' or 'user.name') */
  primaryField: string;
  /** Optional secondary fields to display below the primary field */
  secondaryFields?: SecondaryFieldConfig[];
  /** Optional avatar configuration to show circular avatars */
  avatar?: AvatarConfig;
}

/**
 * Props interface for the FilterableSelect component
 */
interface FilterableSelectProps {
  /** The selected value (should match optionValue field of selected option) */
  modelValue: any;
  /** Array of options to display in the select */
  options: any[];
  /** Field name to use as the display label for filtering (not shown to user) */
  optionLabel: string;
  /** Field name to use as the unique value for each option */
  optionValue: string;
  /** Placeholder text when no option is selected */
  placeholder?: string;
  /** Whether the select should display in an invalid state */
  invalid?: boolean;
  /** Configuration object defining how options should be displayed */
  displayConfig: DisplayConfig;
}

/**
 * Default values for optional props
 */
const props = withDefaults(defineProps<FilterableSelectProps>(), {
  placeholder: 'Select an option',
  invalid: false,
});

/**
 * Emitted events from the component
 */
const emit = defineEmits<{
  'update:modelValue': [value: any];
}>();

/**
 * Safely retrieves a nested field value from an object using dot notation
 *
 * @param obj - The object to retrieve the value from
 * @param field - The field path (supports dot notation like 'user.name')
 * @returns The field value or undefined if the path doesn't exist
 */
const getFieldValue = (obj: any, field: string) => {
  return field.split('.').reduce((o, key) => o?.[key], obj);
};

/**
 * Finds the selected option object from the options array based on the current value
 *
 * @param value - The current selected value to match against
 * @returns The option object that matches the value, or undefined if not found
 */
const getSelectedOption = (value: any) => {
  return props.options.find(
    (option) => getFieldValue(option, props.optionValue) === value,
  );
};

/**
 * Generates avatar text from the configured avatar field
 *
 * @param option - The option object to generate avatar text for
 * @returns The first character (uppercased) of the avatar field value, fallback, or 'A'
 */
const getAvatarText = (option: any) => {
  if (!props.displayConfig.avatar) return 'A';

  const avatarField = props.displayConfig.avatar.field;
  const text =
    getFieldValue(option, avatarField) ||
    props.displayConfig.avatar.fallback ||
    'A';
  return text.toString()[0].toUpperCase();
};

/**
 * Determines whether a secondary field should be displayed based on its condition
 *
 * @param option - The option object to check
 * @param secondaryField - The secondary field configuration
 * @returns True if the field should be shown, false otherwise
 */
const shouldShowSecondaryField = (option: any, secondaryField: any) => {
  if (!secondaryField.condition) return true;
  return !!getFieldValue(option, secondaryField.condition);
};
</script>

<template>
  <Select
    :model-value="modelValue"
    :options="options"
    :option-label="optionLabel"
    :option-value="optionValue"
    :placeholder="placeholder"
    :invalid="invalid"
    filter
    @update:model-value="emit('update:modelValue', $event)"
  >
    <template #value="slotProps">
      <div v-if="slotProps.value" class="flex items-center">
        <div>
          <div class="font-medium">
            {{
              getFieldValue(
                getSelectedOption(slotProps.value),
                displayConfig.primaryField,
              )
            }}
          </div>
          <div
            v-if="
              displayConfig.secondaryFields &&
              getSelectedOption(slotProps.value)
            "
            class="text-xs text-gray-500"
          >
            <template
              v-for="(secondaryField, index) in displayConfig.secondaryFields"
              :key="secondaryField.field"
            >
              <span
                v-if="
                  shouldShowSecondaryField(
                    getSelectedOption(slotProps.value),
                    secondaryField,
                  )
                "
                :class="{
                  'ml-2':
                    index > 0 &&
                    displayConfig
                      .secondaryFields!.slice(0, index)
                      .some((sf) =>
                        shouldShowSecondaryField(
                          getSelectedOption(slotProps.value),
                          sf,
                        ),
                      ),
                }"
              >
                {{ secondaryField.label || ''
                }}{{
                  getFieldValue(
                    getSelectedOption(slotProps.value),
                    secondaryField.field,
                  )
                }}
              </span>
            </template>
          </div>
        </div>
      </div>
      <span v-else>
        {{ slotProps.placeholder }}
      </span>
    </template>

    <!-- 
      Custom template for rendering each option in the dropdown.
      Includes optional avatar and structured field display.
    -->
    <template #option="slotProps">
      <div class="flex items-center space-x-3">
        <!-- Avatar circle with first letter of configured field -->
        <div v-if="displayConfig.avatar" class="flex-shrink-0">
          <div
            class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300"
          >
            <span class="text-sm font-medium text-gray-600">
              {{ getAvatarText(slotProps.option) }}
            </span>
          </div>
        </div>
        <!-- Primary and secondary field display -->
        <div class="min-w-0 flex-1">
          <div class="font-medium text-gray-900 dark:text-gray-100">
            {{ getFieldValue(slotProps.option, displayConfig.primaryField) }}
          </div>
          <div
            v-if="displayConfig.secondaryFields"
            class="text-xs text-gray-500"
          >
            <template
              v-for="(secondaryField, index) in displayConfig.secondaryFields"
              :key="secondaryField.field"
            >
              <span
                v-if="
                  shouldShowSecondaryField(slotProps.option, secondaryField)
                "
                :class="{
                  'ml-2':
                    index > 0 &&
                    displayConfig
                      .secondaryFields!.slice(0, index)
                      .some((sf) =>
                        shouldShowSecondaryField(slotProps.option, sf),
                      ),
                }"
              >
                {{ secondaryField.label || ''
                }}{{ getFieldValue(slotProps.option, secondaryField.field) }}
              </span>
            </template>
          </div>
        </div>
      </div>
    </template>
  </Select>
</template>
