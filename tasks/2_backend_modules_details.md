```markdown
# 2. Backend Modules (Laravel) – In-Depth Guide

This document covers everything you need to know to build, configure, and publish Laravel backend modules as standalone Composer packages.

---

## Table of Contents

1. [Overview & Benefits](#overview--benefits)
2. [Directory Layout & Boilerplate](#directory-layout--boilerplate)
3. [composer.json Configuration](#composerjson-configuration)
4. [AbstractModuleServiceProvider](#abstractmoduleserviceprovider)
5. [Routing & Controllers](#routing--controllers)
6. [Models, Migrations & Factories](#models-migrations--factories)
7. [Views, Configuration & Assets](#views-configuration--assets)
8. [Testing Your Module](#testing-your-module)
9. [Publishing to Packagist](#publishing-to-packagist)

---

## Overview & Benefits

- **Encapsulation**: Each feature (e.g., Blog, Shop) lives in its own namespace and repository.
- **Reusability**: Install via `composer require vendor/module` in any Laravel project.
- **Versioning**: Independently versioned and released.
- **Auto-Discovery**: Laravel auto-registers your ServiceProvider via Composer.

---

## Directory Layout & Boilerplate

```text
packages/backend/Blog/
├─ composer.json
├─ src/
│   ├─ Providers/
│   │   └─ BlogServiceProvider.php
│   ├─ Http/
│   │   ├─ Controllers/
│   │   │   └─ PostController.php
│   │   └─ Middleware/
│   ├─ Models/
│   │   └─ Post.php
│   └─ Policies/
│       └─ PostPolicy.php
├─ routes/
│   ├─ web.php
│   └─ api.php
├─ database/
│   ├─ migrations/
│   │   └─ 2025_01_01_000000_create_posts_table.php
│   └─ factories/
│       └─ PostFactory.php
├─ resources/
│   ├─ views/
│   │   └─ blog/index.blade.php
│   └─ lang/
│       └─ en/blog.php
├─ public/
│   └─ assets/blog.js
└─ config/
    └─ blog.php
```

- Organize by feature (Providers, Http, Models, etc.) to follow PSR-4 and Laravel conventions.
- Include `public/` for module-specific JavaScript/CSS.

---

## composer.json Configuration

```json5
{
  "name": "your-vendor/blog-module",
  "description": "Blog functionality module",
  "type": "library",
  "license": "MIT",
  "autoload": {
    "psr-4": {
      "YourVendor\\Blog\\": "src/"
    }
  },
  "extra": {
    "laravel": {
      "providers": [
        "YourVendor\\Blog\\Providers\\BlogServiceProvider"
      ]
    }
  },
  "require": {
    "php": "^8.1",
    "illuminate/support": "^10.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^9.0",
    "orchestra/testbench": "^7.0"
  }
}
```

- **`illuminate/support`**: For core Laravel classes without full framework.
- **`orchestra/testbench`**: Allows module testing outside a Laravel app.

---

## AbstractModuleServiceProvider

Centralizes repetitive tasks:

```php
namespace YourVendor\Core\Providers;

use Illuminate\Support\ServiceProvider;

abstract class AbstractModuleServiceProvider extends ServiceProvider
{
    abstract protected string $moduleName;
    abstract protected string $modulePath;

    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            "{$this->modulePath}/config/{$this->moduleName}.php" => config_path("{$this->moduleName}.php")
        ], 'config');

        // Load routes
        $this->loadRoutesFrom("{$this->modulePath}/routes/web.php");
        $this->loadRoutesFrom("{$this->modulePath}/routes/api.php");

        // Load migrations & factories
        $this->loadMigrationsFrom("{$this->modulePath}/database/migrations");
        $this->loadFactoriesFrom("{$this->modulePath}/database/factories");

        // Load views and translations
        $this->loadViewsFrom("{$this->modulePath}/resources/views", $this->moduleName);
        $this->loadTranslationsFrom("{$this->modulePath}/resources/lang", $this->moduleName);

        // Publish public assets
        $this->publishes([
            "{$this->modulePath}/public" => public_path("vendor/{$this->moduleName}")
        ], 'public');
    }
}
```

Extend in your module:

```php
namespace YourVendor\Blog\Providers;

use YourVendor\Core\Providers\AbstractModuleServiceProvider;

class BlogServiceProvider extends AbstractModuleServiceProvider
{
    protected string $moduleName = 'blog';
    protected string $modulePath = __DIR__ . '/../../';
}
```

---

## Routing & Controllers

**routes/api.php**

```php
use Illuminate\Support\Facades\Route;
use YourVendor\Blog\Http\Controllers\PostController;

Route::prefix('api/blog')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::get('{id}', [PostController::class, 'show']);
});
```

**PostController.php**

```php
namespace YourVendor\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use YourVendor\Blog\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate(['title' => 'required', 'body' => 'required']);
        return Post::create($data);
    }

    public function show($id)
    {
        return Post::findOrFail($id);
    }
}
```

---

## Models, Migrations & Factories

**Post.php**

```php
namespace YourVendor\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body'];
}
```

**Migration example**

```php
// packages/backend/Blog/database/migrations/2025_01_01_000000_create_posts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
}
```

**Factory example**

```php
// packages/backend/Blog/database/factories/PostFactory.php
namespace YourVendor\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use YourVendor\Blog\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => \$this->faker->sentence,
            'body' => \$this->faker->paragraph
        ];
    }
}
```

---

## Views, Configuration & Assets

**Configuration file** (`config/blog.php`):

```php
return [
    'per_page' => 10,
    'enable_comments' => true
];
```

**Blade view** (`resources/views/blog/index.blade.php`):

```blade
@extends('layouts.app')

@section('content')
<div class="container">
  @foreach(\App\Models\Post::paginate(config('blog.per_page')) as $post)
    <h2>{{ \$post->title }}</h2>
    <p>{{ \$post->body }}</p>
  @endforeach
  {{ \$posts->links() }}
</div>
@endsection
```

**Assets**: Place JS/CSS in `public/assets/blog.js` and publish via provider.

---

## Testing Your Module

Use **Orchestra Testbench** for standalone testing:

```php
// tests/Feature/PostTest.php
namespace YourVendor\Blog\Tests;

use Orchestra\Testbench\TestCase;
use YourVendor\Blog\Providers\BlogServiceProvider;

class PostTest extends TestCase
{
    protected function getPackageProviders(\$app)
    {
        return [BlogServiceProvider::class];
    }

    public function test_can_create_post()
    {
        \$response = \$this->postJson('/api/blog', [
            'title' => 'Test',
            'body' => 'Content'
        ]);

        \$response->assertStatus(201)
                 ->assertJson(['title' => 'Test']);
    }
}
```

Run tests:

```bash
cd packages/backend/Blog
composer install
env-cmd vendor/bin/phpunit
```

---

## Publishing to Packagist

1. Tag a release:
   ```bash
git tag v1.0.0
git push --tags
```
2. Ensure `composer.json` has correct `"name"` on Packagist.
3. Add repository on Packagist dashboard or enable GitHub auto-sync.
4. Run:
   ```bash
composer require your-vendor/blog-module:^1.0
```

---

*Your Laravel backend modules are now fully modular, testable, and publishable across any project.*
```

