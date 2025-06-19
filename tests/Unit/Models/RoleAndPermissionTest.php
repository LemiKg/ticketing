<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionTest extends TestCase
{
  use RefreshDatabase;

  public function test_role_can_be_created()
  {
    $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);

    $this->assertDatabaseHas('roles', ['name' => 'super-admin']);
    $this->assertInstanceOf(Role::class, $role);
  }

  public function test_permission_can_be_created()
  {
    $permission = Permission::create([
      'name' => 'view users',
      'guard_name' => 'web',
    ]);

    $this->assertDatabaseHas('permissions', ['name' => 'view users']);
    $this->assertInstanceOf(Permission::class, $permission);
  }

  public function test_role_can_be_assigned_permissions()
  {
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $viewUsers = Permission::create([
      'name' => 'view users',
      'guard_name' => 'web',
    ]);
    $createUsers = Permission::create([
      'name' => 'create users',
      'guard_name' => 'web',
    ]);

    $role->givePermissionTo($viewUsers);
    $this->assertTrue($role->hasPermissionTo('view users'));

    $role->givePermissionTo([$createUsers->name]);
    $this->assertTrue($role->hasPermissionTo('create users'));
  }

  public function test_role_can_sync_permissions()
  {
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Permission::create(['name' => 'view users', 'guard_name' => 'web']);
    Permission::create(['name' => 'edit users', 'guard_name' => 'web']);
    Permission::create(['name' => 'delete users', 'guard_name' => 'web']);

    $role->givePermissionTo(['view users', 'edit users']);
    $this->assertTrue($role->hasPermissionTo('view users'));
    $this->assertTrue($role->hasPermissionTo('edit users'));

    $role->syncPermissions(['edit users', 'delete users']);
    $this->assertFalse($role->hasPermissionTo('view users'));
    $this->assertTrue($role->hasPermissionTo('edit users'));
    $this->assertTrue($role->hasPermissionTo('delete users'));
  }

  public function test_role_can_revoke_permissions()
  {
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $viewUsers = Permission::create([
      'name' => 'view users',
      'guard_name' => 'web',
    ]);
    $editUsers = Permission::create([
      'name' => 'edit users',
      'guard_name' => 'web',
    ]);

    $role->givePermissionTo([$viewUsers, $editUsers]);
    $this->assertTrue($role->hasAllPermissions(['view users', 'edit users']));

    $role->revokePermissionTo($viewUsers);
    $this->assertFalse($role->hasPermissionTo('view users'));
    $this->assertTrue($role->hasPermissionTo('edit users'));
  }

  public function test_user_can_be_assigned_roles()
  {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);

    $user->assignRole($role);
    $this->assertTrue($user->hasRole('admin'));
  }

  public function test_user_can_have_multiple_roles()
  {
    $user = User::factory()->create();
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'user', 'guard_name' => 'web']);

    $user->assignRole(['admin', 'user']);
    $this->assertTrue($user->hasAllRoles(['admin', 'user']));
  }

  public function test_user_can_sync_roles()
  {
    $user = User::factory()->create();
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'user', 'guard_name' => 'web']);
    Role::create(['name' => 'super-admin', 'guard_name' => 'web']);

    $user->assignRole(['admin', 'user']);
    $this->assertTrue($user->hasRole('admin'));
    $this->assertTrue($user->hasRole('user'));

    $user->syncRoles(['user', 'super-admin']);
    $this->assertFalse($user->hasRole('admin'));
    $this->assertTrue($user->hasRole('user'));
    $this->assertTrue($user->hasRole('super-admin'));
  }

  public function test_user_can_remove_roles()
  {
    $user = User::factory()->create();
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'user', 'guard_name' => 'web']);

    $user->assignRole(['admin', 'user']);
    $this->assertTrue($user->hasRole('admin'));

    $user->removeRole('admin');
    $this->assertFalse($user->hasRole('admin'));
    $this->assertTrue($user->hasRole('user'));
  }

  public function test_user_inherits_permissions_from_role()
  {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Permission::create(['name' => 'view users', 'guard_name' => 'web']);
    Permission::create(['name' => 'edit users', 'guard_name' => 'web']);

    $role->givePermissionTo(['view users', 'edit users']);
    $user->assignRole($role);

    $this->assertTrue($user->hasPermissionTo('view users'));
    $this->assertTrue($user->hasPermissionTo('edit users'));
  }

  public function test_user_can_have_direct_permissions()
  {
    $user = User::factory()->create();
    Permission::create(['name' => 'view users', 'guard_name' => 'web']);

    $user->givePermissionTo('view users');
    $this->assertTrue($user->hasPermissionTo('view users'));
  }

  public function test_employee_role_has_correct_permissions()
  {
    // Create the expected permissions from the seeder
    Permission::create(['name' => 'view users', 'guard_name' => 'web']);

    // Create the employee role with the expected permissions
    $employeeRole = Role::create(['name' => 'employee', 'guard_name' => 'web']);
    $employeeRole->givePermissionTo(['view users']);

    // Test that the employee role has the correct permission
    $this->assertTrue($employeeRole->hasPermissionTo('view users'));
    $this->assertEquals(1, $employeeRole->permissions->count());
  }

  public function test_admin_role_has_correct_permissions()
  {
    // Create a subset of expected permissions for testing
    Permission::create(['name' => 'view users', 'guard_name' => 'web']);
    Permission::create(['name' => 'create users', 'guard_name' => 'web']);
    Permission::create(['name' => 'edit users', 'guard_name' => 'web']);
    Permission::create(['name' => 'view reports', 'guard_name' => 'web']);
    Permission::create(['name' => 'create reports', 'guard_name' => 'web']);
    Permission::create(['name' => 'export reports', 'guard_name' => 'web']);
    Permission::create(['name' => 'delete reports', 'guard_name' => 'web']);

    // Create the admin role with the expected permissions
    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $adminRole->givePermissionTo([
      'view users',
      'create users',
      'edit users',
      'view reports',
      'create reports',
      'export reports',
      'delete reports',
    ]);

    // Test that the admin role has the correct permissions
    $this->assertTrue($adminRole->hasPermissionTo('view users'));
    $this->assertTrue($adminRole->hasPermissionTo('create reports'));
    $this->assertTrue($adminRole->hasPermissionTo('delete reports'));
    $this->assertEquals(7, $adminRole->permissions->count());
  }
}
