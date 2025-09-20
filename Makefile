.PHONY: help install fresh start status logs clean test dev build deploy migrate seed

fresh: ## Fresh install - reset everything and start clean
	@echo "${YELLOW}Fresh install - This will delete containers, volumes, and reset storage...${NC}"
	@printf "Are you sure you want to continue? [y/N] "; \
	read -r REPLY; \
	if [ "$$REPLY" != "y" ] && [ "$$REPLY" != "Y" ]; then \
		echo "${RED}Fresh install cancelled${NC}"; \
		exit 1; \
	fi
	@echo "${BLUE}Starting fresh install...${NC}"
	@bash install.sh --fresh
	@echo "${GREEN}Fresh install completed! 🚀${NC}"
	@echo "${CYAN}Your development environment is ready:${NC}"
	@echo "  Application: ${YELLOW}http://localhost:8000${NC}"
	@echo "  Database:    ${YELLOW}http://localhost:8080${NC}"

# Default target
.DEFAULT_GOAL := help

# Colors
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)
RED    := $(shell tput -Txterm setaf 1)
BLUE   := $(shell tput -Txterm setaf 4)
CYAN   := $(shell tput -Txterm setaf 6)
NC     := $(shell tput -Txterm sgr0)

# Help target
help: ## Show this help message
	@echo ''
	@echo 'Usage:'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<target>${RESET}'
	@echo ''
	@echo 'Laravel Docker Base - Available Commands:'
	@echo ''
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-15s${RESET} %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Installation targets
install: ## Run fresh installation script
	@echo "${GREEN}Starting installation...${RESET}"
	@chmod +x install.sh
	@./install.sh
	@echo "${CYAN}Your development environment is ready:${NC}"
	@echo "  Application: ${YELLOW}http://localhost:8000${NC}"
	@echo "  Database:    ${YELLOW}http://localhost:8080${NC}"

fresh-safe: ## Clean install without tests (safer)
	@echo "${GREEN}Starting safe clean installation...${RESET}"
	@chmod +x install.sh
	@./install.sh --fresh --skip-tests

quick: ## Quick setup (skip tests and frontend build)
	@echo "${GREEN}Starting quick setup...${RESET}"
	@chmod +x install.sh
	@./install.sh --skip-tests --skip-build

# Container management
start: ## Start all containers with Vite watch mode
	@echo "${GREEN}Starting containers with Vite development server...${RESET}"
	@docker-compose up -d php mysql nginx redis
	@echo "${GREEN}Starting Vite development server with watch mode...${RESET}"
	@docker-compose --profile dev up npm-dev

start-bg: ## Start all containers with Vite watch mode in background
	@echo "${GREEN}Starting containers with Vite development server in background...${RESET}"
	@docker-compose --profile dev up -d

start-basic: ## Start all containers without Vite watch mode
	@echo "${GREEN}Starting containers only (no Vite watch)...${RESET}"
	@docker-compose up -d

stop: ## Stop all containers
	@echo "${GREEN}Stopping containers...${RESET}"
	@docker-compose down

restart: ## Restart all containers
	@echo "${GREEN}Restarting containers...${RESET}"
	@docker-compose restart

kill: ## Force stop all containers
	@echo "${GREEN}Force stopping containers...${RESET}"
	@docker-compose kill

# Container information
status: ## Show container status
	@echo "${GREEN}Container Status:${RESET}"
	@docker-compose ps

logs: ## Show container logs
	@docker-compose logs -f

logs-php: ## Show PHP container logs
	@docker-compose logs -f php

logs-nginx: ## Show Nginx container logs
	@docker-compose logs -f nginx

logs-mysql: ## Show MySQL container logs
	@docker-compose logs -f mysql

# Development commands
dev: ## Start development mode (containers + npm dev)
	@echo "${GREEN}Starting development environment...${RESET}"
	@docker-compose up -d php mysql nginx redis
	@echo "${GREEN}Starting Vite development server...${RESET}"
	@docker-compose --profile dev up npm-dev

dev-bg: ## Start development mode in background
	@echo "${GREEN}Starting development environment in background...${RESET}"
	@docker-compose --profile dev up -d

build: ## Build frontend assets
	@echo "${GREEN}Building frontend assets...${RESET}"
	@docker-compose run --rm npm run build

watch: ## Watch for frontend changes
	@echo "${GREEN}Starting file watcher...${RESET}"
	@docker-compose run --rm npm run dev

# Database commands
migrate: ## Run database migrations
	@echo "${GREEN}Running migrations...${RESET}"
	@docker-compose run --rm artisan migrate

migrate-fresh: ## Fresh migration (drop all tables)
	@echo "${GREEN}Running fresh migrations...${RESET}"
	@docker-compose run --rm artisan migrate:fresh

migrate-rollback: ## Rollback last migration
	@echo "${GREEN}Rolling back migrations...${RESET}"
	@docker-compose run --rm artisan migrate:rollback

seed: ## Run database seeders
	@echo "${GREEN}Running database seeders...${RESET}"
	@docker-compose run --rm artisan db:seed

migrate-seed: ## Run migrations and seeders
	@echo "${GREEN}Running migrations and seeders...${RESET}"
	@docker-compose run --rm artisan migrate --seed

# Create migration (usage: make migration name=create_users_table)
migration: ## Create new migration (use: make migration name=create_users_table)
ifeq ($(name),)
	@echo "${YELLOW}Usage: make migration name=create_users_table${RESET}"
else
	@echo "${GREEN}Creating migration: $(name)${RESET}"
	@docker-compose run --rm artisan make:migration $(name)
endif

# Testing
test: ## Run all tests
	@echo "${GREEN}Running tests...${RESET}"
	@docker-compose run --rm phpunit

test-unit: ## Run unit tests only
	@echo "${GREEN}Running unit tests...${RESET}"
	@docker-compose run --rm phpunit --testsuite=Unit

test-feature: ## Run feature tests only
	@echo "${GREEN}Running feature tests...${RESET}"
	@docker-compose run --rm phpunit --testsuite=Feature

test-coverage: ## Run tests with coverage report
	@echo "${GREEN}Running tests with coverage...${RESET}"
	@docker-compose run --rm phpunit --coverage-html coverage

# Laravel Artisan commands
artisan: ## Run artisan command (use: make artisan cmd="make:controller UserController")
ifeq ($(cmd),)
	@echo "${YELLOW}Usage: make artisan cmd='make:controller UserController'${RESET}"
else
	@docker-compose run --rm artisan $(cmd)
endif

tinker: ## Open Laravel Tinker
	@echo "${GREEN}Opening Laravel Tinker...${RESET}"
	@docker-compose run --rm artisan tinker

queue: ## Start queue worker
	@echo "${GREEN}Starting queue worker...${RESET}"
	@docker-compose run --rm artisan queue:work

schedule: ## Start task scheduler
	@echo "${GREEN}Starting scheduler...${RESET}"
	@docker-compose run --rm scheduler

# Composer commands
composer: ## Run composer command (use: make composer cmd="require package/name")
ifeq ($(cmd),)
	@echo "${YELLOW}Usage: make composer cmd='require package/name'${RESET}"
else
	@docker-compose run --rm composer $(cmd)
endif

composer-install: ## Install PHP dependencies
	@echo "${GREEN}Installing PHP dependencies...${RESET}"
	@docker-compose run --rm composer install --optimize-autoloader

composer-install-prod: ## Install PHP dependencies for production
	@echo "${GREEN}Installing PHP dependencies for production...${RESET}"
	@docker-compose run --rm composer install --no-dev --optimize-autoloader --no-interaction

composer-update: ## Update PHP dependencies
	@echo "${GREEN}Updating PHP dependencies...${RESET}"
	@docker-compose run --rm composer update

composer-dump: ## Dump autoload
	@echo "${GREEN}Dumping autoload...${RESET}"
	@docker-compose run --rm composer dump-autoload

# NPM commands
npm: ## Run npm command (use: make npm cmd="install package")
ifeq ($(cmd),)
	@echo "${YELLOW}Usage: make npm cmd='install package'${RESET}"
else
	@docker-compose run --rm npm $(cmd)
endif

npm-install: ## Install Node dependencies
	@echo "${GREEN}Installing Node dependencies...${RESET}"
	@docker-compose run --rm npm install

npm-update: ## Update Node dependencies
	@echo "${GREEN}Updating Node dependencies...${RESET}"
	@docker-compose run --rm npm update

npm-audit: ## Run npm security audit
	@echo "${GREEN}Running npm audit...${RESET}"
	@docker-compose run --rm npm audit

# Cleanup commands
clean: ## Clean up containers and images
	@echo "${GREEN}Cleaning up containers and images...${RESET}"
	@docker-compose down -v
	@docker system prune -f

clean-all: ## Clean up everything including volumes
	@echo "${GREEN}Cleaning up everything...${RESET}"
	@docker-compose down -v
	@docker system prune -af
	@docker volume prune -f

clean-logs: ## Clear Laravel logs
	@echo "${GREEN}Clearing Laravel logs...${RESET}"
	@docker-compose run --rm artisan log:clear 2>/dev/null || echo "Log clear command not available, manually clearing..."
	@truncate -s 0 src/storage/logs/laravel.log 2>/dev/null || echo "Log file not found or cannot be cleared"

fix-composer: ## Fix composer autoload issues
	@echo "${GREEN}Fixing composer autoload issues...${RESET}"
	@docker-compose run --rm composer install --optimize-autoloader
	@docker-compose run --rm composer dump-autoload

# Permissions
fix-permissions: ## Fix storage permissions
	@echo "${GREEN}Fixing storage permissions...${RESET}"
	@sudo chown -R $(USER):www-data src/storage src/bootstrap/cache
	@chmod -R 775 src/storage src/bootstrap/cache
	@find src/storage -type f -exec chmod 664 {} \;

# Backup and restore
backup-db: ## Backup database
	@echo "${GREEN}Creating database backup...${RESET}"
	@docker-compose exec mysql mysqldump -u root -psecret appdb > backup_$(shell date +%Y%m%d_%H%M%S).sql

restore-db: ## Restore database (use: make restore-db file=backup.sql)
ifeq ($(file),)
	@echo "${YELLOW}Usage: make restore-db file=backup.sql${RESET}"
else
	@echo "${GREEN}Restoring database from $(file)...${RESET}"
	@docker-compose exec -T mysql mysql -u root -psecret appdb < $(file)
endif

# Production deployment (basic)
deploy-build: ## Build for production
	@echo "${GREEN}Building for production...${RESET}"
	@docker-compose run --rm composer install --no-dev --optimize-autoloader --no-interaction
	@docker-compose run --rm npm run production
	@docker-compose run --rm artisan config:cache
	@docker-compose run --rm artisan route:cache
	@docker-compose run --rm artisan view:cache

deploy-clear: ## Clear production caches
	@echo "${GREEN}Clearing caches...${RESET}"
	@docker-compose run --rm artisan config:clear
	@docker-compose run --rm artisan route:clear
	@docker-compose run --rm artisan view:clear
	@docker-compose run --rm artisan cache:clear

# SSL/HTTPS (for production)
ssl-renew: ## Renew SSL certificates
	@echo "${GREEN}Renewing SSL certificates...${RESET}"
	@docker-compose -f docker-compose.prod.yml run --rm certbot renew

# Legacy commands for backward compatibility
up: start ## Alias for start
up-build: ## Start with build
	@echo "${GREEN}Starting containers with build...${RESET}"
	@docker-compose up --build

up-d: start ## Alias for start
up-d-build: ## Start in background with build
	@echo "${GREEN}Starting containers in background with build...${RESET}"
	@docker-compose up -d --build
