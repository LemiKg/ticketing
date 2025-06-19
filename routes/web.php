<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group([], function () {
  Route::inertia('/', 'website/Landing')->name('home');
  Route::inertia('/privacy', 'website/Privacy')->name('privacy');
  Route::inertia('/cookies', 'website/Cookies')->name('cookies');
  Route::inertia('/terms', 'website/Terms')->name('terms');
});
Route::middleware(['auth'])->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['verified'])
    ->name('dashboard');

  Route::controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'edit')->name('profile.edit');
    Route::patch('/profile', 'update')->name('profile.update');
    Route::delete('/profile', 'destroy')->name('profile.destroy');
  });

  Route::prefix('users')
    ->controller(UserController::class)
    ->group(function () {
      Route::get('/count', 'count')->name('users.count');

      Route::get('/', 'index')->name('users.index');
      Route::get('/create', 'create')->name('users.create');
      Route::post('/', 'store')->name('users.store');
      Route::get('/{user}', 'show')->name('users.show');
      Route::get('/{user}/edit', 'edit')->name('users.edit');
      Route::put('/{user}', 'update')->name('users.update');
      Route::delete('/{user}', 'destroy')->name('users.destroy');
    });

  // Role management routes - Only for super-admin
  Route::prefix('roles')
    ->controller(RoleController::class)
    ->middleware('role:super-admin')
    ->group(function () {
      Route::get('/count', 'count')->name('roles.count');
      Route::post('/{role}/sync-permissions', 'syncPermissions')->name(
        'roles.syncPermissions'
      );

      Route::get('/', 'index')->name('roles.index');
      Route::get('/create', 'create')->name('roles.create');
      Route::post('/', 'store')->name('roles.store');
      Route::get('/{role}', 'show')->name('roles.show');
      Route::get('/{role}/edit', 'edit')->name('roles.edit');
      Route::put('/{role}', 'update')->name('roles.update');
      Route::delete('/{role}', 'destroy')->name('roles.destroy');
    });

  Route::post('/contact', [ContactController::class, 'submit'])->name(
    'contact.submit'
  );
});

require __DIR__ . '/auth.php';
