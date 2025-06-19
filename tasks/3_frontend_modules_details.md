````markdown
# 3. Frontend Modules (Vue 3 + PrimeVue v4) – Detailed Guide

Build Vue feature modules as installable npm packages, leveraging **DRY principles**, **composables**, **templates**, and styling with **PrimeVue v4**. Each module encapsulates its components, routes, state, and UI patterns for rapid reuse.

---

## Table of Contents

1. [Overview & Benefits](#overview--benefits)
2. [Directory Structure](#directory-structure)
3. [package.json Configuration](#packagejson-configuration)
4. [Template & Styling Conventions](#template--styling-conventions)
5. [Composable Utilities](#composable-utilities)
6. [Vue Plugin Architecture](#vue-plugin-architecture)
7. [Components, Routes & Store](#components-routes--store)
8. [Using PrimeVue v4](#using-primevue-v4)
9. [Build & Distribution](#build--distribution)
10. [Publishing to npm](#publishing-to-npm)
11. [Consuming Modules in Your App](#consuming-modules-in-your-app)

---

## Overview & Benefits

- **DRY (Don't Repeat Yourself)**: Shared templates and composables minimize duplication.
- **Modularity**: Install via `npm install @vendor/module`.
- **Templates & Composables**: Standardize UI and logic patterns.
- **PrimeVue v4**: Leverage a rich UI library for consistent look & feel.

---

## Directory Structure

```text
packages/frontend/blog/
├─ package.json
├─ src/
│   ├─ index.js             # Plugin entrypoint
│   ├─ router.js            # Route definitions
│   ├─ store.js             # Vuex store module
│   ├─ templates/           # Reusable component templates (DRY)
│   │   └─ ListLayout.vue
│   ├─ composables/         # Reusable logic (DRY)
│   │   └─ usePosts.js
│   └─ components/
│       ├─ BlogList.vue     # Uses ListLayout + PrimeVue
│       └─ BlogPost.vue     # Uses PrimeVue Form
└─ README.md
````

- **templates/**: Generic layouts (e.g., lists, detail views).
- **composables/**: Encapsulated logic (e.g., data fetching, pagination).

---

## package.json Configuration

```json5
{
  "name": "@your-vendor/blog-module",
  "version": "1.0.0",
  "description": "Vue 3 blog feature module with PrimeVue v4",
  "main": "dist/index.js",
  "scripts": {
    "build": "vite build --outDir dist"
  },
  "peerDependencies": {
    "vue": "^3.2",
    "vuex": "^4.0",
    "vue-router": "^4.0",
    "primevue": "^4.0"
  },
  "devDependencies": {
    "vite": "^4.0",
    "@vitejs/plugin-vue": "^4.0"
  }
}
```

---

## Template & Styling Conventions

- **Templates** live under `templates/` and use `<slot>`s to inject content.
- Example **ListLayout.vue**:
  ```vue
  <template>
    <div class="list-layout">
      <h2><slot name="title"/></h2>
      <div class="list-content">
        <slot/>
      </div>
    </div>
  </template>

  <script setup>
  // minimal logic; styling via PrimeVue classes
  </script>

  <style scoped>
  .list-layout { padding: 1rem; }
  </style>
  ```
- Child components (`BlogList.vue`) use:
  ```vue
  <template>
    <ListLayout>
      <template #title>Blog Posts</template>
      <ul>
        <li v-for="post in posts" :key="post.id">
          <router-link :to="`/blog/${post.id}`">{{ post.title }}</router-link>
        </li>
      </ul>
    </ListLayout>
  </template>
  ```

---

## Composable Utilities

- Place reusable logic in `composables/` following the **useX** convention.
- Example **usePosts.js**:
  ```js
  import { ref, onMounted } from 'vue'
  import { useStore } from 'vuex'

  export function usePosts() {
    const store = useStore()
    const posts = ref([])
    const loading = ref(false)

    async function fetchPosts() {
      loading.value = true
      posts.value = await store.dispatch('blog/fetchPosts')
      loading.value = false
    }

    onMounted(fetchPosts)
    return { posts, loading, fetchPosts }
  }
  ```
- Components consume:
  ```js
  import { usePosts } from '../composables/usePosts'
  const { posts, loading } = usePosts()
  ```

---

## Vue Plugin Architecture

```js
// src/index.js
import router from './router'
import store from './store'

export default {
  install(app, options = {}) {
    // 1) Router
    options.router?.addRoute({ path: '/blog', component: () => import('./components/BlogList.vue') })
    options.router?.addRoute({ path: '/blog/:id', component: () => import('./components/BlogPost.vue') })

    // 2) Store
    options.store?.registerModule('blog', store)

    // 3) Global templates/components
    app.component('ListLayout', () => import('./templates/ListLayout.vue'))

    // 4) Use PrimeVue plugin for UI
    app.use(options.primevue)
  }
}
```

---

## Components, Routes & Store

### router.js

```js
export default [
  { path: '/blog', name: 'BlogList', component: () => import('./components/BlogList.vue') },
  { path: '/blog/:id', name: 'BlogPost', component: () => import('./components/BlogPost.vue') }
]
```

### store.js

```js
export default {
  namespaced: true,
  state: () => ({ posts: [] }),
  actions: {
    async fetchPosts({ commit }) {
      const res = await fetch('/api/blog')
      const data = await res.json()
      commit('setPosts', data)
      return data
    }
  },
  mutations: {
    setPosts(state, posts) { state.posts = posts }
  }
}
```

---

## Using PrimeVue v4

1. **Install**:
   ```bash
   npm install primevue@^4.0 primeicons
   ```
2. **Import styles** in your host app or module:
   ```js
   import 'primevue/resources/themes/saga-blue/theme.css'
   import 'primevue/resources/primevue.min.css'
   import 'primeicons/primeicons.css'
   ```
3. **Use components**:
   ```vue
   <template>
     <ListLayout>
       <template #title>
         <Button icon="pi pi-plus" label="New Post" />
       </template>
       <DataTable :value="posts">
         <Column field="title" header="Title" />
       </DataTable>
     </ListLayout>
   </template>

   <script>
   import Button from 'primevue/button'
   import DataTable from 'primevue/datatable'
   import Column from 'primevue/column'
   import { usePosts } from '../composables/usePosts'

   export default {
     components: { Button, DataTable, Column },
     setup() {
       const { posts, loading } = usePosts()
       return { posts, loading }
     }
   }
   </script>
   ```

---

## Build & Distribution

At repo root:

```bash
npm run build --workspace=@your-vendor/blog-module
```

Outputs `dist/` for publication.

---

## Publishing to npm

1. Tag version: `npm version patch`
2. Publish: `npm publish --access public`

---

## Consuming Modules in Your App

```bash
npm install primevue@^4.0 @your-vendor/blog-module
```

```js
// apps/web/src/main.js
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import PrimeVue from 'primevue/config'
import BlogModule from '@your-vendor/blog-module'

const app = createApp(App)
app.use(PrimeVue)
app.use(router)
app.use(store)
app.use(BlogModule, { router, store, primevue: PrimeVue })
app.mount('#app')
```

---

*With templates, composables, DRY design, and PrimeVue v4 integration, your frontend modules become robust, reusable, and stylistically consistent.*

```
```
