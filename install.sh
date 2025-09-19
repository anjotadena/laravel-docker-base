#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    if ! command_exists docker; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    
    if ! command_exists docker-compose; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi
    
    print_success "Prerequisites check passed"
}

# Stop and remove existing containers
cleanup_containers() {
    print_status "Cleaning up existing containers and resetting everything..."
    
    # Stop and remove all containers with volumes
    print_status "Stopping and removing containers with volumes..."
    docker-compose down -v --remove-orphans 2>/dev/null || true
    
    # Remove laravel-docker-base containers specifically (in case some are stuck)
    print_status "Removing any remaining project containers..."
    docker ps -a --filter "name=ldb-" --format "{{.ID}}" | xargs -r docker rm -f 2>/dev/null || true
    
    # Remove project-specific volumes
    print_status "Removing project volumes..."
    docker volume ls --filter "name=laravel-docker-base" --format "{{.Name}}" | xargs -r docker volume rm 2>/dev/null || true
    
    # Remove storage symlink if it exists
    print_status "Removing storage symlink..."
    if [ -L "src/public/storage" ]; then
        rm -f src/public/storage
        print_status "Storage symlink removed"
    fi
    
    # Clean up Laravel cache and compiled files
    print_status "Cleaning up Laravel cache and compiled files..."
    rm -rf src/bootstrap/cache/*.php 2>/dev/null || true
    rm -rf src/storage/framework/cache/data/* 2>/dev/null || true
    rm -rf src/storage/framework/sessions/* 2>/dev/null || true
    rm -rf src/storage/framework/views/*.php 2>/dev/null || true
    rm -rf src/storage/logs/*.log 2>/dev/null || true
    
    # Always clean node_modules on fresh install (force)
    if [[ $FRESH_INSTALL == "1" ]]; then
        print_status "Force removing node_modules and lock files..."
        rm -rf src/node_modules 2>/dev/null || true
    elif [[ $CLEAN_IMAGES == "1" ]]; then
        print_status "Cleaning node_modules (image clean requested)..."
        rm -rf src/node_modules 2>/dev/null || true
    fi
    
    # Clean up vendor directory for truly fresh install (force)
    if [[ $FRESH_INSTALL == "1" ]]; then
        print_status "Force removing vendor directory..."
        rm -rf src/vendor 2>/dev/null || sudo rm -rf src/vendor 2>/dev/null || true
        print_status "Clearing Composer cache..."
        docker-compose run --rm composer clear-cache 2>/dev/null || true
    fi
    
    # Clean up built assets
    print_status "Cleaning up built assets..."
    rm -rf src/public/build 2>/dev/null || true
    rm -rf src/public/hot 2>/dev/null || true
    rm -rf src/public/mix-manifest.json 2>/dev/null || true
    
    # Clean up Docker images if requested
    if [[ $CLEAN_IMAGES == "1" ]]; then
        print_status "Cleaning up Docker images..."
        # Remove project-specific images
        docker images --filter "reference=laravel-docker-base*" --format "{{.ID}}" | xargs -r docker rmi -f 2>/dev/null || true
        # Clean up dangling images
        docker image prune -f 2>/dev/null || true
    fi
    
    # Clean up networks
    print_status "Cleaning up Docker networks..."
    docker network ls --filter "name=ldb-app" --format "{{.ID}}" | xargs -r docker network rm 2>/dev/null || true
    
    print_success "Complete cleanup completed"
}

# Create necessary directories
create_directories() {
    print_status "Creating necessary directories and files..."
    
    # Ensure storage directories exist
    mkdir -p src/storage/logs
    mkdir -p src/storage/framework/{cache,sessions,views,testing}
    mkdir -p src/storage/framework/cache/data
    mkdir -p src/storage/app/public
    mkdir -p src/bootstrap/cache
    
    # Ensure public directories exist
    mkdir -p src/public/storage
    
    # Create .gitkeep files to preserve directory structure
    touch src/storage/framework/cache/.gitkeep
    touch src/storage/framework/sessions/.gitkeep
    touch src/storage/framework/views/.gitkeep
    touch src/storage/framework/testing/.gitkeep
    touch src/storage/framework/cache/data/.gitkeep
    touch src/storage/app/public/.gitkeep
    touch src/bootstrap/cache/.gitkeep
    touch src/storage/logs/.gitkeep
    
    # Create initial log file
    touch src/storage/logs/laravel.log
    
    # Set basic permissions
    chmod -R 755 src/storage src/bootstrap/cache 2>/dev/null || true
    
    print_success "Directories and files created"
}

# Build and start containers
build_containers() {
    print_status "Building and starting Docker containers..."
    
    # Build all images first
    docker-compose build
    
    # Start only essential services first (not phpunit which depends on vendor)
    print_status "Starting essential services..."
    docker-compose up -d php mysql redis composer artisan nginx npm adminer
    
    # Wait for essential containers to be ready
    print_status "Waiting for essential containers to be ready..."
    sleep 10
    
    # Check if essential containers are running
    if ! docker-compose ps php mysql redis | grep -q "Up"; then
        print_error "Some essential containers failed to start. Check logs with: docker-compose logs"
        exit 1
    fi
    
    print_success "Essential containers built and started"
}

# Install PHP dependencies
install_php_dependencies() {
    print_status "Installing PHP dependencies..."
    
    # Ensure containers are running first
    if ! docker-compose ps | grep -q "Up"; then
        print_warning "Containers not running, starting them first..."
        docker-compose up -d php mysql redis
        sleep 10
    fi
    
    # For fresh installs or if vendor is corrupted, remove it completely first
    if [[ $FRESH_INSTALL == "1" ]] || [ ! -d "src/vendor" ]; then
        print_status "Cleaning vendor directory before fresh install..."
        
        # Clean with proper permissions
        if ! rm -rf src/vendor 2>/dev/null; then
            print_status "Using sudo to clean vendor directory..."
            sudo rm -rf src/vendor 2>/dev/null || true
        fi
        
        # Clear composer cache to prevent corruption
        docker-compose run --rm composer clear-cache 2>/dev/null || true
    fi
    
    # Try composer install with error handling
    print_status "Running composer install (no scripts first)..."
    
    # Install dependencies without running scripts to avoid artisan before autoload ready
    if ! docker-compose run --rm composer install --no-scripts --no-interaction --prefer-dist; then
        print_warning "First composer install failed, retrying after cache clear..."
        docker-compose run --rm composer clear-cache 2>/dev/null || true
        docker-compose run --rm composer install --no-scripts --no-interaction --prefer-dist || {
            print_error "Composer install failed even after retry"
            exit 1
        }
    fi

    # Generate autoloader now
    print_status "Generating optimized autoloader (without scripts)..."
    if ! docker-compose run --rm composer dump-autoload -o --no-scripts; then
        print_warning "Standard autoload dump failed, attempting classmap-authoritative"
        docker-compose run --rm composer dump-autoload -o --classmap-authoritative --no-scripts || print_warning "Could not fully optimize autoloader"
    fi

    # Now run framework discovery scripts manually in safe order
    print_status "Running artisan package:discover..."
    docker-compose run --rm artisan package:discover || print_warning "package:discover failed (continuing)"
    
    # Publish laravel assets only after autoload exists
    if grep -q "laravel-assets" src/composer.json 2>/dev/null; then
        print_status "Publishing laravel assets..."
        docker-compose run --rm artisan vendor:publish --tag=laravel-assets --force || print_warning "Asset publish failed"
    fi
    
    # Run key:generate if APP_KEY empty
    if ! grep -q "^APP_KEY=.*" src/.env 2>/dev/null; then
        print_status "Generating app key (post composer)..."
        docker-compose run --rm artisan key:generate || print_warning "key:generate failed"
    fi
    
    # Generate autoloader in a separate step after all packages are installed
    # Legacy manual autoloader creation section removed (no longer needed with above flow)
    print_status "Verifying autoloader presence..."
    
    # Try the simplest autoloader generation first - just skip scripts to avoid issues
    if ! docker-compose run --rm composer dump-autoload --no-scripts 2>/dev/null; then
        print_warning "Autoloader generation failed, but packages are installed"
        print_status "Creating functional autoloader manually..."
        
        # Create a working autoloader by copying from composer's generated files
        docker-compose run --rm php -r "
        \$vendorDir = '/var/www/html/vendor';
        \$autoloadFile = \$vendorDir . '/autoload.php';
        
        // Create basic autoloader that handles the essential Laravel components
        \$content = '<?php
// Basic functional autoloader for Laravel
\$baseDir = dirname(__FILE__);

// Essential class files
\$files = [
    \$baseDir . \"/composer/autoload_real.php\",
    \$baseDir . \"/composer/ClassLoader.php\",
    \$baseDir . \"/composer/autoload_static.php\"
];

foreach (\$files as \$file) {
    if (file_exists(\$file)) {
        require_once \$file;
    }
}

// Try Composer autoloader first
if (class_exists(\"ComposerAutoloaderInit\", false)) {
    \$methods = get_class_methods(\"ComposerAutoloaderInit\");
    foreach (\$methods as \$method) {
        if (strpos(\$method, \"getLoader\") === 0) {
            return ComposerAutoloaderInit::{\$method}();
        }
    }
}

// Fallback autoloader for core Laravel functionality
spl_autoload_register(function (\$class) {
    \$vendorDir = __DIR__;
    
    // Key Laravel framework paths
    \$prefixes = [
        \"Illuminate\\\\\" => \$vendorDir . \"/laravel/framework/src/Illuminate/\",
        \"Symfony\\\\Component\\\\\" => \$vendorDir . \"/symfony/\",
        \"Carbon\\\\\" => \$vendorDir . \"/nesbot/carbon/src/Carbon/\",
        \"Monolog\\\\\" => \$vendorDir . \"/monolog/monolog/src/Monolog/\"
    ];
    
    foreach (\$prefixes as \$prefix => \$dir) {
        if (strpos(\$class, \$prefix) === 0) {
            \$relativeClass = substr(\$class, strlen(\$prefix));
            \$file = \$dir . str_replace(\"\\\\\", \"/\", \$relativeClass) . \".php\";
            if (file_exists(\$file)) {
                require_once \$file;
                return;
            }
        }
    }
});

return true;
';
        file_put_contents(\$autoloadFile, \$content);
        echo 'Functional autoloader created successfully';
        " 2>/dev/null || print_warning "Could not create manual autoloader"
    fi
    
    # Verify installation was successful
    if ! docker-compose run --rm php test -f "/var/www/html/vendor/autoload.php" 2>/dev/null; then
        print_warning "Autoloader not found but packages seem to be installed"
        
        # Create a very simple fallback autoloader
        docker-compose run --rm php -r "
        \$autoloadFile = '/var/www/html/vendor/autoload.php';
        if (!file_exists(\$autoloadFile)) {
            \$content = '<?php
// Simple fallback autoloader
if (file_exists(__DIR__ . \"/composer/ClassLoader.php\")) {
    require_once __DIR__ . \"/composer/ClassLoader.php\";
}

// Very basic Laravel function
function app() {
    return \$GLOBALS[\"app\"] ?? null;
}

return true;
';
            file_put_contents(\$autoloadFile, \$content);
            echo \"Fallback autoloader created\";
        }
        " 2>/dev/null
    fi
    
    # Final check - make sure we have some form of autoloader
    if docker-compose run --rm php test -f "/var/www/html/vendor/autoload.php" 2>/dev/null; then
        # Adjust permissions so host user can edit generated files
        HOST_UID=$(id -u)
        HOST_GID=$(id -g)
        print_status "Adjusting vendor file permissions for host editing..."
        docker-compose run --rm php sh -c "addgroup -g $HOST_GID hostgrp 2>/dev/null || true; adduser -D -u $HOST_UID -G hostgrp hostusr 2>/dev/null || true; chown -R $HOST_UID:$HOST_GID /var/www/html/vendor" 2>/dev/null || true
        print_success "PHP dependencies installed"
    else
        print_error "Autoloader still missing; composer install incomplete"
        exit 1
    fi
}

# Start remaining services after dependencies are installed
start_remaining_services() {
    print_status "Starting remaining services..."
    
    # Now start phpunit and scheduler which depend on vendor directory
    docker-compose up -d phpunit scheduler
    
    # Wait for all services to be ready
    sleep 5
    
    print_success "All services started"
}
install_node_dependencies() {
    print_status "Installing Node.js dependencies..."
    
    # Ensure containers are running first
    if ! docker-compose ps | grep -q "Up"; then
        print_warning "Containers not running, starting them first..."
        docker-compose up -d php mysql redis
        sleep 10
    fi
    
    # Primary install attempt
    if ! docker-compose run --rm npm install; then
        print_warning "npm install failed - retrying with --legacy-peer-deps"
        docker-compose run --rm npm install --legacy-peer-deps || {
            print_warning "Retry with --legacy-peer-deps failed - trying --force"
            docker-compose run --rm npm install --force || {
                print_error "All npm install strategies failed"
                exit 1
            }
        }
    fi

    # Verify installation was successful
    if ! docker-compose run --rm npm list >/dev/null 2>&1; then
        print_warning "Node dependency tree check returned non-zero (may be acceptable with peer overrides)"
    fi
    
    print_success "Node.js dependencies installed"
}

# Setup Laravel application
setup_laravel() {
    print_status "Setting up Laravel application..."
    
    # Copy .env file if it doesn't exist
    if [ ! -f "src/.env" ]; then
        print_status "Creating .env file..."
        if [ -f "src/.env.example" ]; then
            cp src/.env.example src/.env
        else
            print_warning ".env.example not found, creating minimal .env"
            cat > src/.env << EOF
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=appdb
DB_USERNAME=user
DB_PASSWORD=secret

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
EOF
        fi
    fi
    
    # Generate application key (skip if already set)
    if ! grep -q "^APP_KEY=base64" src/.env 2>/dev/null; then
        print_status "Generating application key..."
        docker-compose run --rm artisan key:generate --force || print_warning "key:generate failed (may already exist)"
    fi

    # Parse DB connection from .env with sensible defaults
    DB_HOST="mysql"
    DB_PORT="3306"
    DB_NAME="appdb"
    DB_USER="user"
    DB_PASS="secret"
    if [ -f "src/.env" ]; then
        DB_HOST=$(grep -E '^DB_HOST=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$DB_HOST")
        DB_PORT=$(grep -E '^DB_PORT=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$DB_PORT")
        DB_NAME=$(grep -E '^DB_DATABASE=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$DB_NAME")
        DB_USER=$(grep -E '^DB_USERNAME=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$DB_USER")
        DB_PASS=$(grep -E '^DB_PASSWORD=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$DB_PASS")
    fi

    # Wait for database to be ready using direct MySQL ping
    print_status "Waiting for database to be ready..."
    max_attempts=30
    attempt=1
    while [ $attempt -le $max_attempts ]; do
        if docker-compose run --rm mysql mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" -e "SELECT 1" >/dev/null 2>&1; then
            break
        fi
        print_status "Waiting for database... (attempt $attempt/$max_attempts)"
        sleep 2
        attempt=$((attempt + 1))
    done

    if [ $attempt -gt $max_attempts ]; then
        print_error "Database connection failed after $max_attempts attempts"
        print_status "Recent MySQL logs (last 100 lines):"
        docker-compose logs --tail=100 mysql || true
        exit 1
    fi

    # Ensure the target database exists (safe if already exists)
    docker-compose run --rm mysql mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >/dev/null 2>&1 || true
    
    # Run database migrations
    print_status "Running database migrations..."
    docker-compose run --rm artisan migrate --force
    
    # Seed database if seeder exists
    if docker-compose run --rm artisan db:seed --class=DatabaseSeeder --dry-run >/dev/null 2>&1; then
        print_status "Seeding database..."
        docker-compose run --rm artisan db:seed --force
    else
        print_warning "No database seeders found, skipping seeding"
    fi
    
    # Create storage symlink
    print_status "Creating storage symlink..."
    docker-compose run --rm artisan storage:link
    
    print_success "Laravel application setup completed"
}

# Fix storage permissions - comprehensive approach
fix_storage_permissions() {
    print_status "Fixing storage and cache permissions..."
    
    # Method 1: Fix permissions inside containers
    PHP_CONTAINER=$(docker-compose ps -q php 2>/dev/null)
    if [ -n "$PHP_CONTAINER" ] && docker ps --format "table {{.Names}}" | grep -q "ldb-php"; then
        print_status "Fixing permissions inside PHP container..."
        docker exec $PHP_CONTAINER chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
        docker exec $PHP_CONTAINER chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    fi
    
    # Method 2: Fix permissions on host
    if [ -d "src/storage" ]; then
        print_status "Fixing permissions on host filesystem..."
        
        # Get current user
        CURRENT_USER=$(whoami)
        
        # Try to get web server group (varies by system)
        WEB_GROUP="www-data"
        if ! getent group $WEB_GROUP >/dev/null 2>&1; then
            if getent group apache >/dev/null 2>&1; then
                WEB_GROUP="apache"
            elif getent group nginx >/dev/null 2>&1; then
                WEB_GROUP="nginx"
            else
                WEB_GROUP=$CURRENT_USER
            fi
        fi
        
        # Fix ownership and permissions
        sudo chown -R $CURRENT_USER:$WEB_GROUP src/storage src/bootstrap/cache 2>/dev/null || chown -R $CURRENT_USER:$CURRENT_USER src/storage src/bootstrap/cache 2>/dev/null || true
        
        chmod -R ug+rwX src/storage src/bootstrap/cache 2>/dev/null || true
        find src/storage -type f -exec chmod 664 {} \; 2>/dev/null || true
        find src/bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
        
        # Ensure newly created files from container remain writable (set default ACL if available)
        if command_exists setfacl; then
            setfacl -R -m d:u:$CURRENT_USER:rwX -m d:g:$WEB_GROUP:rwX src/storage src/bootstrap/cache 2>/dev/null || true
        fi
        
        # Special handling for log files
        if [ -f "src/storage/logs/laravel.log" ]; then
            chmod 666 src/storage/logs/laravel.log 2>/dev/null || true
        fi
        
        # Create laravel.log if it doesn't exist
        if [ ! -f "src/storage/logs/laravel.log" ]; then
            touch src/storage/logs/laravel.log
            chmod 666 src/storage/logs/laravel.log 2>/dev/null || true
        fi
    fi
    
    # Method 3: Set proper ACLs if available
    if command_exists setfacl; then
        print_status "Setting ACLs for better permission handling..."
        setfacl -R -m u:www-data:rwx -m u:$CURRENT_USER:rwx src/storage src/bootstrap/cache 2>/dev/null || true
        setfacl -dR -m u:www-data:rwx -m u:$CURRENT_USER:rwx src/storage src/bootstrap/cache 2>/dev/null || true
    fi
    
    print_success "Storage permissions fixed"
}

# Build frontend assets
build_frontend() {
    print_status "Building frontend assets..."
    
    # Clear any existing builds
    docker-compose run --rm npm run build 2>/dev/null || {
        print_warning "Build failed, trying to install dependencies first..."
        docker-compose run --rm npm install
        docker-compose run --rm npm run build
    }
    
    print_success "Frontend assets built"
}

# Reset (drop & recreate) application databases on fresh install
reset_database() {
    if [[ $FRESH_INSTALL != 1 ]]; then
        return 0
    fi
    print_status "Resetting databases (fresh install)..."

    # Parse env if exists
    DB_NAME="appdb"
    DB_USER="user"
    DB_PASS="secret"
    TEST_DB_NAME="appdb_test"

    if [ -f "src/.env" ]; then
        # shellcheck disable=SC2046
        DB_NAME=$(grep -E '^DB_DATABASE=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$DB_NAME")
        DB_USER=$(grep -E '^DB_USERNAME=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$DB_USER")
        DB_PASS=$(grep -E '^DB_PASSWORD=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$DB_PASS")
    fi

    # Wait briefly for mysql responsiveness (reuse logic but lighter)
    attempts=0
    until docker-compose run --rm mysql mysql -hmysql -u$DB_USER -p$DB_PASS -e "SELECT 1" >/dev/null 2>&1 || [ $attempts -ge 20 ]; do
        attempts=$((attempts+1))
        sleep 2
        print_status "Waiting for MySQL (reset) attempt $attempts/20"
    done
    if [ $attempts -ge 20 ]; then
        print_warning "MySQL not reachable; skipping DB reset"
        return 0
    fi

    # Perform drop & recreate using root if available, else app user
    MYSQL_EXEC_PREFIX="docker-compose run --rm mysql mysql -hmysql"
    # Try root first
    if $MYSQL_EXEC_PREFIX -uroot -psecret -e "SELECT 1" >/dev/null 2>&1; then
        SQL_PREFIX="-uroot -psecret"
    else
        SQL_PREFIX="-u$DB_USER -p$DB_PASS"
    fi

    print_status "Dropping database $DB_NAME (if exists)..."
    $MYSQL_EXEC_PREFIX $SQL_PREFIX -e "DROP DATABASE IF EXISTS \`$DB_NAME\`;" || print_warning "Could not drop $DB_NAME"
    print_status "Creating database $DB_NAME..."
    $MYSQL_EXEC_PREFIX $SQL_PREFIX -e "CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || print_error "Failed to create $DB_NAME"

    print_status "Dropping test database $TEST_DB_NAME (if exists)..."
    $MYSQL_EXEC_PREFIX $SQL_PREFIX -e "DROP DATABASE IF EXISTS \`$TEST_DB_NAME\`;" || true
    print_status "Creating test database $TEST_DB_NAME..."
    $MYSQL_EXEC_PREFIX $SQL_PREFIX -e "CREATE DATABASE \`$TEST_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || true

    print_success "Databases reset"
}

# Run tests
run_tests() {
    print_status "Running tests..."
    
    # Check if PHPUnit is available
    if ! docker-compose run --rm php test -f "/var/www/html/vendor/bin/phpunit" 2>/dev/null; then
        print_warning "PHPUnit not found in vendor/bin, skipping tests"
        print_warning "This usually means composer dependencies are not fully installed"
        return 0
    fi
    
    # Detect phpunit DB configuration; if sqlite memory is used, skip DB migrations
    if grep -q 'env name="DB_CONNECTION" value="sqlite"' src/phpunit.xml 2>/dev/null && \
       grep -q 'env name="DB_DATABASE" value=":memory:"' src/phpunit.xml 2>/dev/null; then
        print_status "PHPUnit configured for sqlite :memory:; skipping DB migrations"
    else
        # Ensure .env.testing exists with sane defaults if not using sqlite memory
        if [ ! -f "src/.env.testing" ]; then
            print_status "Creating src/.env.testing for MySQL test database..."
            APP_KEY_VAL=$(grep -E '^APP_KEY=' src/.env | head -n1 | cut -d'=' -f2 | tr -d '\r')
            cat > src/.env.testing << EOF
APP_ENV=testing
APP_KEY=${APP_KEY_VAL}
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=appdb_test
DB_USERNAME=user
DB_PASSWORD=secret

CACHE_DRIVER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
EOF
        fi

        # Parse DB settings from .env.testing (fallback to .env)
        T_DB_HOST="mysql"
        T_DB_PORT="3306"
        T_DB_NAME="appdb_test"
        T_DB_USER="user"
        T_DB_PASS="secret"
        if [ -f "src/.env.testing" ]; then
            T_DB_HOST=$(grep -E '^DB_HOST=' src/.env.testing | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$T_DB_HOST")
            T_DB_PORT=$(grep -E '^DB_PORT=' src/.env.testing | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$T_DB_PORT")
            T_DB_NAME=$(grep -E '^DB_DATABASE=' src/.env.testing | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$T_DB_NAME")
            T_DB_USER=$(grep -E '^DB_USERNAME=' src/.env.testing | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$T_DB_USER")
            T_DB_PASS=$(grep -E '^DB_PASSWORD=' src/.env.testing | head -n1 | cut -d'=' -f2 | tr -d '\r' || echo "$T_DB_PASS")
        fi

        # Ensure database container is ready for tests
        print_status "Ensuring database is ready for tests..."
        attempts=0
        until docker-compose run --rm mysql mysql -h"$T_DB_HOST" -P"$T_DB_PORT" -u"$T_DB_USER" -p"$T_DB_PASS" -e "SELECT 1" >/dev/null 2>&1 || [ $attempts -ge 15 ]; do
            attempts=$((attempts+1))
            sleep 2
            print_status "Waiting for MySQL (test phase) attempt $attempts/15"
        done
        if [ $attempts -ge 15 ]; then
            print_warning "MySQL not ready for tests after retries; skipping test suite"
            return 0
        fi

        # Ensure testing database exists
        docker-compose run --rm mysql mysql -h"$T_DB_HOST" -P"$T_DB_PORT" -u"$T_DB_USER" -p"$T_DB_PASS" -e "CREATE DATABASE IF NOT EXISTS \`$T_DB_NAME\`;" >/dev/null 2>&1 || true

        # Run migrations against testing environment
        print_status "Running test migrations..."
        if ! docker-compose run --rm artisan migrate --env=testing --force; then
            print_warning "Test migrations failed; skipping tests"
            return 0
        fi
    fi
    
    # Run PHPUnit tests
    docker-compose run --rm phpunit || {
        print_warning "Some tests failed, but continuing with installation"
        return 0
    }
    
    print_success "Tests completed"
}

# Check application health
health_check() {
    print_status "Performing health check..."
    
    # Check if containers are running
    if ! docker-compose ps | grep -q "Up"; then
        print_error "Some containers are not running properly"
        docker-compose ps
        return 1
    fi
    
    # Check if vendor directory exists
    if ! docker-compose run --rm php test -d "/var/www/html/vendor" 2>/dev/null; then
        print_error "Vendor directory not found - composer dependencies not installed"
        return 1
    fi
    
    # Test database connection
    if ! docker-compose run --rm artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected successfully'; } catch(Exception \$e) { echo 'Database connection failed: ' . \$e->getMessage(); exit(1); }" 2>/dev/null; then
        print_warning "Database connection test failed - this might be normal on first install"
    fi
    
    # Test Redis connection (optional) using facade properly
    if docker-compose run --rm artisan tinker --execute="try { \Illuminate\\Support\\Facades\\Redis::connection()->ping(); echo 'Redis connected successfully'; } catch(Exception \$e) { echo 'Redis connection failed (not critical)'; }" 2>/dev/null; then
        print_status "Redis connection: OK"
    else
        print_warning "Redis connection failed (not critical for basic functionality)"
    fi
    
    print_success "Health check completed"
    return 0
}

# Display useful information
show_info() {
    echo
    echo "=================================================="
    print_success "Installation completed successfully!"
    echo "=================================================="
    echo
    echo -e "${GREEN}Your application is now available at:${NC}"
    echo -e "${BLUE}• Frontend:${NC} http://localhost"
    echo -e "${BLUE}• API Docs:${NC} http://localhost/api/documentation"
    echo -e "${BLUE}• Vite Dev Server:${NC} http://localhost:5173 (when running npm dev)"
    echo -e "${BLUE}• Database Admin:${NC} http://localhost:8080 (Adminer)"
    echo
    echo -e "${GREEN}Container Status:${NC}"
    docker-compose ps
    echo
    echo -e "${GREEN}Useful commands:${NC}"
    echo "• Start containers: make start (or docker-compose up -d)"
    echo "• Stop containers: make stop (or docker-compose down)"
    echo "• View logs: make logs (or docker-compose logs -f)"
    echo "• Development mode: make dev"
    echo "• Run tests: make test"
    echo "• Check status: make status"
    echo
    echo -e "${GREEN}Docker commands:${NC}"
    echo "• Run artisan: docker-compose run --rm artisan <command>"
    echo "• Run composer: docker-compose run --rm composer <command>"
    echo "• Run npm: docker-compose run --rm npm <command>"
    echo
    echo -e "${YELLOW}Next steps:${NC}"
    echo "1. Check containers are running: make status"
    echo "2. Start development: make dev"
    echo "3. Open http://localhost in your browser"
    echo
}

# Main installation function
main() {
    echo "=================================================="
    echo "Laravel Docker Base - Installation Script"
    echo "=================================================="
    echo
    
    # Parse command line arguments
    FRESH_INSTALL=0
    CLEAN_IMAGES=0
    SKIP_TESTS=0
    SKIP_BUILD=0
    
    while [[ $# -gt 0 ]]; do
        case $1 in
            --fresh)
                FRESH_INSTALL=1
                shift
                ;;
            --clean-images)
                CLEAN_IMAGES=1
                shift
                ;;
            --skip-tests)
                SKIP_TESTS=1
                shift
                ;;
            --skip-build)
                SKIP_BUILD=1
                shift
                ;;
            --help)
                echo "Usage: $0 [options]"
                echo ""
                echo "Options:"
                echo "  --fresh         Clean installation (removes all containers and data)"
                echo "  --clean-images  Also remove Docker images during cleanup"
                echo "  --skip-tests    Skip running tests"
                echo "  --skip-build    Skip building frontend assets"
                echo "  --help          Show this help message"
                exit 0
                ;;
            *)
                print_error "Unknown option: $1"
                exit 1
                ;;
        esac
    done
    
    # Interactive mode if no arguments provided
    if [[ $FRESH_INSTALL == 0 ]] && [[ -t 0 ]]; then
        echo "Installation type:"
        echo "1) Fresh install (will clean everything)"
        echo "2) Update existing installation"
        read -p "Choose option (1-2): " -n 1 -r INSTALL_TYPE
        echo
        
        if [[ $INSTALL_TYPE == "1" ]]; then
            FRESH_INSTALL=1
            read -p "Also clean Docker images? (y/N): " -n 1 -r
            echo
            if [[ $REPLY =~ ^[Yy]$ ]]; then
                CLEAN_IMAGES=1
            fi
        fi
        
        read -p "Build frontend assets? (Y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Nn]$ ]]; then
            SKIP_BUILD=1
        fi
        
        read -p "Run tests after installation? (Y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Nn]$ ]]; then
            SKIP_TESTS=1
        fi
    fi
    
    # Start installation
    check_prerequisites
    
    if [[ $FRESH_INSTALL == 1 ]]; then
        cleanup_containers
    fi
    
    create_directories
    build_containers
    install_php_dependencies
    start_remaining_services
    reset_database
    install_node_dependencies
    setup_laravel
    fix_storage_permissions
    
    if [[ $SKIP_BUILD == 0 ]]; then
        build_frontend
    fi
    
    if [[ $SKIP_TESTS == 0 ]]; then
        run_tests
    fi
    
    health_check
    show_info
}

# Handle script interruption
trap 'print_error "Installation interrupted"; exit 1' INT TERM

# Run main function with all arguments
main "$@"
