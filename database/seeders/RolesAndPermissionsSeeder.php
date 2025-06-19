<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Reset cached roles and permissions
    app()[
      \Spatie\Permission\PermissionRegistrar::class
    ]->forgetCachedPermissions();

    // Create basic roles
    $roles = [
      'user' => 'Regular user with limited access',
      'manager' => 'Manager with department-level access',
      'admin' => 'Administrator with system-wide access',
    ];

    foreach ($roles as $role => $description) {
      Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }

    // Create permissions by category
    $permissionsByCategory = [
      'User Management' => [
        'view users',
        'create users',
        'edit users',
        'delete users',
      ],
      'Content Management' => [
        'view content',
        'create content',
        'edit content',
        'delete content',
        'publish content',
      ],
      'System' => ['access admin area', 'manage settings', 'view logs'],
    ];

    // Create all permissions
    foreach ($permissionsByCategory as $category => $permissions) {
      foreach ($permissions as $permission) {
        Permission::firstOrCreate([
          'name' => $permission,
          'guard_name' => 'web',
        ]);
      }
    }

    // Assign permissions to roles
    $role = Role::findByName('admin', 'web');
    $role->givePermissionTo(Permission::all());

    $role = Role::findByName('manager', 'web');
    $role->givePermissionTo([
      'view users',
      'view content',
      'create content',
      'edit content',
      'publish content',
      'access admin area',
    ]);

    $role = Role::findByName('user', 'web');
    $role->givePermissionTo(['view content']);

    $this->command->info('Roles and permissions created successfully.');
  }
}
