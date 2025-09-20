# Laravel Docker Workflow
[![CircleCI](https://circleci.com/gh/anjotadena/laravel-docker-base/tree/master.svg?style=svg)](https://circleci.com/gh/anjotadena/laravel-docker-base/tree/master) ![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)

### 🛠 Development Mode (Watch Mode)
**Important**: You don't access port 5173 directly. Vite runs internally to compile assets that are served through your Laravel application on port 80.

## 🔗 Laravel + Vite Integration

This project uses Laravel's native Vite integration, meaning:

### How Assets Are Served

**Development Mode** (`make start`):
1. Vite dev server runs on port 5173 (internal)
2. Laravel views include `@vite` directives
3. Assets are served through Laravel on port 80 with HMR
4. You work with your app at `http://localhost`

**Production Mode** (`make build` + `make start-basic`):
1. Vite builds optimized assets to `public/build/`
2. Laravel serves static assets directly
3. No dev server needed, just optimized bundles

### Laravel Blade Integration

Your Laravel views should include:
```blade
@vite(['resources/ts/main.tsx', 'resources/ts/index.scss'])
```

This automatically:
- ✅ Loads HMR scripts in development
- ✅ Loads optimized bundles in production
- ✅ Handles asset versioning and caching

## 🔧 Configurationr active development work**

```bash
make start          # ✅ RECOMMENDED: Vite watch mode with HMR
make start-bg       # Same as above but runs in background
make dev           # Alternative development setup
```

**Features:**
- 🔄 Hot Module Replacement (HMR)
- 📁 File watching and auto-compilation
- 🚀 Fast rebuilds and instant updates
- 🐛 Source maps for debugging

### 📦 Production Mode (Build Mode)
**Use for production deployment**

```bash
make build          # ✅ Build optimized assets
make start-basic    # Start containers (no watch mode)
make deploy-build   # Complete production setup
```

**Features:**
- 🗜️ Minified and optimized bundles
- 🌳 Tree-shaking removes unused code
- 📈 Performance optimizations
- 🎯 Asset versioning and caching

### Quick Reference

| Task | Development | Production |
|------|-------------|------------|
| **Start Environment** | `make start` | `make build` → `make start-basic` |
| **Asset Processing** | Watch mode (HMR) | Build mode (optimized) |
| **File Changes** | Instant updates | Manual rebuild required |
| **Performance** | Development optimized | Production optimized |
| **Debugging** | Source maps enabled | Minified code |/MIT)

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

### Development Environment

```bash
# Start development with Vite watch mode (RECOMMENDED for development)
make start

# Start in background with watch mode
make start-bg

# Alternative development mode
make dev
```

### Production Environment

```bash
# Build optimized assets for production
make build

# Start containers without watch mode (for production)
make start-basic
```

Your application will be available at:
- **Main Application**: http://localhost (Laravel views with compiled assets)
- **API Documentation**: http://localhost/api/documentation  
- **Database Admin**: http://localhost:8080 (Adminer)

**Note**: Port 5173 is used internally by Vite for asset compilation and HMR. Your main application runs on port 80 through Laravel.

## 🛠 Technology Stack

- **Backend**: Laravel 10 with PHP 8.1+
- **Frontend**: React 18.2.0 with TypeScript
- **Database**: MySQL 8.0
- **Caching**: Redis
- **Web Server**: Nginx
- **Build Tools**: Vite with HMR
- **Containerization**: Docker & Docker Compose

## ✨ Features

- 🔐 API authentication with Sanctum
- ⚡ Hot module replacement with Vite watch mode
- 🎨 Tailwind CSS for styling
- 🧪 PHPUnit testing setup
- 📊 API documentation with Swagger
- 🔄 Redis for caching and queuing
- 🐳 Complete Docker environment
- 🛡️ Automated permission fixes
- 📦 Automated dependency management
- 🔧 React 18 with full TypeScript support
- 🎯 Drag & Drop with react-beautiful-dnd

## 📋 Available Commands

### Installation & Setup
```bash
make help          # Show all available commands
make install       # Fresh installation with prompts
make fresh         # Clean install (removes all data)
make quick         # Quick setup (skip tests & build)
```

### Development (Watch Mode)
```bash
make start         # 🔥 Start with Vite watch mode (RECOMMENDED)
make start-bg      # Start with watch mode (background)
make dev           # Alternative development mode
make dev-bg        # Dev mode in background
```

### Production (Build Mode)
```bash
make build         # 📦 Build optimized assets for production
make start-basic   # Start containers without watch mode
make deploy-build  # Complete production build setup
```

### Container Management
```bash
make stop          # Stop all containers
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
| **nginx** | ldb-nginx | 80, 443 | Web server & reverse proxy (main app) |
| **php** | ldb-php | 9000 | PHP-FPM application server |
| **mysql** | ldb-mysql | 3306 | Database server |
| **redis** | ldb-redis | 6379 | Cache & session store |
| **npm-dev** | ldb-npm-dev | 5173* | Vite asset compilation (internal) |
| **npm** | ldb-npm | - | Node.js for running commands |
| **adminer** | ldb-adminer | 8080 | Database management UI |

*Port 5173 is used internally for asset compilation. Access your app at http://localhost (port 80)

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

### 2. Development Mode (Watch Mode)
**Use this for active development work:**

```bash
# Start development environment with Vite watch mode
make start

# Your development environment provides:
# ✅ Hot Module Replacement (HMR) for instant React updates
# ✅ Automatic file watching and compilation
# ✅ Live reload for all frontend changes
# ✅ Source maps for debugging
```

**In another terminal, run Laravel commands:**
```bash
make artisan cmd="make:controller ApiController"
make migrate
make test
```

### 3. Production Mode (Build Mode)
**Use this for production deployment:**

```bash
# Build optimized assets (minified, tree-shaken)
make build

# Start containers without watch mode
make start-basic

# Your production build provides:
# ✅ Minified and optimized JavaScript/CSS
# ✅ Tree-shaking to remove unused code
# ✅ Asset versioning and caching
# ✅ Production-ready performance
```

### 4. Frontend Development Guidelines

#### Development Commands
```bash
# Install new packages
make npm cmd="install axios"

# Development with watch mode (ALWAYS use for development)
make start

# Check for updates
make npm cmd="audit"
```

#### Production Commands
```bash
# Build for production deployment
make build

# Test production build locally
make start-basic

# Deploy production assets
make deploy-build
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

## � Vite Watch Mode & HMR

This project includes an advanced Vite development setup with Hot Module Replacement (HMR) for an optimal development experience.

### Watch Mode Commands

```bash
# Primary development command (recommended)
make start          # Starts containers + Vite dev server with HMR

# Background development
make start-bg       # Same as above but runs in background

# Containers only (no frontend watching)
make start-basic    # Just Laravel containers, no Vite

# Legacy development mode
make dev           # Alternative development setup
```

### Features

- **🔄 Hot Module Replacement**: Changes to React/TypeScript files are instantly reflected in the browser
- **📁 File Watching**: Automatic detection of file changes using optimized polling for Docker
- **⚡ Fast Builds**: Vite's lightning-fast bundling with React 18 support
- **🎨 SASS Processing**: Automatic compilation of SASS files with proper @use rule ordering
- **🐳 Docker Optimized**: Configured specifically for containerized development

### Development Workflow

1. **Start watching**: `make start`
2. **Edit files**: Make changes to any `.tsx`, `.ts`, `.scss`, or `.css` files
3. **Instant feedback**: See changes immediately in your browser at `http://localhost`
4. **HMR integration**: Frontend changes are hot-reloaded through Laravel views
5. **Backend changes**: Laravel changes require container restart

### How It Works

- **Main App**: http://localhost (Laravel serves views with compiled assets)
- **Asset Pipeline**: Vite compiles and injects assets into Laravel views via HMR
- **Database Admin**: http://localhost:8080

**Important**: You don't access port 5173 directly. Vite runs internally to compile assets that are served through your Laravel application on port 80.

## �🔧 Configuration

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

### Development Issues (Watch Mode)
```bash
# Restart development environment
make stop
make start                    # Restarts with watch mode

# Clear caches and rebuild
make npm cmd="cache clean --force"
rm -rf src/node_modules
make npm-install
make start

# Check Vite dev server logs (internal compilation)
docker logs ldb-npm-dev

# Check if HMR is working by visiting your Laravel app
curl http://localhost
```

### Production Issues (Build Mode)
```bash
# Rebuild production assets
make build                    # Create optimized bundles

# Clear all caches
make deploy-clear            # Clear Laravel caches
make npm cmd="cache clean --force"

# Test production build locally
make start-basic             # Start without watch mode

# Check build output
ls -la src/public/build/
```

### Common Issues

#### "HMR not working" (Development)
```bash
# Ensure you're using development mode
make start                   # ✅ Correct command
# NOT: make start-basic      # ❌ This is for production
```

#### "Assets not updating" (Production)
```bash
# Rebuild assets after changes
make build                   # ✅ Required for production
make restart                 # Restart containers
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

## 🔧 Recommended Development Steps

### Daily Development Workflow
```bash
# 1. Start development environment
make start                    # Starts containers + Vite watch mode

# 2. Open your editor and start coding
# - React/TypeScript files: Instant HMR updates via Laravel views
# - SASS/CSS files: Automatic compilation
# - Changes reflect immediately at http://localhost (main app)

# 3. Run Laravel commands in another terminal
make artisan cmd="make:model User -m"
make migrate
make test

# 4. Install new packages when needed
make npm cmd="install lodash"
make composer cmd="require laravel/sanctum"

# 5. Stop when done
make stop
```

### Frontend Development Best Practices
```bash
# Always use watch mode for development
make start                    # ✅ Correct for development

# Never use build mode for development
make build && make start-basic # ❌ Wrong for development (slow, no HMR)
```

## 🚀 Recommended Production Deployment

### Production Deployment Checklist

#### 1. Pre-deployment Preparation
```bash
# Build optimized assets
make build                    # Creates minified, optimized bundles

# Run tests
make test                     # Ensure everything works

# Check for security issues
make npm cmd="audit --audit-level high"
```

#### 2. Production Environment Setup
```bash
# Use production docker-compose
docker-compose -f docker-compose.prod.yml up -d

# Or complete production build
make deploy-build            # Includes:
                            # - Composer install --no-dev
                            # - npm run build
                            # - Laravel cache optimization
```

#### 3. Production Optimization Commands
```bash
# Laravel optimizations
make artisan cmd="config:cache"      # Cache configuration
make artisan cmd="route:cache"       # Cache routes
make artisan cmd="view:cache"        # Cache views
make artisan cmd="optimize"          # General optimization

# Database setup
make migrate --env=production
```

#### 4. Production Monitoring
```bash
# Check production logs
make logs-nginx              # Web server logs
make logs-php               # Application logs

# Monitor container health
make status                 # Container status
docker stats                # Resource usage
```

### Environment-Specific Commands

#### Development Environment
```bash
# Start development (with watch mode)
make start                  # Always use this for development

# Clear development caches
make artisan cmd="config:clear"
make artisan cmd="route:clear"
make artisan cmd="view:clear"
```

#### Production Environment
```bash
# Build and start production
make build                  # Build optimized assets
make start-basic           # Start without watch mode

# Production caching
make artisan cmd="config:cache"
make artisan cmd="route:cache"
make artisan cmd="view:cache"
```

### SSL/HTTPS (Production)
```bash
# Setup SSL certificates
make ssl-renew             # Renew SSL certificates

# Use HTTPS docker-compose
docker-compose -f docker-compose.prod.yml up -d
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

## � Current Package Versions

### Core Dependencies
- **React**: 18.2.0
- **React DOM**: 18.2.0
- **TypeScript**: 5.4.5
- **Vite**: 5.2.8
- **Laravel Vite Plugin**: 1.0.2

### Key Frontend Libraries
- **UI Components**: react-beautiful-dnd (13.1.1), react-select (5.8.0)
- **State Management**: @reduxjs/toolkit (2.2.3), react-redux (9.1.0)
- **Routing**: react-router-dom (6.22.3)
- **Styling**: tailwindcss (3.4.3), styled-components (6.1.8)
- **Forms**: formik (2.4.5), yup (1.4.0)
- **Charts**: apexcharts (4.0.0), react-apexcharts (1.7.0)

### React 19 Upgrade Path
If you want to upgrade to React 19, you'll need to:

```bash
# Update React and React DOM
make npm cmd="install react@19 react-dom@19"

# Replace react-beautiful-dnd with @hello-pangea/dnd (React 19 compatible)
make npm cmd="uninstall react-beautiful-dnd @types/react-beautiful-dnd"
make npm cmd="install @hello-pangea/dnd@18.0.1"

# Update TypeScript types
make npm cmd="install --save-dev @types/react@19 @types/react-dom@19"

# Rebuild and test
make build
make test
```

## �📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev/)
- [Docker Documentation](https://docs.docker.com/)
- [Vite Documentation](https://vitejs.dev/)

---

**Happy coding! 🎉** 
