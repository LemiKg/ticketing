<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\User\Interfaces\UserManageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserManageService implements UserManageServiceInterface
{
  /**
   * Create a new user
   *
   * @param array $data
   * @return User
   */
  public function createUser(array $data): User
  {
    // Hash password if it exists in the data
    if (isset($data['password'])) {
      $data['password'] = Hash::make($data['password']);
    }

    // Create the user
    return User::create($data);
  }

  /**
   * Update an existing user
   *
   * @param User $user
   * @param array $data
   * @return bool
   */
  public function updateUser(User $user, array $data): bool
  {
    // Hash password if it's being updated
    if (isset($data['password']) && $data['password']) {
      $data['password'] = Hash::make($data['password']);
    } else {
      // Remove password from data if empty to avoid overwriting
      unset($data['password']);
    }

    return $user->update($data);
  }

  /**
   * Delete a user
   *
   * @param User $user
   * @return bool
   */
  public function deleteUser(User $user): bool
  {
    return $user->delete();
  }

  /**
   * Validate user creation request
   *
   * @param Request $request
   * @return array
   */ public function validateCreateRequest(Request $request): array
  {
    return $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => [
        'required',
        'string',
        'lowercase',
        'email',
        'max:255',
        'unique:users',
      ],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      // Removed is_worker field
    ]);
  }

  /**
   * Validate user update request
   *
   * @param Request $request
   * @return array
   */ public function validateUpdateRequest(Request $request): array
  {
    $userId = $request->route('user')->id ?? null;

    return $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => [
        'required',
        'string',
        'lowercase',
        'email',
        'max:255',
        Rule::unique('users')->ignore($userId),
      ],
      'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
    ]);
  }
}
