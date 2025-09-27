# Laravel DDD API Documentation

A comprehensive REST API built with Domain-Driven Design (DDD) architecture using Laravel, featuring Laravel Sanctum authentication and interactive API documentation.

## Features

- **Domain-Driven Design** architecture with clean separation of concerns
- **Laravel Sanctum** authentication with bearer token support
- **Interactive API Documentation** powered by Laravel Scramble
- **HTTP Client Testing** files for easy endpoint testing
- **Docker** development environment ready
- **Modern Laravel** with best practices

## Quick Start

1. **Start the Docker environment**:
   ```bash
   docker-compose up -d
   ```

2. **Access the API Documentation**:
   - Interactive Documentation: [https://localhost/api/docs](https://localhost/api/docs)
   - OpenAPI JSON Spec: [https://localhost/docs/api.json](https://localhost/docs/api.json)

3. **Test the API**:
   - Use the provided `api.http` file with your HTTP client
   - Try the interactive documentation's "Try It" feature

## API Documentation

### Interactive Documentation

Visit [https://localhost/api/docs](https://localhost/api/docs) to access the comprehensive interactive API documentation powered by Laravel Scramble. The documentation includes:

- **Complete endpoint reference** with request/response examples
- **Interactive testing** - try endpoints directly from the browser
- **Authentication support** - automatically handles bearer tokens
- **Schema documentation** - detailed request and response structures

### Available Endpoints

#### Authentication
- `POST /api/v1/auth/register` - User registration
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout (requires authentication)
- `GET /api/v1/auth/me` - Get current user (requires authentication)
- `POST /api/v1/auth/refresh` - Refresh token (requires authentication)

#### User Management
- `GET /api/v1/users` - List all users (requires authentication)
- `GET /api/v1/users/{id}` - Get specific user (requires authentication)
- `PUT /api/v1/users/{id}` - Update user (requires authentication)
- `DELETE /api/v1/users/{id}` - Delete user (requires authentication)
- `GET /api/v1/users/search` - Search users (requires authentication)

## Testing with HTTP Files

The project includes a comprehensive HTTP testing file at `api.http` with:

- Pre-configured environment variables
- Complete endpoint coverage
- Automatic token management
- Manual token alternatives

### Setup for HTTP Testing

1. **Using REST Client extension in VS Code**:
   - Install the "REST Client" extension
   - Open `api.http`
   - Configure environment in `http-client.env.json`

2. **Environment Configuration**:
   ```json
   {
     "development": {
       "baseUrl": "https://localhost",
       "token": "your-bearer-token-here"
     }
   }
   ```

## Authentication

The API uses **Laravel Sanctum** for authentication:

1. **Register** or **Login** to receive a bearer token
2. **Include the token** in subsequent requests:
   ```
   Authorization: Bearer your-token-here
   ```
3. **Token management** is handled automatically in the HTTP testing files

## Architecture

This project follows **Domain-Driven Design (DDD)** principles:

```
app/
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

### Domain Structure

Each domain contains:
- **Controllers** - HTTP request handlers
- **Services** - Business logic and use cases
- **Repositories** - Data access patterns
- **DTOs** - Data transfer objects with validation
- **Value Objects** - Domain concepts with business rules
- **Events** - Domain events for decoupled side effects
- **Listeners** - Event handlers for business operations
- **Exceptions** - Domain-specific exceptions

## Development

### Requirements
- Docker & Docker Compose
- PHP 8.1+ (for local development)
- Composer (for dependency management)

### Local Setup

1. **Clone the repository**
2. **Copy environment file**:
   ```bash
   cp .env.example .env
   ```
3. **Start Docker services**:
   ```bash
   docker-compose up -d
   ```
4. **Install dependencies**:
   ```bash
   docker exec -it ldb-php composer install
   ```
5. **Generate application key**:
   ```bash
   docker exec -it ldb-php php artisan key:generate
   ```
6. **Run migrations**:
   ```bash
   docker exec -it ldb-php php artisan migrate
   ```

### Available Services
- **Web Application**: [https://localhost](https://localhost)
- **API Documentation**: [https://localhost/api/docs](https://localhost/api/docs)
- **Database (MySQL)**: `localhost:3306`
- **Database Admin (Adminer)**: [http://localhost:8080](http://localhost:8080)
- **Redis**: `localhost:6379`

## Testing

### Running Tests
```bash
# Run all tests
docker exec -it ldb-php php artisan test

# Run specific test suite
docker exec -it ldb-php php artisan test --testsuite=Feature

# Run with coverage
docker exec -it ldb-php php artisan test --coverage
```

### HTTP Client Testing
Use the provided `api.http` file with:
- **VS Code REST Client** extension
- **JetBrains HTTP Client**
- **Postman** (import the collection)

## API Features

- **RESTful Design** - Follows REST principles
- **OpenAPI 3.1** - Complete specification generation  
- **Interactive Docs** - Try endpoints in the browser
- **Bearer Token Auth** - Secure authentication via Sanctum
- **Request/Response Validation** - Comprehensive validation
- **Error Handling** - Consistent error responses
- **Rate Limiting** - Built-in API rate limiting
- **CORS Support** - Cross-origin resource sharing

## Troubleshooting

### Common Issues

1. **SSL Certificate Warnings**:
   - The development environment uses self-signed certificates
   - Accept the security warning in your browser or use `-k` flag with curl

2. **Documentation Not Loading**:
   - Ensure Docker services are running: `docker-compose ps`
   - Clear Laravel cache: `docker exec -it ldb-php php artisan cache:clear`
   - Check route configuration: `docker exec -it ldb-php php artisan route:list`

3. **Authentication Issues**:
   - Verify token is included in requests
   - Check token format: `Authorization: Bearer your-token-here`
   - Ensure token hasn't expired

### Logs
- **Laravel Logs**: `storage/logs/laravel.log`
- **Nginx Logs**: `docker logs ldb-nginx`
- **PHP Logs**: `docker logs ldb-php`

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).