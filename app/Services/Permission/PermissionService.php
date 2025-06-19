<?php

namespace App\Services\Permission;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Services\Permission\Interfaces\PermissionServiceInterface;

class PermissionService implements PermissionServiceInterface
{
    /**
     * Get all available roles
     *
     * @return EloquentCollection<int, Role>
     */
    public function getAllRoles(): EloquentCollection
    {
        return Role::all();
    }

    /**
     * Get all available permissions
     *
     * @return EloquentCollection<int, Permission>
     */
    public function getAllPermissions(): EloquentCollection
    {
        return Permission::all();
    }

    /**
     * Get permissions by role
     *
     * @param string $roleName
     * @return Collection
     */
    public function getPermissionsByRole(string $roleName): Collection
    {
        return Role::findByName($roleName)->permissions->pluck('name');
    }

    /**
     * Assign role to a user
     *
     * @param User $user
     * @param string|string[] $roles
     * @return void
     */
    public function assignRoleToUser(User $user, $roles): void
    {
        $user->assignRole($roles);
    }

    /**
     * Remove role from a user
     *
     * @param User $user
     * @param string|string[] $roles
     * @return void
     */
    public function removeRoleFromUser(User $user, $roles): void
    {
        $user->removeRole($roles);
    }

    /**
     * Sync roles for a user
     *
     * @param User $user
     * @param array<string> $roles
     * @return void
     */
    public function syncRoles(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }

    /**
     * Sync permissions for a role
     *
     * @param Role $role
     * @param array<string> $permissions
     * @return void
     */
    public function syncPermissions(Role $role, array $permissions): void
    {
        $role->syncPermissions($permissions);
    }

    /**
     * Check if user has permission
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    public function hasPermission(User $user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }

    /**
     * Check if user has role
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * Create a new role
     *
     * @param string $name
     * @param array<string> $permissions
     * @return Role
     */
    public function createRole(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name, 'guard_name' => 'web']);

        if (!empty($permissions)) {
            $role->givePermissionTo($permissions);
        }

        return $role;
    }

    /**
     * Create a new permission
     *
     * @param string $name
     * @return Permission
     */
    public function createPermission(string $name): Permission
    {
        return Permission::create(['name' => $name, 'guard_name' => 'web']);
    }
}
