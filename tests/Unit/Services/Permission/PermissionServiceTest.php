<?php

namespace Tests\Unit\Services\Permission;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Permission\PermissionService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionServiceTest extends TestCase
{
  use RefreshDatabase;

  private PermissionService $permissionService;

  protected function setUp(): void
  {
    parent::setUp();

    $this->permissionService = new PermissionService();

    Permission::create(['name' => 'view users', 'guard_name' => 'web']);
    Permission::create(['name' => 'create users', 'guard_name' => 'web']);
    Permission::create(['name' => 'edit users', 'guard_name' => 'web']);
    Permission::create(['name' => 'delete users', 'guard_name' => 'web']);

    Permission::create(['name' => 'view users', 'guard_name' => 'web']);
    Permission::create(['name' => 'create users', 'guard_name' => 'web']);
    Permission::create(['name' => 'edit users', 'guard_name' => 'web']);
    Permission::create(['name' => 'delete users', 'guard_name' => 'web']);

    Permission::create(['name' => 'view roles', 'guard_name' => 'web']);
    Permission::create(['name' => 'create roles', 'guard_name' => 'web']);
    Permission::create(['name' => 'edit roles', 'guard_name' => 'web']);
    Permission::create(['name' => 'delete roles', 'guard_name' => 'web']);

    Permission::create(['name' => 'access admin panel', 'guard_name' => 'web']);
    Permission::create(['name' => 'view system logs', 'guard_name' => 'web']);
    Permission::create(['name' => 'manage settings', 'guard_name' => 'web']);
    Permission::create(['name' => 'export reports', 'guard_name' => 'web']);
    Permission::create(['name' => 'delete reports', 'guard_name' => 'web']);

    // Create test roles matching the seeder
    Role::create(['name' => 'super-admin', 'guard_name' => 'web']);

    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $adminRole->givePermissionTo([
      'view users',
      'create users',
      'edit users',
      'delete users',
      'view roles',
      'create roles',
      'edit roles',
      'access admin panel',
    ]);

    $moderatorRole = Role::create([
      'name' => 'moderator',
      'guard_name' => 'web',
    ]);
    $moderatorRole->givePermissionTo([
      'view users',
      'edit users',
      'access admin panel',
    ]);

    $userRole = Role::create(['name' => 'user', 'guard_name' => 'web']);
  }

  public function test_get_all_roles()
  {
    $roles = $this->permissionService->getAllRoles();

    $this->assertCount(4, $roles);
    $this->assertTrue($roles->contains('name', 'super-admin'));
    $this->assertTrue($roles->contains('name', 'admin'));
    $this->assertTrue($roles->contains('name', 'moderator'));
    $this->assertTrue($roles->contains('name', 'user'));
  }

  public function test_get_all_permissions()
  {
    $permissions = $this->permissionService->getAllPermissions();

    $this->assertCount(11, $permissions);
    $this->assertTrue($permissions->contains('name', 'view users'));
    $this->assertTrue($permissions->contains('name', 'create users'));
    $this->assertTrue($permissions->contains('name', 'delete reports'));
  }

  public function test_get_permissions_by_role()
  {
    $adminPermissions = $this->permissionService->getPermissionsByRole('admin');
    $userPermissions = $this->permissionService->getPermissionsByRole('user');

    $this->assertCount(17, $adminPermissions);
    $this->assertTrue($adminPermissions->contains('view users'));
    $this->assertTrue($adminPermissions->contains('delete users'));

    $this->assertCount(1, $userPermissions);
    $this->assertTrue($userPermissions->contains('view content'));
  }

  public function test_assign_role_to_user()
  {
    $user = User::factory()->create();

    $this->permissionService->assignRoleToUser($user, 'user');
    $this->assertTrue($user->hasRole('user'));

    $this->permissionService->assignRoleToUser($user, ['admin', 'super-admin']);
    $this->assertTrue($user->hasRole('admin'));
    $this->assertTrue($user->hasRole('super-admin'));
  }

  public function test_remove_role_from_user()
  {
    $user = User::factory()->create();
    $user->assignRole(['user', 'admin', 'super-admin']);

    $this->permissionService->removeRoleFromUser($user, 'user');
    $this->assertFalse($user->hasRole('user'));
    $this->assertTrue($user->hasRole('admin'));
    $this->assertTrue($user->hasRole('super-admin'));

    $this->permissionService->removeRoleFromUser($user, 'admin');
    $this->permissionService->removeRoleFromUser($user, 'super-admin');
    $this->assertFalse($user->hasRole('admin'));
    $this->assertFalse($user->hasRole('super-admin'));
  }

  public function test_sync_roles()
  {
    $user = User::factory()->create();
    $user->assignRole(['user', 'admin']);

    $this->permissionService->syncRoles($user, ['super-admin']);

    $this->assertFalse($user->hasRole('user'));
    $this->assertFalse($user->hasRole('admin'));
    $this->assertTrue($user->hasRole('super-admin'));
  }

  public function test_sync_permissions()
  {
    $role = Role::create(['name' => 'manager', 'guard_name' => 'web']);
    $role->givePermissionTo('view users');

    $this->permissionService->syncPermissions($role, [
      'delete users',
      'view content',
    ]);

    $permissions = $role->permissions->pluck('name');
    $this->assertCount(2, $permissions);
    $this->assertTrue($permissions->contains('delete users'));
    $this->assertTrue($permissions->contains('view content'));
    $this->assertFalse($permissions->contains('view users'));
  }

  public function test_has_permission()
  {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->assertTrue(
      $this->permissionService->hasPermission($user, 'view users')
    );
    $this->assertTrue(
      $this->permissionService->hasPermission($user, 'create reports')
    );
    $this->assertFalse(
      $this->permissionService->hasPermission($user, 'create roles')
    );
  }

  public function test_has_role()
  {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->assertTrue($this->permissionService->hasRole($user, 'admin'));
    $this->assertFalse($this->permissionService->hasRole($user, 'user'));
  }

  public function test_create_role()
  {
    $role = $this->permissionService->createRole('manager');
    $this->assertDatabaseHas('roles', ['name' => 'manager']);

    $roleWithPermissions = $this->permissionService->createRole('supervisor', [
      'view users',
      'view content',
    ]);
    $this->assertDatabaseHas('roles', ['name' => 'supervisor']);
    $this->assertTrue($roleWithPermissions->hasPermissionTo('view users'));
    $this->assertTrue($roleWithPermissions->hasPermissionTo('view content'));
  }

  public function test_create_permission()
  {
    $permission = $this->permissionService->createPermission(
      'access dashboard'
    );
    $this->assertDatabaseHas('permissions', ['name' => 'access dashboard']);
  }
}
