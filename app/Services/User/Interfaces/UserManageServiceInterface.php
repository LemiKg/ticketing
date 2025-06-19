<?php

namespace App\Services\User\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface UserManageServiceInterface
{
  /**
   * Create a new user
   *
   * @param array $data
   * @return User
   */
  public function createUser(array $data): User;

  /**
   * Update an existing user
   *
   * @param User $user
   * @param array $data
   * @return bool
   */
  public function updateUser(User $user, array $data): bool;

  /**
   * Delete a user
   *
   * @param User $user
   * @return bool
   */
  public function deleteUser(User $user): bool;

  /**
   * Validate user creation request
   *
   * @param Request $request
   * @return array
   */
  public function validateCreateRequest(Request $request): array;

  /**
   * Validate user update request
   *
   * @param Request $request
   * @return array
   */
  public function validateUpdateRequest(Request $request): array;
}
