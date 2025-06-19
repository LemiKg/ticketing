<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\User\Interfaces\UserListServiceInterface;
use App\Services\User\Interfaces\UserManageServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
  protected $userListService;
  protected $userManageService;

  /**
   * UserController constructor.
   *
   * @param UserListServiceInterface $userListService
   * @param UserManageServiceInterface $userManageService
   */
  public function __construct(
    UserListServiceInterface $userListService,
    UserManageServiceInterface $userManageService
  ) {
    $this->userListService = $userListService;
    $this->userManageService = $userManageService;
  }

  /**
   * Display a listing of the users with filtering and sorting
   *
   * @param Request $request
   * @return \Inertia\Response
   */
  public function index(Request $request)
  {
    $params = [
      'sortField' => $request->input('sortField', 'id'),
      'sortOrder' => $request->input('sortOrder', 'asc'),
      'page' => $request->input('page', 1),
      'rows' => $request->input('rows', 15),
      'searchName' => $request->input('searchName'),
      'searchEmail' => $request->input('searchEmail'),
    ];

    $users = $this->userListService->getPaginatedUsers($params);

    return Inertia::render('dashboard/Users/Index', [
      'users' => $users,
      'sortField' => $params['sortField'],
      'sortOrder' => $params['sortOrder'],
      'searchName' => $params['searchName'],
      'searchEmail' => $params['searchEmail'],
    ]);
  }

  /**
   * Show the form for creating a new user
   *
   * @return \Inertia\Response
   */
  public function create()
  {
    return Inertia::render('dashboard/Users/CreateOrEditUser');
  }

  /**
   * Store a newly created user
   *
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request)
  {
    $validatedData = $this->userManageService->validateCreateRequest($request);

    $this->userManageService->createUser($validatedData);

    return redirect()
      ->route('users.index')
      ->with('success', 'User created successfully.');
  }

  /**
   * Show the user details
   *
   * @param User $user
   * @return \Inertia\Response
   */
  public function show(User $user)
  {
    return Inertia::render('dashboard/Users/ShowUser', [
      'user' => $user,
    ]);
  }

  /**
   * Show the form for editing a user
   *
   * @param User $user
   * @return \Inertia\Response
   */
  public function edit(User $user)
  {
    return Inertia::render('dashboard/Users/CreateOrEditUser', [
      'user' => $user,
    ]);
  }

  /**
   * Update the specified user
   *
   * @param Request $request
   * @param User $user
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request, User $user)
  {
    $validatedData = $this->userManageService->validateUpdateRequest($request);

    $this->userManageService->updateUser($user, $validatedData);

    return redirect()
      ->route('users.index')
      ->with('success', 'User updated successfully.');
  }

  /**
   * Remove the specified user
   *
   * @param User $user
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(User $user)
  {
    $this->userManageService->deleteUser($user);

    return redirect()
      ->route('users.index')
      ->with('success', 'User deleted successfully.');
  }

  /**
   * Get the total user count (for API)
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function count(Request $request)
  {
    return response()->json(['count' => $this->userListService->count()]);
  }

  /**
   * Get all users for dropdown selections
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getUsers()
  {
    $users = User::select('id', 'name')->orderBy('name')->get();
    return response()->json($users);
  }
}
