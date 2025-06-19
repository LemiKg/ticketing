````markdown
# 4. CI/CD & Publishing – Best Practices and Examples

Automate your module’s quality checks, builds, and releases with GitHub Actions for both backend and frontend packages.

---

## Table of Contents

1. [Overview](#overview)
2. [Versioning Strategy](#versioning-strategy)
3. [GitHub Actions – Backend Module](#github-actions--backend-module)
   - Workflow File Breakdown
4. [GitHub Actions – Frontend Module](#github-actions--frontend-module)
   - Workflow File Breakdown
5. [Secrets & Permissions](#secrets--permissions)
6. [Release Automation](#release-automation)
7. [Rollbacks & Rollforward](#rollbacks--rollforward)

---

## Overview

Implement CI/CD pipelines that automatically:
- Lint code and styles
- Run unit/feature tests
- Build distributable artifacts
- Publish to registries (Packagist, npm)

---

## Versioning Strategy

- **Semantic Versioning (SemVer)**: `MAJOR.MINOR.PATCH`
  - **MAJOR**: Breaking changes
  - **MINOR**: New, backward-compatible features
  - **PATCH**: Bug fixes and minor improvements
- Tag format: `v1.2.3`
- Automate bumping via `npm version` or `composer version`

---

## GitHub Actions – Backend Module

### Workflow File: `.github/workflows/publish-backend.yml`

```yaml
name: CI & Publish Backend Module
on:
  push:
    tags:
      - 'v*.*.*'
jobs:
  test-build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-suggest
      - name: Run Static Analysis
        run: vendor/bin/phpstan analyse
      - name: Run Tests
        run: vendor/bin/phpunit --coverage-text

  publish:
    needs: test-build
    runs-on: ubuntu-latest
    if: startsWith(github.ref, 'refs/tags/')
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install --no-interaction
      - name: Publish to Packagist
        env:
          COMPOSER_AUTH: ${{ secrets.COMPOSER_AUTH }}
        run: composer publish
````

#### Workflow File Breakdown

- **Triggers**: On push of tag matching `v*.*.*`.
- **test-build**: Lint (`phpstan`) and run tests (`phpunit`).
- **publish**: Runs only if tests pass and on a tag push, publishing via `composer publish`.

---

## GitHub Actions – Frontend Module

### Workflow File: `.github/workflows/publish-frontend.yml`

```yaml
name: CI & Publish Frontend Module
on:
  push:
    tags:
      - 'v*.*.*'
jobs:
  test-build:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        node-version: [16, 18]
    steps:
      - uses: actions/checkout@v3
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: ${{ matrix.node-version }}
      - name: Install dependencies
        run: npm ci
      - name: Run lint
        run: npm run lint
      - name: Run tests
        run: npm test
      - name: Build
        run: npm run build

  publish:
    needs: test-build
    runs-on: ubuntu-latest
    if: startsWith(github.ref, 'refs/tags/')
    steps:
      - uses: actions/checkout@v3
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: 18
      - run: npm ci
      - run: npm run build
      - name: Publish to npm
        env:
          NODE_AUTH_TOKEN: ${{ secrets.NPM_TOKEN }}
        run: npm publish --access public
```

#### Workflow File Breakdown

- **Matrix Testing**: Ensures compatibility across Node.js 16 & 18.
- **Lint & Tests**: Runs code style and unit tests.
- **Build & Publish**: Only on tag push, publishing with `npm publish`.

---

## Secrets & Permissions

- **COMPOSER\_AUTH**: In GitHub secrets, for Packagist API token.
- **NPM\_TOKEN**: In GitHub secrets, for npm publish.
- **Least Privilege**: Tokens scoped to only allow publishing.

---

## Release Automation

- Use `actions/github-script` or `release-drafter` to auto-generate changelogs.
- Integrate with semantic-release for fully automated version bumps and tagging.

---

## Rollbacks & Rollforward

- **Rollback**: Revert to previous tag and republish if needed.
- **Rollforward**: Patch a new release (e.g., `v1.0.1`) with fixes.

---

*With these pipelines, your modules maintain high quality and are published reliably on every release tag.*

```
```
