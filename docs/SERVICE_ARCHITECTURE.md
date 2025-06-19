# TimeHive Service Architecture Documentation

This document describes the service-oriented architecture implemented in the TimeHive application following SOLID principles. Use this as a reference when implementing new features or services.

## Table of Contents

1. [Overview](#1-overview)
2. [SOLID Principles](#2-solid-principles)
3. [Directory Structure](#3-directory-structure)
4. [Implementation Pattern](#4-implementation-pattern)
5. [Example: Dashboard Implementation](#5-example-dashboard-implementation)
6. [Creating New Services](#6-creating-new-services)
7. [Testing Services](#7-testing-services)

## 1. Overview

TimeHive's architecture follows a service-oriented approach where:

- **Controllers** are responsible for handling HTTP requests and delegating business logic to services
- **Services** encapsulate business logic and are accessed through interfaces
- **Interfaces** define contracts that service implementations must fulfill
- **Models** represent database entities and handle data access

This approach promotes separation of concerns, testability, and maintainability.

## 2. SOLID Principles

Our architecture adheres to SOLID principles:

- **Single Responsibility Principle (SRP)**: Each class has one responsibility
- **Open/Closed Principle (OCP)**: Classes are open for extension but closed for modification
- **Liskov Substitution Principle (LSP)**: Implementations are substitutable for their interfaces
- **Interface Segregation Principle (ISP)**: Interfaces are specific to client needs
- **Dependency Inversion Principle (DIP)**: High-level modules depend on abstractions

## 3. Directory Structure

Services are organized in the following structure:

```
app/
├── Http/
│   └── Controllers/
│       └── FeatureController.php
│
├── Services/
│   └── FeatureName/
│       ├── ConcreteService1.php
│       ├── ConcreteService2.php
│       └── Interfaces/
│           ├── Service1Interface.php
│           └── Service2Interface.php
│
└── Providers/
    └── AppServiceProvider.php
```

## 4. Implementation Pattern

### 4.1. Define Interfaces

Create interfaces that declare the methods your service will implement:

```php
<?php

namespace App\Services\FeatureName\Interfaces;

interface ExampleServiceInterface
{
  /**
   * Method description
   *
   * @param type $paramName
   * @return returnType
   */
  public function methodName($paramName): returnType;
}
```

### 4.2. Implement Services

Create concrete implementations of your interfaces:

```php
<?php

namespace App\Services\FeatureName;

use App\Services\FeatureName\Interfaces\ExampleServiceInterface;

class ExampleService implements ExampleServiceInterface
{
  /**
   * Method description
   *
   * @param type $paramName
   * @return returnType
   */
  public function methodName($paramName): returnType
  {
    // Implementation
    return $result;
  }
}
```

### 4.3. Register Services

Register service bindings in AppServiceProvider:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FeatureName\Interfaces\ExampleServiceInterface;
use App\Services\FeatureName\ExampleService;

class AppServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    $this->app->bind(ExampleServiceInterface::class, ExampleService::class);
  }
}
```

### 4.4. Use Services in Controllers

Inject and use services in controllers:

```php
<?php

namespace App\Http\Controllers;

use App\Services\FeatureName\Interfaces\ExampleServiceInterface;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
  protected $exampleService;

  public function __construct(ExampleServiceInterface $exampleService)
  {
    $this->exampleService = $exampleService;
  }

  public function index(Request $request)
  {
    $result = $this->exampleService->methodName($request->input('param'));

    return view('feature.index', ['result' => $result]);
  }
}
```

## 5. Example: Dashboard Implementation

The Dashboard feature demonstrates this architecture:

### 5.1. Interfaces

```php
// AttendanceStatisticsServiceInterface.php
interface AttendanceStatisticsServiceInterface
{
  public function getWorkerAttendanceStatistics(): array;
}

// RecentAttendanceServiceInterface.php
interface RecentAttendanceServiceInterface
{
  public function getRecentAttendances(int $limit = 5): Collection;
}

// UserStatisticsServiceInterface.php
interface UserStatisticsServiceInterface
{
  public function getTotalUsers(): int;
  public function getTotalWorkers(): int;
}

// DeviceStatisticsServiceInterface.php
interface DeviceStatisticsServiceInterface
{
  public function getTotalDevices(): int;
}
```

### 5.2. Implementations

```php
// AttendanceStatisticsService.php
class AttendanceStatisticsService implements
  AttendanceStatisticsServiceInterface
{
  public function getWorkerAttendanceStatistics(): array
  {
    // Implementation...
    return [
      'presentWorkers' => $presentWorkers,
      'absentWorkers' => $absentWorkers,
      'totalWorkers' => $totalWorkers,
      'checkedOutWorkers' => $checkedOutWorkers,
      'stillWorkingWorkers' => $stillWorkingWorkers,
    ];
  }
}

// Other service implementations follow the same pattern
```

### 5.3. Controller Usage

```php
// DashboardController.php
class DashboardController extends Controller
{
  protected $attendanceStatisticsService;
  protected $recentAttendanceService;
  protected $userStatisticsService;
  protected $deviceStatisticsService;

  public function __construct(
    AttendanceStatisticsServiceInterface $attendanceStatisticsService,
    RecentAttendanceServiceInterface $recentAttendanceService,
    UserStatisticsServiceInterface $userStatisticsService,
    DeviceStatisticsServiceInterface $deviceStatisticsService
  ) {
    $this->attendanceStatisticsService = $attendanceStatisticsService;
    $this->recentAttendanceService = $recentAttendanceService;
    $this->userStatisticsService = $userStatisticsService;
    $this->deviceStatisticsService = $deviceStatisticsService;
  }

  public function index(Request $request)
  {
    $recentAttendances = $this->recentAttendanceService->getRecentAttendances(
      5
    );
    $attendanceStats = $this->attendanceStatisticsService->getWorkerAttendanceStatistics();

    // Controller combines data from multiple services
    return Inertia::render('dashboard/Dashboard', [
      'recentAttendances' => $recentAttendances,
      'userCount' => $this->userStatisticsService->getTotalUsers(),
      // ... other data
    ]);
  }
}
```

## 6. Creating New Services

Follow these steps to create a new service:

1. Identify the feature and its required services
2. Create interfaces in `app/Services/FeatureName/Interfaces/`
3. Implement services in `app/Services/FeatureName/`
4. Register bindings in AppServiceProvider
5. Inject and use services in controllers

## 7. Testing Services

The service architecture makes testing easier:

```php
class AttendanceStatisticsServiceTest extends TestCase
{
  protected $service;

  public function setUp(): void
  {
    parent::setUp();
    $this->service = new AttendanceStatisticsService();
  }

  public function testGetWorkerAttendanceStatistics()
  {
    // Create test data

    $stats = $this->service->getWorkerAttendanceStatistics();

    $this->assertIsArray($stats);
    $this->assertArrayHasKey('presentWorkers', $stats);
    // ... other assertions
  }
}
```

For controller tests, you can mock the service interfaces:

```php
class DashboardControllerTest extends TestCase
{
  public function testIndex()
  {
    // Mock services
    $attendanceService = Mockery::mock(
      AttendanceStatisticsServiceInterface::class
    );
    $attendanceService
      ->shouldReceive('getWorkerAttendanceStatistics')
      ->once()
      ->andReturn([
        'presentWorkers' => 5,
        'absentWorkers' => 3,
        'totalWorkers' => 8,
        'checkedOutWorkers' => 2,
        'stillWorkingWorkers' => 3,
      ]);

    // Bind mock
    $this->app->instance(
      AttendanceStatisticsServiceInterface::class,
      $attendanceService
    );

    // Make request and assert response
    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
  }
}
```

---

This architecture promotes clean code, maintainability, and testability throughout the TimeHive application. When implementing new features, follow these patterns to ensure consistency and quality.
