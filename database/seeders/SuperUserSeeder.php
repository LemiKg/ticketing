<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create the super-admin role if it doesn't exist
    $superAdminRole = Role::firstOrCreate([
      'name' => 'super-admin',
      'guard_name' => 'web',
    ]);

    // Create common permissions
    $permissions = [
      // User Management
      'view users',
      'create users',
      'edit users',
      'delete users',

      // Role & Permission Management
      'view roles',
      'create roles',
      'edit roles',
      'delete roles',
      'assign roles',

      // System Management
      'access admin area',
      'manage settings',
      'view logs',
      'manage system',
    ];

    // Create all permissions
    foreach ($permissions as $permission) {
      Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    // Create superuser
    $superuser = User::firstOrCreate(
      ['email' => 'admin@example.com'],
      [
        'name' => 'Super Admin',
        'password' => Hash::make('password123'), // Change this to a secure password in production
        'email_verified_at' => now(),
      ]
    );

    // Assign the super-admin role to the superuser
    $superuser->assignRole($superAdminRole);

    $this->command->info(
      'Superuser created successfully with super-admin role.'
    );
  }
}
