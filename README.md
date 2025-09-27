# Laravel Docker Base

A professional Laravel application with Domain-Driven Design (DDD) architecture, featuring a complete Docker development environment and modern React frontend.

## Overview

This project provides a production-ready Laravel application with:

- **Domain-Driven Design** architecture for maintainable code
- **Laravel Sanctum** authentication system
- **Interactive API Documentation** with Laravel Scramble
- **Docker** development environment
- **React 18** with TypeScript frontend
- **Hot Module Replacement** for efficient development

## Quick Start

### Prerequisites

- Docker and Docker Compose
- Git
- Make (optional, for convenience commands)

### Installation

1. **Clone the repository**:
   ```bash
   git clone <your-repo-url>
   cd laravel-docker-base
   ```

2. **Run the installation script**:
   ```bash
   chmod +x install.sh
   ./install.sh
   ```

3. **Start the development environment**:
   ```bash
   make start
   ```

4. **Access your application**:
   - Main Application: https://localhost
   - API Documentation: https://localhost/api/docs
   - Database Admin: http://localhost:8080

## Architecture

### Domain-Driven Design Structure

The application follows DDD principles with clear domain separation:

```
src/app/
├── Domains/           # Business domains
│   ├── Auth/         # Authentication domain
│   │   ├── Controllers/
│   │   ├── Services/
│   │   ├── DTOs/
│   │   ├── Events/
│   │   └── Exceptions/
│   └── User/         # User management domain
│       ├── Controllers/
│       ├── Services/
│       ├── Repositories/
│       ├── ValueObjects/
│       ├── Events/
│       └── Listeners/
├── Shared/           # Shared components
│   ├── Http/         # HTTP responses, middleware
│   └── Exceptions/   # Custom exceptions
└── Providers/        # Service providers
```

### Key Components

- **Services**: Business logic and use cases
- **Repositories**: Data access abstraction
- **DTOs**: Data transfer objects with validation
- **Value Objects**: Domain concepts with business rules
- **Events**: Domain events for decoupled side effects
- **Listeners**: Event handlers for business operations

## API Documentation

### Interactive Documentation

Visit https://localhost/api/docs for comprehensive API documentation featuring:

- Complete endpoint reference with examples
- Interactive testing interface
- Authentication support
- Request/response schemas
- Domain-organized structure

### Available Endpoints

#### Authentication
- `POST /api/v1/auth/register` - User registration
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout
- `GET /api/v1/auth/me` - Get current user
- `POST /api/v1/auth/refresh` - Refresh token

#### User Management
- `GET /api/v1/users` - List all users
- `GET /api/v1/users/{id}` - Get specific user
- `PUT /api/v1/users/{id}` - Update user
- `DELETE /api/v1/users/{id}` - Delete user
- `GET /api/v1/users/search` - Search users

### Authentication

The API uses Laravel Sanctum for authentication:

1. Register or login to receive a bearer token
2. Include the token in subsequent requests:
   ```
   Authorization: Bearer your-token-here
   ```

## Development Commands

### Environment Management

```bash
# Start development environment (with hot reload)
make start

# Start in background
make start-bg

# Stop all containers
make stop

# Restart containers
make restart

# View container status
make status
```

### Database Operations

```bash
# Run migrations
make migrate

# Run seeders
make seed

# Fresh migration (drops all tables)
make migrate-fresh

# Backup database
make backup-db

# Restore database
make restore-db file=backup.sql
```

### Testing

```bash
# Run all tests
make test

# Run unit tests
make test-unit

# Run feature tests
make test-feature

# Run with coverage
make test-coverage
```

### Frontend Development

```bash
# Build production assets
make build

# Install npm packages
make npm cmd="install package-name"

# Watch for changes (development)
make watch
```

### Laravel Artisan Commands

```bash
# Run artisan commands
make artisan cmd="make:controller UserController"

# Open Laravel Tinker
make tinker

# Start queue worker
make queue
```

## Technology Stack

### Backend
- **Laravel 10** with PHP 8.1+
- **MySQL 8.0** database
- **Redis** for caching and sessions
- **Laravel Sanctum** for API authentication

### Frontend
- **React 18** with TypeScript
- **Vite** for build tooling
- **Tailwind CSS** for styling
- **Hot Module Replacement** for development

### Infrastructure
- **Docker** and **Docker Compose**
- **Nginx** web server
- **Adminer** for database management

## Container Architecture

| Service | Container | Port | Purpose |
|---------|-----------|------|---------|
| nginx | ldb-nginx | 80, 443 | Web server & reverse proxy |
| php | ldb-php | 9000 | PHP-FPM application server |
| mysql | ldb-mysql | 3306 | Database server |
| redis | ldb-redis | 6379 | Cache & session store |
| npm-dev | ldb-npm-dev | 5173 | Vite asset compilation |
| adminer | ldb-adminer | 8080 | Database management UI |

## Development Workflow

### Daily Development

1. **Start the development environment**:
   ```bash
   make start
   ```

2. **Make changes to your code**:
   - React/TypeScript files update instantly with HMR
   - Laravel changes require container restart

3. **Run Laravel commands**:
   ```bash
   make artisan cmd="make:model User -m"
   make migrate
   make test
   ```

4. **Stop when finished**:
   ```bash
   make stop
   ```

### Production Deployment

1. **Build optimized assets**:
   ```bash
   make build
   ```

2. **Use production docker-compose**:
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

3. **Run production optimizations**:
   ```bash
   make artisan cmd="config:cache"
   make artisan cmd="route:cache"
   make artisan cmd="view:cache"
   ```

## Configuration

### Environment Variables

Copy `src/.env.example` to `src/.env` and configure:

```bash
APP_URL=https://localhost
DB_HOST=mysql
DB_DATABASE=appdb
DB_USERNAME=user
DB_PASSWORD=secret

REDIS_HOST=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Docker Compose Override

Create `docker-compose.override.yml` for local customizations:

```yaml
version: '3.8'
services:
  php:
    environment:
      - XDEBUG_MODE=debug
```

## Troubleshooting

### Common Issues

**Permission Issues**:
```bash
make fix-permissions
```

**Database Connection Issues**:
```bash
make status
make logs-mysql
make migrate-fresh
```

**Container Issues**:
```bash
make restart
make clean
make fresh
```

**Development Issues**:
```bash
make stop
make start
docker logs ldb-npm-dev
```

### Logs

```bash
make logs          # All services
make logs-php      # PHP logs
make logs-nginx    # Nginx logs
make logs-mysql    # MySQL logs
```

## Testing

### HTTP Client Testing

The project includes `src/api.http` for testing endpoints with:

- VS Code REST Client extension
- JetBrains HTTP Client
- Postman (import the collection)

### Test Structure

```bash
tests/
├── Feature/       # Integration tests
├── Unit/          # Unit tests
└── TestCase.php   # Base test class
```

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Make your changes
4. Run tests: `make test`
5. Commit changes: `git commit -am 'Add new feature'`
6. Push to branch: `git push origin feature/new-feature`
7. Submit a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For issues, questions, or contributions:

- **Issues**: [GitHub Issues](https://github.com/anjotadena/laravel-docker-base/issues)
- **Email**: [tadena.anjo@gmail.com](mailto:tadena.anjo@gmail.com)
- **LinkedIn**: [Connect with me](https://www.linkedin.com/in/73ch801/)

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev/)
- [Docker Documentation](https://docs.docker.com/)
- [Vite Documentation](https://vitejs.dev/)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)