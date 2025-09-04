.PHONY: help build up down restart logs clean backup deploy setup migrate seed shell mysql redis status top

help: ## Show this help message
	@echo 'SkyBrokerSystem v6 - Docker Management'
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker containers
	docker-compose build

up: ## Start all containers
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

restart: ## Restart all containers
	docker-compose restart

logs: ## Show logs from all containers
	docker-compose logs -f

logs-app: ## Show app logs only
	docker-compose logs -f app

clean: ## Clean up Docker resources
	docker-compose down -v --remove-orphans
	docker system prune -f

backup: ## Create backup
	@if [ -f "scripts/docker-backup.sh" ]; then \
		chmod +x scripts/docker-backup.sh; \
		./scripts/docker-backup.sh; \
	else \
		echo "Backup script not found. Please create scripts/docker-backup.sh"; \
	fi

deploy: ## Deploy new version
	@if [ -f "scripts/docker-deploy.sh" ]; then \
		chmod +x scripts/docker-deploy.sh; \
		./scripts/docker-deploy.sh; \
	else \
		echo "Deploy script not found. Please create scripts/docker-deploy.sh"; \
	fi

setup: ## Initial setup
	@echo "🚀 Setting up SkyBrokerSystem v6..."
	@echo "Creating required directories..."
	@mkdir -p storage/{app,framework,logs}
	@mkdir -p storage/framework/{cache,sessions,views}
	@mkdir -p bootstrap/cache
	@mkdir -p public/uploads
	@mkdir -p logs
	@mkdir -p scripts
	@echo "Setting permissions..."
	@chmod -R 755 storage bootstrap/cache
	@if [ ! -f .env ]; then \
		echo "Creating .env file..."; \
		if [ -f .env.docker ]; then \
			cp .env.docker .env; \
		else \
			cp .env.example .env; \
		fi; \
		echo "✅ .env file created. Please update it with your configuration."; \
	else \
		echo "ℹ️  .env file already exists."; \
	fi
	@echo "Building containers..."
	@make build
	@echo "Starting containers..."
	@make up
	@echo "Waiting for services to be ready..."
	@sleep 30
	@echo "Running migrations..."
	@make migrate
	@echo "✅ Setup complete!"
	@echo "🌐 Application: http://localhost"
	@echo "📧 Mailpit: http://localhost:8025"

migrate: ## Run database migrations
	docker-compose exec -T app php artisan migrate --force

migrate-fresh: ## Fresh migrations with seed
	docker-compose exec -T app php artisan migrate:fresh --seed --force

seed: ## Run database seeders
	docker-compose exec -T app php artisan db:seed --force

shell: ## Access app container shell
	docker-compose exec app sh

shell-root: ## Access app container as root
	docker-compose exec --user root app sh

mysql: ## Access MySQL shell
	docker-compose exec mysql mysql -u root -p

mysql-user: ## Access MySQL as app user
	@echo "Use password from .env DB_PASSWORD"
	docker-compose exec mysql mysql -u $(shell grep DB_USERNAME .env | cut -d '=' -f2) -p $(shell grep DB_DATABASE .env | cut -d '=' -f2)

redis: ## Access Redis CLI
	docker-compose exec redis redis-cli

status: ## Show container status
	docker-compose ps

top: ## Show running processes in containers
	docker-compose top

install: ## Install PHP dependencies
	docker-compose exec app composer install

install-prod: ## Install production dependencies
	docker-compose exec app composer install --no-dev --optimize-autoloader

npm-install: ## Install Node dependencies
	docker-compose exec app npm install

npm-build: ## Build frontend assets
	docker-compose exec app npm run build

npm-dev: ## Build frontend assets for development
	docker-compose exec app npm run dev

clear-cache: ## Clear all caches
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
	docker-compose exec app php artisan cache:clear

optimize: ## Optimize application for production
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache
	docker-compose exec app composer dump-autoload --optimize

queue-work: ## Start queue worker manually
	docker-compose exec app php artisan queue:work

queue-restart: ## Restart queue workers
	docker-compose exec app php artisan queue:restart

test: ## Run tests
	docker-compose exec app php artisan test

test-coverage: ## Run tests with coverage
	docker-compose exec app php artisan test --coverage

check-health: ## Check application health
	@echo "Checking application health..."
	@if curl -s -f http://localhost/health >/dev/null 2>&1; then \
		echo "✅ Application is healthy"; \
	else \
		echo "❌ Application health check failed"; \
	fi

dev: ## Start development environment
	@echo "Starting development environment..."
	@docker-compose --profile development up -d
	@echo "Development tools available:"
	@echo "📊 PHPMyAdmin: http://localhost:8081"
	@echo "🔴 Redis Commander: http://localhost:8082"

prod: ## Start production environment
	@echo "Starting production environment..."
	@docker-compose up -d app mysql redis queue scheduler mailpit

stop-dev: ## Stop development tools
	docker-compose --profile development down

monitor: ## Show real-time container stats
	docker stats $(shell docker-compose ps -q)

update: ## Update to latest version
	@echo "Updating SkyBrokerSystem..."
	@git pull origin main
	@make build
	@make down
	@make up
	@make migrate
	@make optimize
	@echo "✅ Update complete!"

reset: ## Reset everything (WARNING: destroys data)
	@echo "⚠️  This will destroy ALL data. Are you sure? [y/N]" && read ans && [ $${ans:-N} = y ]
	@make clean
	@docker volume prune -f
	@make setup

backup-db: ## Backup database only
	@echo "Creating database backup..."
	@mkdir -p backups
	@docker-compose exec mysql mysqldump -u root -p$(shell grep DB_ROOT_PASSWORD .env | cut -d '=' -f2) --all-databases > backups/db_backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "✅ Database backup created in backups/ directory"

restore-db: ## Restore database from backup (provide BACKUP_FILE)
	@if [ -z "$(BACKUP_FILE)" ]; then \
		echo "Usage: make restore-db BACKUP_FILE=path/to/backup.sql"; \
		exit 1; \
	fi
	@echo "Restoring database from $(BACKUP_FILE)..."
	@docker-compose exec -T mysql mysql -u root -p$(shell grep DB_ROOT_PASSWORD .env | cut -d '=' -f2) < $(BACKUP_FILE)
	@echo "✅ Database restored"

logs-error: ## Show error logs only
	docker-compose logs app | grep -i error

follow-logs: ## Follow specific service logs (provide SERVICE)
	@if [ -z "$(SERVICE)" ]; then \
		echo "Usage: make follow-logs SERVICE=app|mysql|redis|queue"; \
		exit 1; \
	fi
	docker-compose logs -f $(SERVICE)

ip: ## Show container IP addresses
	@echo "Container IP addresses:"
	@docker-compose ps -q | xargs docker inspect -f '{{.Name}} - {{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}'