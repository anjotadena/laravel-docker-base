# Quick Start Guide

## From Zero to Development in 5 Minutes

This guide will get you up and running with the Laravel Docker Base application quickly and efficiently.

## Prerequisites

Before starting, ensure you have the following installed:

- **Docker** (version 20.0 or higher)
- **Docker Compose** (version 2.0 or higher)
- **Git**
- **Make** (optional, but recommended for convenience)

### Verify Prerequisites

```bash
# Check if Docker is installed
docker --version
docker-compose --version

# If not installed, visit:
# - Docker: https://docs.docker.com/get-docker/
# - Docker Compose: https://docs.docker.com/compose/install/
```

## Installation Options

### Option 1: Interactive Installation (Recommended)

Perfect for first-time setup with guided prompts:

```bash
# Clone the repository
git clone <your-repo-url>
cd laravel-docker-base

# Run interactive installation
make install
```

### Option 2: Quick Setup

For experienced developers who want minimal setup:

```bash
# Quick setup (skips tests and frontend build)
make quick
```

### Option 3: Fresh Installation

For a completely clean start (removes all existing data):

```bash
# Fresh installation with confirmation prompt
make fresh

# Or silent fresh installation
./install.sh --fresh --skip-tests
```

## Start Development Environment

### Development Mode (Recommended)

Start the full development environment with hot module replacement:

```bash
# Start with Vite watch mode (best for development)
make start

# Start in background
make start-bg
```

### Basic Mode

Start containers without frontend watching (for production-like testing):

```bash
# Start containers only
make start-basic
```

## Verify Installation

Once started, verify everything is working:

### Check Container Status

```bash
# View container status
make status
```

### Access Your Application

Open your browser and visit:

- **Main Application**: https://localhost
- **API Documentation**: https://localhost/api/docs
- **Database Admin**: http://localhost:8080

### Test API Endpoints

Use the provided HTTP client file:

```bash
# Open the API testing file
code src/api.http  # VS Code with REST Client extension
```

## Essential Commands

### Container Management

```bash
# Start development environment
make start

# Stop all containers
make stop

# Restart containers
make restart

# View container logs
make logs

# View specific service logs
make logs-php
make logs-nginx
make logs-mysql
```

### Database Operations

```bash
# Run migrations
make migrate

# Run migrations and seeders
make migrate-seed

# Fresh migration (drops all tables)
make migrate-fresh

# Create new migration
make migration name=create_users_table
```

### Laravel Development

```bash
# Run Artisan commands
make artisan cmd="make:controller UserController"
make artisan cmd="make:model User -m"

# Open Laravel Tinker
make tinker

# Start queue worker
make queue
```

### Package Management

```bash
# Install PHP packages
make composer cmd="require laravel/sanctum"

# Install Node packages
make npm cmd="install axios"

# Update dependencies
make composer-update
make npm-update
```

### Testing

```bash
# Run all tests
make test

# Run specific test suites
make test-unit
make test-feature

# Run tests with coverage
make test-coverage
```

### Frontend Development

```bash
# Build production assets
make build

# Watch for changes (development)
make watch

# Install Node dependencies
make npm-install
```

## Troubleshooting

### Common Setup Issues

#### Port Conflicts

If containers fail to start due to port conflicts:

```bash
# Check what's using the ports
sudo netstat -tulpn | grep :80
sudo netstat -tulpn | grep :3306

# Stop conflicting services
sudo service apache2 stop  # if Apache is running
sudo service nginx stop    # if Nginx is running
sudo service mysql stop    # if MySQL is running

# Try fresh installation
make fresh
```

#### Permission Issues

If you encounter permission errors:

```bash
# Fix storage permissions
make fix-permissions

# Or run permission fix manually
sudo chown -R $(USER):www-data src/storage src/bootstrap/cache
chmod -R 775 src/storage src/bootstrap/cache
```

#### Database Connection Issues

If database connection fails:

```bash
# Check MySQL logs
make logs-mysql

# Wait for MySQL to initialize (first time can take longer)
# Check if MySQL is ready
docker-compose exec mysql mysql -u root -psecret -e "SHOW DATABASES;"

# Run migrations manually
make migrate
```

#### Container Issues

If containers are not responding:

```bash
# Restart all containers
make restart

# Clean rebuild
make clean
make fresh

# Check container status
make status
```

### SSL Certificate Warnings

The development environment uses self-signed certificates:

- **Browser**: Accept the security warning
- **Command Line**: Use `-k` flag with curl: `curl -k https://localhost`

## Development Workflow

### Daily Development Process

1. **Start your environment**:
   ```bash
   make start
   ```

2. **Make your changes**:
   - Edit files in `src/` directory
   - React/TypeScript changes update instantly with HMR
   - Laravel changes may require container restart

3. **Test your changes**:
   ```bash
   make test
   ```

4. **Commit your work**:
   ```bash
   git add .
   git commit -m "Your changes"
   ```

5. **Stop when finished**:
   ```bash
   make stop
   ```

### Creating New Features

```bash
# Create a new controller
make artisan cmd="make:controller ApiController"

# Create a new model with migration
make artisan cmd="make:model Product -m"

# Run migrations
make migrate

# Create tests
make artisan cmd="make:test ProductTest"
```

## Production Deployment

### Build for Production

```bash
# Build optimized assets
make build

# Deploy with production optimizations
make deploy-build
```

### Production Commands

```bash
# Use production docker-compose
docker-compose -f docker-compose.prod.yml up -d

# Clear production caches
make deploy-clear

# Renew SSL certificates
make ssl-renew
```

## Useful Tips

### Performance Optimization

```bash
# Clear Laravel caches
make artisan cmd="config:clear"
make artisan cmd="route:clear"
make artisan cmd="view:clear"

# Optimize for production
make artisan cmd="config:cache"
make artisan cmd="route:cache"
make artisan cmd="view:cache"
```

### Database Management

```bash
# Backup database
make backup-db

# Restore database
make restore-db file=backup_20240101_120000.sql

# Access database directly
docker-compose exec mysql mysql -u root -psecret appdb
```

### Cleanup Commands

```bash
# Clean containers and images
make clean

# Clean everything including volumes
make clean-all

# Clear Laravel logs
make clean-logs
```

## Next Steps

1. **Explore the Architecture**: Check the `src/app/Domains/` directory to understand the DDD structure
2. **Read the Full Documentation**: See `README.md` for comprehensive documentation
3. **Set Up Your IDE**: Configure your development environment for Docker
4. **Start Building**: Create your first feature using the established patterns

## Getting Help

If you encounter issues:

1. **Check the logs**: `make logs`
2. **Verify container status**: `make status`
3. **Review the main README**: Comprehensive troubleshooting guide
4. **Check GitHub Issues**: For known problems and solutions

## Quick Reference

| Task | Command |
|------|---------|
| **Start Development** | `make start` |
| **Stop Environment** | `make stop` |
| **Run Tests** | `make test` |
| **Run Migrations** | `make migrate` |
| **View Logs** | `make logs` |
| **Fix Permissions** | `make fix-permissions` |
| **Clean Everything** | `make clean` |
| **Fresh Install** | `make fresh` |

---

**You're now ready to develop with Laravel + Docker + React!**

For complete documentation, see the main `README.md` file.