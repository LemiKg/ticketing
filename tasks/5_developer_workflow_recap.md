````markdown
# 5. Developer Workflow Recap – From Scaffold to Release

A concise guide to your end-to-end workflow: scaffold, link, develop, test, and publish modules.

---

## Table of Contents

1. [Scaffolding New Modules](#scaffolding-new-modules)
2. [Local Linking & Environment Setup](#local-linking--environment-setup)
3. [Development & Hot-Reload](#development--hot-reload)
4. [Testing & Quality Assurance](#testing--quality-assurance)
5. [Versioning & Changelogs](#versioning--changelogs)
6. [Release & Publishing](#release--publishing)
7. [Post-Release Maintenance](#post-release-maintenance)

---

## 1. Scaffolding New Modules

### Backend

```bash
php artisan make:module Shop
# Or use custom generator:
npx create-laravel-module your-vendor/shop-module
````

### Frontend

```bash
npx create-vue-plugin @your-vendor/shop-module
# Or manual:
mkdir -p packages/frontend/shop/src/components
npm init --scope=@your-vendor --yes
```

- Both generators should scaffold: `composer.json`/`package.json`, boilerplate code, and test setup.

---

## 2. Local Linking & Environment Setup

### Composer Path Repos

In root `composer.json`:

```json5
"repositories": [
  { "type": "path", "url": "packages/backend/*", "options": { "symlink": true } }
]
```

Install locally:

```bash
cd apps/api
composer require your-vendor/shop-module:@dev
```

### npm/Yarn Workspaces

In root `package.json`:

```json5
"workspaces": ["packages/frontend/*"]
```

Install locally:

```bash
cd apps/web
yarn add @your-vendor/shop-module
```

---

## 3. Development & Hot-Reload

- **Backend**: `php artisan serve --host=0.0.0.0 --port=8000`
- **Frontend**: `vite --host` or `yarn dev`
- **Live Reload**: Vite’s HMR picks up changes in `packages/frontend/*`
- **IDE Integration**: Configure VSCode/workspace `settings.json` to resolve symlinks for intellisense.

---

## 4. Testing & Quality Assurance

### Backend

```bash
cd packages/backend/shop
composer install
env-cmd vendor/bin/phpunit --coverage-text
```

- **Static Analysis**: `vendor/bin/phpstan analyse` or `vendor/bin/psalm`

### Frontend

```bash
cd packages/frontend/shop
yarn
yarn lint
yarn test
```

- **E2E Tests**: Use Cypress in `apps/web` to validate full flows.

---

## 5. Versioning & Changelogs

- Use **Conventional Commits** for commit messages:
  - `feat: add new endpoint`
  - `fix: resolve bug`
- Generate changelogs with **release-drafter** or **standard-version**.

---

## 6. Release & Publishing

1. **Bump Version**
   - Backend: `composer version patch`
   - Frontend: `npm version patch`
2. **Tag & Push**
   ```bash
   ```

git tag v1.0.1 git push && git push --tags

```
3. **CI/CD**: GitHub Actions picks up tag and publishes to registries.

---

## 7. Post-Release Maintenance

- Monitor issues & set up **notifications** (Slack, email).
- **Patch** critical bugs quickly and release hotfixes.
- **Deprecation**: Mark old major versions and guide users to upgrade.

---

*Follow this workflow to maintain high productivity and code quality across your modular Laravel + Vue platform.*
```

