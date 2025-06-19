<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\Permission\Interfaces\PermissionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
  public function __construct(
    private PermissionServiceInterface $permissionService
  ) {}

  /**
   * Display a listing of roles.
   */
  public function index(Request $request): Response
  {
    $query = Role::with(['permissions'])->where('name', '!=', 'super-admin'); // Hide super-admin from management

    // Search functionality
    if ($request->has('search') && $request->search) {
      $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Sorting
    $sortField = $request->get('sortField', 'name');
    $sortOrder = $request->get('sortOrder', 'asc');
    $query->orderBy($sortField, $sortOrder);

    // Pagination
    $roles = $query->paginate(
      $request->get('rows', 15),
      ['*'],
      'page',
      $request->get('page', 1)
    );

    // Get all permissions for role creation/editing
    $permissions = Permission::orderBy('name')
      ->get()
      ->groupBy(function ($permission) {
        $parts = explode(' ', $permission->name);
        return count($parts) > 1 ? $parts[1] : 'general';
      });

    return Inertia::render('dashboard/Roles/Index', [
      'roles' => $roles,
      'permissions' => $permissions,
      'filters' => [
        'search' => $request->search,
        'sortField' => $sortField,
        'sortOrder' => $sortOrder,
      ],
    ]);
  }

  /**
   * Show the form for creating a new role.
   */
  public function create(): Response
  {
    $permissions = Permission::orderBy('name')
      ->get()
      ->groupBy(function ($permission) {
        $parts = explode(' ', $permission->name);
        return count($parts) > 1 ? $parts[1] : 'general';
      });

    return Inertia::render('dashboard/Roles/CreateOrEdit', [
      'permissions' => $permissions,
      'formMethod' => 'post',
      'formActionUrl' => route('roles.store'),
    ]);
  }

  /**
   * Store a newly created role in storage.
   */
  public function store(StoreRoleRequest $request): RedirectResponse
  {
    $validated = $request->validated();

    try {
      $role = $this->permissionService->createRole(
        $validated['name'],
        $validated['permissions'] ?? []
      );

      return redirect()
        ->route('roles.index')
        ->with('success', "Role '{$role->name}' created successfully.");
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Failed to create role: ' . $e->getMessage());
    }
  }

  /**
   * Display the specified role.
   */
  public function show(Role $role): Response
  {
    // Prevent viewing super-admin role
    if ($role->name === 'super-admin') {
      abort(404);
    }

    $role->load(['permissions', 'users']);

    return Inertia::render('dashboard/Roles/Show', [
      'role' => $role,
    ]);
  }

  /**
   * Show the form for editing the specified role.
   */
  public function edit(Role $role): Response
  {
    // Prevent editing super-admin role
    if ($role->name === 'super-admin') {
      abort(403, 'Cannot edit super-admin role');
    }

    $role->load('permissions');

    $permissions = Permission::orderBy('name')
      ->get()
      ->groupBy(function ($permission) {
        $parts = explode(' ', $permission->name);
        return count($parts) > 1 ? $parts[1] : 'general';
      });

    return Inertia::render('dashboard/Roles/CreateOrEdit', [
      'role' => $role,
      'permissions' => $permissions,
      'formMethod' => 'put',
      'formActionUrl' => route('roles.update', $role),
    ]);
  }

  /**
   * Update the specified role in storage.
   */
  public function update(
    UpdateRoleRequest $request,
    Role $role
  ): RedirectResponse {
    // Prevent updating super-admin role
    if ($role->name === 'super-admin') {
      abort(403, 'Cannot modify super-admin role');
    }

    $validated = $request->validated();

    try {
      // Update role name if provided
      if (isset($validated['name']) && $validated['name'] !== $role->name) {
        $role->update(['name' => $validated['name']]);
      }

      // Sync permissions
      if (isset($validated['permissions'])) {
        $this->permissionService->syncPermissions(
          $role,
          $validated['permissions']
        );
      }

      return redirect()
        ->route('roles.index')
        ->with('success', "Role '{$role->name}' updated successfully.");
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Failed to update role: ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified role from storage.
   */
  public function destroy(Role $role): RedirectResponse
  {
    // Prevent deleting super-admin role
    if ($role->name === 'super-admin') {
      abort(403, 'Cannot delete super-admin role');
    }

    // Prevent deleting roles that have users assigned
    if ($role->users()->count() > 0) {
      return redirect()
        ->back()
        ->with(
          'error',
          "Cannot delete role '{$role->name}' because it has users assigned to it."
        );
    } // Prevent deleting system roles
    $systemRoles = ['timehive_admin', 'timehive_sales', 'admin'];
    if (in_array($role->name, $systemRoles)) {
      return redirect()
        ->back()
        ->with('error', "Cannot delete system role '{$role->name}'.");
    }

    try {
      $roleName = $role->name;
      $role->delete();

      return redirect()
        ->route('roles.index')
        ->with('success', "Role '{$roleName}' deleted successfully.");
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->with('error', 'Failed to delete role: ' . $e->getMessage());
    }
  }

  /**
   * Get roles count for dashboard widgets
   */
  public function count(): \Illuminate\Http\JsonResponse
  {
    $count = Role::where('name', '!=', 'super-admin')->count();

    return response()->json(['count' => $count]);
  }

  /**
   * Sync permissions for a role
   */
  public function syncPermissions(
    Request $request,
    Role $role
  ): RedirectResponse {
    // Prevent modifying super-admin role
    if ($role->name === 'super-admin') {
      abort(403, 'Cannot modify super-admin role');
    }

    $request->validate([
      'permissions' => 'array',
      'permissions.*' => 'string|exists:permissions,name',
    ]);

    try {
      $this->permissionService->syncPermissions(
        $role,
        $request->permissions ?? []
      );

      return redirect()
        ->back()
        ->with('success', "Permissions updated for role '{$role->name}'.");
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->with('error', 'Failed to update permissions: ' . $e->getMessage());
    }
  }
}
