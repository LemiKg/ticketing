# Laravel Vue Boilerplate

A modern, full-stack web application boilerplate built with Laravel 11, Vue 3, TypeScript, and modern tooling. This template provides a solid foundation for building web applications with authentication, user management, role-based permissions, and a clean service-oriented architecture.

## 🚀 Features

### Backend (Laravel 11)
- **Service Layer Architecture** - Clean, maintainable code following SOLID principles
- **Authentication** - Laravel Breeze with session-based auth
- **Role-Based Permissions** - Spatie Laravel Permission integration
- **User Management** - Complete CRUD with role assignment
- **Contact Form** - Built-in contact form with mail functionality
- **Docker Ready** - docker-compose configuration included

### Frontend (Vue 3 + TypeScript)
- **Vue 3 Composition API** - Modern reactive framework
- **TypeScript** - Type safety and better developer experience
- **Inertia.js** - SPA experience without API complexity
- **PrimeVue** - Professional UI component library
- **Tailwind CSS** - Utility-first styling framework
- **Vue I18n** - Internationalization support
- **Vite** - Fast development and optimized builds

### Development Experience
- **Hot Module Replacement** - Instant feedback during development
- **ESLint + Prettier** - Code formatting and linting
- **Composables** - Reusable functionality (CRUD, DataTable, etc.)
- **TypeScript Interfaces** - Type-safe data structures
- **Clean Architecture** - Separation of concerns and maintainability

## 📦 What's Included

### Pages & Components
- Landing page with features showcase
- Complete authentication flow (login, register, password reset)
- User dashboard with admin panels
- User management (CRUD operations)
- Role management with permission assignment
- Profile management
- Contact form
- Responsive design for all screen sizes

### Backend Services
- User management services (list, manage)
- Permission service for role-based access control
- Service provider configuration for dependency injection
- Clean controller structure with service injection

### Frontend Utilities
- **useCrud** - Generic CRUD operations composable
- **useDataTable** - Sortable, filterable data tables
- **useFlashMessages** - Toast notifications and alerts
- Reusable form components and validation

## 🛠 Installation

### Prerequisites
- PHP 8.2+
- Node.js 18+
- Composer
- SQLite (default) or your preferred database

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd laravel-vue-boilerplate
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start development servers**
   ```bash
   # Terminal 1 - Laravel backend
   php artisan serve
   
   # Terminal 2 - Frontend assets
   npm run dev
   ```

7. **Access the application**
   - Frontend: http://localhost:8000
   - Login with: admin@example.com / password

## 🐳 Docker Development

For a containerized development environment:

```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## 📚 Usage

### Adding New Features

1. **Create Models & Migrations**
   ```bash
   php artisan make:model YourModel -m
   ```

2. **Create Service Layer**
   ```bash
   mkdir app/Services/YourFeature
   # Create service interfaces and implementations
   ```

3. **Register Services**
   ```php
   // In AppServiceProvider.php
   $this->app->bind(YourServiceInterface::class, YourService::class);
   ```

4. **Create Controllers**
   ```bash
   php artisan make:controller YourController
   ```

5. **Add Frontend Pages**
   ```bash
   # Create Vue components in resources/js/Pages/
   ```

### Customization

- **Styling**: Modify `tailwind.config.js` and add custom CSS
- **Components**: Create reusable components in `resources/js/Components/`
- **Translations**: Add language files in `resources/js/lang/`
- **Permissions**: Extend the permission system in the seeder
- **Services**: Follow the existing service pattern for business logic

## 🔧 Available Commands

### Development
```bash
npm run dev          # Start development server with HMR
npm run build        # Build for production
npm run preview      # Preview production build
php artisan serve    # Start Laravel development server
```

### Database
```bash
php artisan migrate        # Run migrations
php artisan db:seed        # Seed database
php artisan migrate:fresh  # Fresh migration with seeding
```

### Code Quality
```bash
npm run lint        # Run ESLint
npm run format      # Format code with Prettier
composer pint       # Format PHP code (if installed)
```

## 🏗 Architecture

### Service Layer Pattern
```
app/
├── Services/
│   ├── User/
│   │   ├── UserListService.php
│   │   ├── UserManageService.php
│   │   └── Interfaces/
│   └── Permission/
│       ├── PermissionService.php
│       └── Interfaces/
```

### Frontend Structure
```
resources/js/
├── Components/       # Reusable components
├── Composables/      # Vue composables
├── Layouts/          # Page layouts
├── Pages/           # Page components
├── types/           # TypeScript interfaces
└── utils/           # Utility functions
```

## 🔐 Default Users

After seeding, you can login with:
- **Admin**: admin@example.com / password
- **User**: user@example.com / password

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

Feel free to submit issues and enhancement requests!

## 📖 Documentation

For detailed architecture documentation, see [docs/PROJECT_ARCHITECTURE.md](docs/PROJECT_ARCHITECTURE.md).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
