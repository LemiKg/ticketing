<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Services\User\Interfaces\UserListServiceInterface;
use App\Services\User\Interfaces\UserManageServiceInterface;
use App\Services\User\UserListService;
use App\Services\User\UserManageService;
use App\Services\Permission\Interfaces\PermissionServiceInterface;
use App\Services\Permission\PermissionService;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    // User services
    $this->app->bind(UserListServiceInterface::class, UserListService::class);
    $this->app->bind(
      UserManageServiceInterface::class,
      UserManageService::class
    );

    // Permission services
    $this->app->bind(
      PermissionServiceInterface::class,
      PermissionService::class
    );
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Vite::prefetch(concurrency: 3);
  }
}
