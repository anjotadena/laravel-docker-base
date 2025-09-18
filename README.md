# Laravel Docker Workflow
[![CircleCI](https://circleci.com/gh/anjotadena/laravel-docker-base/tree/master.svg?style=svg)](https://circleci.com/gh/anjotadena/laravel-docker-base/tree/master)[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A complete Docker-based development environment for Laravel with React/TypeScript frontend. This setup provides a robust, scalable, and easy-to-use development workflow.

## 🚀 Quick Start

### Prerequisites
- [Docker](https://docs.docker.com/get-docker/) (version 20.0 or higher)
- [Docker Compose](https://docs.docker.com/compose/install/) (version 2.0 or higher)
- Make (optional, for convenience commands)

### Fresh Installation

```bash
# Clone the repository
git clone <your-repo-url>
cd laravel-docker-base

# Make install script executable and run
chmod +x install.sh
./install.sh

# Or use Make command
make install
```

### Start Development

```bash
# Start all services
make dev

# Or manually
docker-compose up -d
```

Your application will be available at:
- **Frontend**: http://localhost
- **API Documentation**: http://localhost/api/documentation  
- **Database Admin**: http://localhost:8080 (Adminer)

## 🛠 Technology Stack

- **Backend**: Laravel 10 with PHP 8.1+
- **Frontend**: React 18 with TypeScript
- **Database**: MySQL 8.0
- **Caching**: Redis
- **Web Server**: Nginx
- **Build Tools**: Vite
- **Containerization**: Docker & Docker Compose

## ✨ Features

- 🔐 API authentication with Sanctum
- ⚡ Hot module replacement with Vite
- 🎨 Tailwind CSS for styling
- 🧪 PHPUnit testing setup
- 📊 API documentation with Swagger
- 🔄 Redis for caching and queuing
- 🐳 Complete Docker environment
- 🛡️ Automated permission fixes
- 📦 Automated dependency management

## 📋 Available Commands

### Installation & Setup
```bash
make help          # Show all available commands
make install       # Fresh installation with prompts
make fresh         # Clean install (removes all data)
make quick         # Quick setup (skip tests & build)
```

### Development
```bash
make dev           # Start development mode (with Vite)
make dev-bg        # Start in background
make start         # Start containers
make stop          # Stop containers  
make restart       # Restart containers
make status        # Show container status
make logs          # Show all logs
```

### Database
```bash
make migrate       # Run migrations
make seed          # Run seeders
make migrate-seed  # Run migrations and seeders
make migrate-fresh # Fresh migration (drops tables)
make backup-db     # Backup database
make restore-db file=backup.sql  # Restore database
```

### Testing
```bash
make test          # Run all tests
make test-unit     # Run unit tests
make test-feature  # Run feature tests
make test-coverage # Run tests with coverage
```

### Frontend
```bash
make build         # Build production assets
make watch         # Watch for changes
make npm cmd="install package"  # Run npm commands
```

### Laravel Artisan
```bash
make artisan cmd="make:controller UserController"
make tinker        # Open Laravel Tinker
make queue         # Start queue worker
```

## 🐳 Container Architecture

| Service | Container | Port | Purpose |
|---------|-----------|------|---------|
| **nginx** | ldb-nginx | 80, 443 | Web server & reverse proxy |
| **php** | ldb-php | 9000 | PHP-FPM application server |
| **mysql** | ldb-mysql | 3306 | Database server |
| **redis** | ldb-redis | 6379 | Cache & session store |
| **npm** | ldb-npm | 5173 | Node.js & Vite dev server |
| **adminer** | ldb-adminer | 8080 | Database management UI |

## 📁 Project Structure

```
laravel-docker-base/
├── docker/                 # Docker configuration
│   ├── nginx/             # Nginx configuration
│   ├── php/               # PHP-FPM configuration  
│   ├── mysql/             # MySQL configuration
│   └── npm/               # Node.js configuration
├── src/                   # Laravel application
│   ├── app/               # Laravel app code
│   ├── resources/         # Views, assets, lang
│   │   ├── ts/           # TypeScript/React code
│   │   └── css/          # Stylesheets
│   ├── public/           # Web root
│   ├── database/         # Migrations, seeders
│   └── tests/            # Test files
├── docker-compose.yml     # Development compose file
├── docker-compose.prod.yml # Production compose file
├── install.sh            # Installation script
├── Makefile              # Make commands
└── README.md
```

## 🚀 Development Workflow

### 1. Initial Setup
```bash
# Clone and install
git clone <repo-url>
cd laravel-docker-base
make install
```

### 2. Daily Development
```bash
# Start development environment
make dev

# In another terminal, make changes and run commands:
make artisan cmd="make:controller ApiController"
make migrate
make test
```

### 3. Frontend Development
```bash
# Start Vite dev server (with hot reload)
make dev

# Build for production
make build

# Install new npm packages
make npm cmd="install axios"
```

### 4. Database Management
```bash
# Create migration
make migration name=create_posts_table

# Run migration
make migrate

# Seed database
make seed

# Access database via Adminer: http://localhost:8080
# Server: mysql, User: user, Password: secret, Database: appdb
```

## 🔧 Configuration

### Environment Variables
Copy `src/.env.example` to `src/.env` and modify as needed:

```bash
APP_URL=http://localhost
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

## 🐛 Troubleshooting

### Permission Issues
Laravel storage permission problems are automatically fixed during installation, but if you encounter issues:

```bash
make fix-permissions
```

### Database Connection Issues
```bash
# Check if MySQL container is running
make status

# View MySQL logs
make logs-mysql

# Reset database
make migrate-fresh
```

### Container Issues
```bash
# Restart all containers
make restart

# Clean rebuild
make clean
make fresh
```

### Vite/Frontend Issues
```bash
# Clear node modules and reinstall
make npm cmd="cache clean --force"
rm -rf src/node_modules
make npm-install

# Rebuild assets
make build
```

## 🧪 Testing

### Running Tests
```bash
# All tests
make test

# Specific test suite
make test-unit
make test-feature

# With coverage
make test-coverage
```

### Creating Tests
```bash
# Create unit test
make artisan cmd="make:test UserTest --unit"

# Create feature test  
make artisan cmd="make:test ApiTest"
```

## 📦 Deployment

### Production Build
```bash
# Build optimized assets
make deploy-build

# Use production docker-compose
docker-compose -f docker-compose.prod.yml up -d
```

### SSL/HTTPS
```bash
# Renew SSL certificates (production)
make ssl-renew
```

## 🔍 Monitoring & Debugging

### Logs
```bash
make logs          # All services
make logs-php      # PHP logs only
make logs-nginx    # Nginx logs only
make logs-mysql    # MySQL logs only
```

### Laravel Specific Logs
- Application logs: `src/storage/logs/laravel.log`
- Clear logs: `make clean-logs`

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Make your changes
4. Run tests: `make test`
5. Commit changes: `git commit -am 'Add new feature'`
6. Push to branch: `git push origin feature/new-feature`
7. Submit a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For issues, questions, or contributions:

- **Issues**: [GitHub Issues](https://github.com/anjotadena/laravel-docker-base/issues)
- **Email**: [tadena.anjo@gmail.com](mailto:tadena.anjo@gmail.com)
- **LinkedIn**: [Connect with me](https://www.linkedin.com/in/73ch801/)

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev/)
- [Docker Documentation](https://docs.docker.com/)
- [Vite Documentation](https://vitejs.dev/)

---

**Happy coding! 🎉** 
