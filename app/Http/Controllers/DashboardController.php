<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controller handling dashboard functionality
 */
class DashboardController extends Controller
{
  /**
   * Display the main dashboard
   *
   * @return \Inertia\Response
   */
  public function index()
  {
    return Inertia::render('dashboard/Dashboard', [
      'user' => auth()->user(),
    ]);
  }
}
