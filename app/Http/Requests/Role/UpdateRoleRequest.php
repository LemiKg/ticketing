<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class UpdateRoleRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->user()->hasAnyRole(['super-admin', 'timehive_admin']);
  }

  /**
   * Get the validation rules that apply to the request.
   */
  public function rules(): array
  {
    $role = $this->route('role');

    return [
      'name' => [
        'sometimes',
        'required',
        'string',
        'max:255',
        Rule::unique('roles', 'name')->ignore($role->id),
        'not_in:super-admin', // Prevent renaming to super-admin
        'regex:/^[a-zA-Z0-9_-]+$/', // Only allow alphanumeric, underscore, and hyphen
      ],
      'permissions' => 'array',
      'permissions.*' => ['string', Rule::exists('permissions', 'name')],
    ];
  }

  /**
   * Get custom error messages for validation rules.
   */
  public function messages(): array
  {
    return [
      'name.required' => 'Role name is required.',
      'name.unique' => 'A role with this name already exists.',
      'name.not_in' => 'Cannot rename role to super-admin.',
      'name.regex' =>
        'Role name can only contain letters, numbers, underscores, and hyphens.',
      'permissions.*.exists' =>
        'One or more selected permissions do not exist.',
    ];
  }

  /**
   * Get custom attribute names for validation errors.
   */
  public function attributes(): array
  {
    return [
      'name' => 'role name',
      'permissions' => 'permissions',
    ];
  }

  /**
   * Prepare the data for validation.
   */
  protected function prepareForValidation(): void
  {
    // Prevent modification of system role names
    $role = $this->route('role');
    $systemRoles = ['timehive_admin', 'timehive_sales', 'admin'];

    if ($role && in_array($role->name, $systemRoles)) {
      $this->merge(['name' => $role->name]); // Keep original name
    }
  }
}
