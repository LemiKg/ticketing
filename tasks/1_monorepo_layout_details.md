````markdown
# 1. Monorepo Layout (Local Development)

A **monorepo** (mono repository) centralizes all related code—modules, libraries, and example applications—into a single Git repository. This approach simplifies cross‑module refactoring, version consistency, and local development workflows.

---

## Table of Contents

1. [Why a Monorepo?](#why-a-monorepo)
2. [Directory Structure](#directory-structure)
3. [Root-Level Configuration](#root-level-configuration)
   - [composer.json (Backend)](#composerjson-backend)
   - [package.json (Frontend)](#packagejson-frontend)
4. [Adding New Modules](#adding-new-modules)
   - [Backend Module Example](#backend-module-example)
   - [Frontend Module Example](#frontend-module-example)
5. [Local Linking & Workspaces](#local-linking--workspaces)
6. [Bootstrapping Consumer Apps](#bootstrapping-consumer-apps)
7. [Common Pitfalls & Tips](#common-pitfalls--tips)

---

## Why a Monorepo?

- **Atomic Changes**: Refactor code across multiple modules in one commit, preventing mismatched versions.
- **Single Source of Truth**: Manage dependencies centrally—no drift between module versions.
- **Streamlined Dev Workflow**: Instant symlinks via path repos and workspaces, eliminating the need to publish packages during active development.

---

## Directory Structure

```text
/my-platform/                    # Git root
├─ .gitignore                    # Ignore node_modules, vendor, etc.
├─ composer.json                 # Root PHP config (+ path repos)
├─ package.json                  # Root JS config (+ workspaces)
├─ packages/                     # All reusable modules
│   ├─ backend/                  # Laravel module packages
│   │   ├─ User/                 # your-vendor/user-module
│   │   └─ Blog/                 # your-vendor/blog-module
│   └─ frontend/                 # Vue plugin packages
│       ├─ user/                 # @your-vendor/user-module
│       └─ blog/                 # @your-vendor/blog-module
└─ apps/                         # Example consumer applications
    ├─ api/                      # Laravel API consumer
    └─ web/                      # Vue SPA consumer
````

- **packages/backend/**: Each subfolder is a standalone Composer package.
- **packages/frontend/**: Each subfolder is a scoped npm package.
- **apps/**: Projects that consume and integrate modules for local testing.

---

## Root-Level Configuration

### composer.json (Backend)

*Centralizes PHP dependencies and defines path repositories for local modules.*

```json5
{
  "name": "my-platform/root",
  "description": "Monorepo root for Laravel + Vue platform",
  "repositories": [
    {
      "type": "path",
      "url": "packages/backend/*",
      "options": { "symlink": true }
    }
  ],
  "require": {
    "php": "^8.1",
    "laravel/framework": "^10.0"
  },
  "autoload": {
    "psr-4": { "App\\": "apps/api/app/" }
  }
}
```

- ``: Uses a filesystem symlink instead of Packagist—ideal for local development.
- ``: Ensures changes propagate instantly without re-running `composer update`.

### package.json (Frontend)

*Defines JS dependencies and workspace globs for Vue modules.*

```json5
{
  "name": "@my-platform/root",
  "private": true,
  "workspaces": [
    "packages/frontend/*"
  ],
  "devDependencies": {
    "vite": "^4.0",
    "vue": "^3.2",
    "@vitejs/plugin-vue": "^4.0"
  },
  "scripts": {
    "dev:web": "cd apps/web && vite",
    "dev:api": "cd apps/api && php artisan serve"
  }
}
```

- ``: Prevents accidental `npm publish` of the root.
- ``: Symlinks packages under `packages/frontend/` into the root `node_modules`.

---

## Adding New Modules

### Backend Module Example

1. **Scaffold Package**

   ```bash
   mkdir -p packages/backend/Order/src
   cd packages/backend/Order
   composer init --name="your-vendor/order-module" --type=library --require="php:^8.1" --autoload
   ```

2. **Define composer.json**

   ```json5
   {
     "name": "your-vendor/order-module",
     "description": "Order management module",
     "type": "library",
     "autoload": {
       "psr-4": { "YourVendor\\Order\\": "src/" }
     },
     "extra": {
       "laravel": {
         "providers": [
           "YourVendor\\Order\\Providers\\OrderServiceProvider"
         ]
       }
     }
   }
   ```

3. **Create Service Provider**

   ```php
   // packages/backend/Order/src/Providers/OrderServiceProvider.php
   namespace YourVendor\Order\Providers;

   use Illuminate\Support\ServiceProvider;

   class OrderServiceProvider extends ServiceProvider
   {
       public function boot(): void
       {
           $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
           $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
       }

       public function register(): void
       {
           // Bind interfaces, publish configs, etc.
       }
   }
   ```

4. **Add Routes & Migrations**

   ```php
   // packages/backend/Order/routes/api.php
   use Illuminate\Support\Facades\Route;
   Route::get('orders', 'YourVendor\Order\Http\Controllers\OrderController@index');
   ```

   ```bash
   # Migration example
   mkdir -p packages/backend/Order/database/migrations
   touch packages/backend/Order/database/migrations/2025_06_15_000000_create_orders_table.php
   ```

### Frontend Module Example

1. **Scaffold Package**

   ```bash
   mkdir -p packages/frontend/order/src/components
   cd packages/frontend/order
   npm init --scope=@your-vendor --yes
   npm install vue-router vuex --save-peer
   ```

2. **Define package.json**

   ```json5
   {
     "name": "@your-vendor/order-module",
     "version": "1.0.0",
     "main": "dist/index.js",
     "scripts": { "build": "vite build" },
     "peerDependencies": { "vue": "^3.2" }
   }
   ```

3. **Plugin Entry**

   ```js
   // packages/frontend/order/src/index.js
   import OrderList from './components/OrderList.vue';
   export default {
     install(app, { router, store }) {
       app.component('OrderList', OrderList);
       router.addRoute({ path: '/orders', component: OrderList });
       store.registerModule('orders', require('./store').default);
     }
   };
   ```

4. **Build & Link**

   ```bash
   # From monorepo root
   yarn workspace @your-vendor/order-module build
   ```

---

## Local Linking & Workspaces

- **Backend**: `composer require your-vendor/order-module:@dev` uses path repo symlink.
- **Frontend**: `yarn workspace apps/web add @your-vendor/order-module` picks up local workspace.

Run from root:

```bash
composer update
yarn install
```

---

## Bootstrapping Consumer Apps

### apps/api Bootstrap (Laravel)

```bash
cd apps/api
composer install
php artisan migrate
php artisan serve
```

- Laravel auto-discovers and registers any module providers defined in `composer.json` under `packages/backend/`.

### apps/web Bootstrap (Vue)

```bash
cd apps/web
yarn install
yarn dev
```

- Vite and Vue detect workspace modules under `packages/frontend/` and auto-load them via `import.meta.glob` in `main.js`.

---

## Common Pitfalls & Tips

- **Circular Dependencies**: Avoid module A importing B if B also depends on A.
- **Cache Clearing**: After adding/removing backend modules, run `php artisan config:clear` and `php artisan route:clear`.
- **TypeScript Typings**: For TS projects, include `.d.ts` in each frontend module for strong typing.
- **Version Mismatch**: Lock major versions in peerDependencies to prevent breaking changes.

*This guide equips you with a fully detailed Monorepo setup—accelerating development and ensuring robust modularity across your Laravel + Vue applications.*

```
```
