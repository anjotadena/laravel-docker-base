# Quick Start Guide

## From Zero to Development in 5 Minutes

### 1. Prerequisites Check
```bash
# Check if Docker is installed
docker --version
docker-compose --version

# If not installed:
# - Install Docker: https://docs.docker.com/get-docker/
# - Install Docker Compose: https://docs.docker.com/compose/install/
```

### 2. Clone and Setup
```bash
git clone <your-repo-url>
cd laravel-docker-base
```

### 3. Automated Installation
```bash
# Option A: Interactive installation (recommended for first time)
make install

# Option B: Silent fresh installation  
./install.sh --fresh --skip-tests

# Option C: Quick setup (minimal)
make quick
```

### 4. Start Development
```bash
# Start development environment
make dev
```

### 5. Verify Installation
Open your browser and visit:
- **Main App**: http://localhost
- **Database Admin**: http://localhost:8080

## Common First-Time Commands

```bash
# Check everything is running
make status

# View logs if something's wrong
make logs

# Run migrations (if not done during install)
make migrate

# Create your first controller
make artisan cmd="make:controller HomeController"

# Install a new package
make composer cmd="require vendor/package"

# Run tests
make test
```

## Troubleshooting First Setup

### If containers fail to start:
```bash
# Check what's using the ports
sudo netstat -tulpn | grep :80
sudo netstat -tulpn | grep :3306

# Stop conflicting services
sudo service apache2 stop  # if Apache is running
sudo service nginx stop    # if Nginx is running
sudo service mysql stop    # if MySQL is running

# Try again
make fresh
```

### If you see permission errors:
```bash
# Fix permissions
make fix-permissions

# Or run the permission fix from install script
sudo chmod +x install.sh
./install.sh --skip-tests --skip-build
```

### If database connection fails:
```bash
# Wait longer for MySQL to initialize (especially first time)
make logs-mysql

# Check MySQL is ready
docker-compose exec mysql mysql -u root -psecret -e "SHOW DATABASES;"
```

## Next Steps

1. **Explore the codebase**: Check `src/` directory
2. **Read the main README.md**: For complete documentation
3. **Set up your IDE**: Configure for Docker development
4. **Create your first feature**: Start building!

## Development Workflow

```bash
# Daily workflow
make dev              # Start development
# Make your changes in src/
make test             # Test your changes
make migrate          # If you created migrations
git add . && git commit -m "Your changes"
```

That's it! You're ready to develop with Laravel + Docker + React! 🚀
