<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\User\Interfaces\UserListServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserListService implements UserListServiceInterface
{
  /**
   * Get paginated users with sorting and filtering
   *
   * @param array $params
   * @return LengthAwarePaginator
   */ public function getPaginatedUsers(array $params): LengthAwarePaginator
  {
    $sortField = $params['sortField'] ?? 'id';
    $sortOrder = $params['sortOrder'] ?? 'asc';
    $page = $params['page'] ?? 1;
    $rows = $params['rows'] ?? 15;
    $searchName = $params['searchName'] ?? null;
    $searchEmail = $params['searchEmail'] ?? null;

    $query = User::query();

    // Apply filters
    $this->applyNameFilter($query, $searchName);
    $this->applyEmailFilter($query, $searchEmail);

    // Apply sorting
    $this->applySorting($query, $sortField, $sortOrder);

    // Return paginated results
    return $query->paginate($rows, ['*'], 'page', $page);
  }
  /**
   * Get all users
   *
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getAllUsers()
  {
    return User::select('id', 'name', 'email')->orderBy('name')->get();
  }

  /**
   * Get a user by ID
   *
   * @param int $id
   * @return \App\Models\User|null
   */
  public function getUserById(int $id)
  {
    return User::find($id);
  }

  /**
   * Get total user count
   *
   * @return int
   */
  public function count(): int
  {
    return User::count();
  }

  /**
   * Apply name filter to the query
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string|null $searchName
   * @return void
   */
  private function applyNameFilter($query, ?string $searchName): void
  {
    if ($searchName) {
      $query->where('name', 'like', "%{$searchName}%");
    }
  } /**
   * Apply email filter to the query
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string|null $searchEmail
   * @return void
   */
  private function applyEmailFilter($query, ?string $searchEmail): void
  {
    if ($searchEmail) {
      $query->where('email', 'like', "%{$searchEmail}%");
    }
  }

  /**
   * Apply sorting to the query
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $sortField
   * @param string $sortOrder
   * @return void
   */
  private function applySorting(
    $query,
    string $sortField,
    string $sortOrder
  ): void {
    $query->orderBy($sortField, $sortOrder);
  }
}
