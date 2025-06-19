<?php

namespace App\Services\Permission\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface PermissionServiceInterface
{
    /**
     * Get all available roles
     *
     * @return EloquentCollection<int, Role>
     */
    public function getAllRoles(): EloquentCollection;

    /**
     * Get all available permissions
     *
     * @return EloquentCollection<int, Permission>
     */
    public function getAllPermissions(): EloquentCollection;

    /**
     * Get permissions by role
     *
     * @param string $roleName
     * @return Collection
     */
    public function getPermissionsByRole(string $roleName): Collection;

    /**
     * Assign role to a user
     *
     * @param User $user
     * @param string|string[] $roles
     * @return void
     */
    public function assignRoleToUser(User $user, $roles): void;

    /**
     * Remove role from a user
     *
     * @param User $user
     * @param string|string[] $roles
     * @return void
     */
    public function removeRoleFromUser(User $user, $roles): void;

    /**
     * Sync roles for a user
     *
     * @param User $user
     * @param array<string> $roles
     * @return void
     */
    public function syncRoles(User $user, array $roles): void;

    /**
     * Sync permissions for a role
     *
     * @param Role $role
     * @param array<string> $permissions
     * @return void
     */
    public function syncPermissions(Role $role, array $permissions): void;

    /**
     * Check if user has permission
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    public function hasPermission(User $user, string $permission): bool;

    /**
     * Check if user has role
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user, string $role): bool;

    /**
     * Create a new role
     *
     * @param string $name
     * @param array<string> $permissions
     * @return Role
     */
    public function createRole(string $name, array $permissions = []): Role;

    /**
     * Create a new permission
     *
     * @param string $name
     * @return Permission
     */
    public function createPermission(string $name): Permission;
}
