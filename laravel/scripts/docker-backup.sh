#!/bin/bash

set -e

echo "📦 Creating backup..."

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="skybrokersystem_backup_${DATE}"

# Create backup directory
mkdir -p ${BACKUP_DIR}

echo -e "${YELLOW}Creating database backup...${NC}"
docker-compose exec mysql mysqldump -u root -p${DB_ROOT_PASSWORD} --all-databases > ${BACKUP_DIR}/${BACKUP_NAME}_database.sql

echo -e "${YELLOW}Creating files backup...${NC}"
tar -czf ${BACKUP_DIR}/${BACKUP_NAME}_files.tar.gz storage/ public/uploads/

echo -e "${YELLOW}Creating configuration backup...${NC}"
cp .env ${BACKUP_DIR}/${BACKUP_NAME}_env.txt

# Upload to S3 if configured
if [ ! -z "$S3_BACKUP_BUCKET" ]; then
    echo -e "${YELLOW}Uploading to S3...${NC}"
    aws s3 cp ${BACKUP_DIR}/${BACKUP_NAME}_database.sql s3://${S3_BACKUP_BUCKET}/
    aws s3 cp ${BACKUP_DIR}/${BACKUP_NAME}_files.tar.gz s3://${S3_BACKUP_BUCKET}/
    aws s3 cp ${BACKUP_DIR}/${BACKUP_NAME}_env.txt s3://${S3_BACKUP_BUCKET}/
fi

echo -e "${GREEN}✅ Backup created: ${BACKUP_NAME}${NC}"

# Makefile
.PHONY: help build up down restart logs clean backup deploy

help: ## Show this help message
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

logs: ## Show logs
	docker-compose logs -f

clean: ## Clean up Docker resources
	docker-compose down -v
	docker system prune -f

backup: ## Create backup
	./scripts/docker-backup.sh

deploy: ## Deploy new version
	./scripts/docker-deploy.sh

setup: ## Initial setup
	./scripts/docker-setup.sh

migrate: ## Run migrations
	docker-compose exec app php artisan migrate

seed: ## Run seeders
	docker-compose exec app php artisan db:seed

shell: ## Access app container shell
	docker-compose exec app sh

mysql: ## Access MySQL shell
	docker-compose exec mysql mysql -u ${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}

redis: ## Access Redis CLI
	docker-compose exec redis redis-cli -a ${REDIS_PASSWORD}

status: ## Show container status
	docker-compose ps

top: ## Show running processes
	docker-compose top