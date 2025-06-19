export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string | null;
  created_at?: string;
  updated_at?: string;
  roles?: Role[];
  permissions?: Permission[];
  // Additional fields that might be useful in a boilerplate
  avatar?: string;
  phone?: string;
  status?: 'active' | 'inactive' | 'suspended';
  last_login_at?: string;
  profile?: UserProfile;
}

export interface UserProfile {
  bio?: string;
  timezone?: string;
  language?: string;
  theme?: 'light' | 'dark' | 'auto';
  notifications_enabled?: boolean;
}

export interface Permission {
  id: number;
  name: string;
  guard_name?: string;
  created_at?: string;
  updated_at?: string;
}

export interface Role {
  id: number;
  name: string;
  guard_name?: string;
  permissions: Permission[];
  users?: User[];
  created_at?: string;
  updated_at?: string;
}

// Pagination interfaces
export interface PaginationLink {
  url?: string;
  label: string;
  active: boolean;
}

export interface PaginatedData<T> {
  data: T[];
  current_page: number;
  first_page_url: string;
  from?: number;
  last_page: number;
  last_page_url: string;
  links: PaginationLink[];
  next_page_url?: string;
  path: string;
  per_page: number;
  prev_page_url?: string;
  to?: number;
  total: number;
}

// API Response interfaces
export interface ApiResponse<T> {
  success: boolean;
  message?: string;
  data?: T;
  errors?: Record<string, string[]>;
}

// Navigation Menu interfaces
export interface MenuItem {
  title: string;
  icon?: string;
  route?: string;
  children?: MenuItem[];
}

export interface PrimeVueMenuItem {
  key: string;
  label: string;
  icon?: string;
  to?: string;
  rawRoute?: string;
  items?: PrimeVueMenuItem[];
}

// Action Menu interfaces (for ResponsiveActionButtons)
export interface ActionMenuItem {
  label: string;
  icon: string;
  command: () => void;
}

// Composable Options interfaces
export interface FlashOptions {
  i18nPrefix?: string;
  successKey?: string;
  errorKey?: string;
}

export interface CrudOptions {
  resourceRouteName: string;
  i18nPrefix?: string;
  deleteConfirmationTitle?: string;
  deleteConfirmationMessage?: string;
}

export interface DataTableOptions {
  searchable?: boolean;
  sortable?: boolean;
  filterable?: boolean;
  exportable?: boolean;
  perPageOptions?: number[];
  defaultPerPage?: number;
  initialSortField?: string;
  initialSortOrder?: string;
  initialFilters?: Record<string, any>;
}

// Filter interfaces for DataTable
export interface FilterValue {
  value: any;
  matchMode: string;
}

export interface FilterValueObject {
  constraints: FilterValue[];
  operator: string;
}

export interface Filters {
  [key: string]: FilterValue | FilterValueObject;
}

// Form Data Interfaces
export interface RoleFormData {
  name: string;
  permissions: string[];
  [key: string]: any;
}

export interface UserFormData {
  name: string;
  email: string;
  password?: string;
  password_confirmation?: string;
  roles: string[];
  phone?: string;
  status?: 'active' | 'inactive' | 'suspended';
  avatar?: string;
  [key: string]: any;
}

// Form Error Interfaces
export interface RoleFormErrors {
  name?: string;
  permissions?: string;
  [key: string]: string | undefined;
}

export interface UserFormErrors {
  name?: string;
  email?: string;
  password?: string;
  password_confirmation?: string;
  roles?: string;
  phone?: string;
  status?: string;
  avatar?: string;
  [key: string]: string | undefined;
}

// Inertia Form Type Extension
export interface InertiaForm<
  TForm = Record<string, any>,
  TErrors = Record<string, string>,
> {
  data: TForm;
  errors: TErrors;
  hasErrors: boolean;
  processing: boolean;
  progress: { percentage: number } | null;
  wasSuccessful: boolean;
  recentlySuccessful: boolean;
  transform: (callback: (data: TForm) => TForm) => InertiaForm<TForm, TErrors>;
  defaults(): InertiaForm<TForm, TErrors>;
  defaults(
    field: keyof TForm,
    value: TForm[keyof TForm],
  ): InertiaForm<TForm, TErrors>;
  defaults(fields: Partial<TForm>): InertiaForm<TForm, TErrors>;
  reset(): InertiaForm<TForm, TErrors>;
  reset(...fields: (keyof TForm)[]): InertiaForm<TForm, TErrors>;
  clearErrors(): InertiaForm<TForm, TErrors>;
  clearErrors(...fields: (keyof TErrors)[]): InertiaForm<TForm, TErrors>;
  setError(field: keyof TErrors, value: string): InertiaForm<TForm, TErrors>;
  setError(errors: Partial<TErrors>): InertiaForm<TForm, TErrors>;
  submit(method: string, url: string, options?: any): void;
  get(url: string, options?: any): void;
  post(url: string, options?: any): void;
  put(url: string, options?: any): void;
  patch(url: string, options?: any): void;
  delete(url: string, options?: any): void;
  cancel(): void;
  [key: string]: any;
}
