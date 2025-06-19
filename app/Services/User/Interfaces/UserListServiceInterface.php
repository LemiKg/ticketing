<?php

namespace App\Services\User\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserListServiceInterface
{
  /**
   * Get paginated users with sorting and filtering
   *
   * @param array $params
   * @return LengthAwarePaginator
   */
  public function getPaginatedUsers(array $params): LengthAwarePaginator;

  /**
   * Get all users
   *
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getAllUsers();

  /**
   * Get a user by ID
   *
   * @param int $id
   * @return \App\Models\User|null
   */
  public function getUserById(int $id); /**
   * Get total user count
   *
   * @return int
   */
  public function count(): int;
}
