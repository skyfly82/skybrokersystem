#!/bin/bash

set -e

echo "🚀 Setting up SkyBrokerSystem v6 with Docker..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Docker Compose is not installed. Please install Docker Compose first.${NC}"
    exit 1
fi

# Create required directories
echo -e "${YELLOW}Creating required directories...${NC}"
mkdir -p storage/{app,framework,logs}
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p bootstrap/cache
mkdir -p public/uploads
mkdir -p logs

# Copy environment file
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file...${NC}"
    cp .env.docker .env
    echo -e "${GREEN}.env file created. Please update it with your configuration.${NC}"
else
    echo -e "${YELLOW}.env file already exists.${NC}"
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo -e "${YELLOW}Generating application key...${NC}"
    docker run --rm -v $(pwd):/app -w /app php:8.3-cli php artisan key:generate
fi

# Set permissions
echo -e "${YELLOW}Setting permissions...${NC}"
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache public/uploads

# Build and start containers
echo -e "${YELLOW}Building Docker containers...${NC}"
docker-compose build

echo -e "${YELLOW}Starting Docker containers...${NC}"
docker-compose up -d

# Wait for database to be ready
echo -e "${YELLOW}Waiting for database to be ready...${NC}"
sleep 30

# Run migrations
echo -e "${YELLOW}Running database migrations...${NC}"
docker-compose exec app php artisan migrate --force

# Run seeders
echo -e "${YELLOW}Running database seeders...${NC}"
docker-compose exec app php artisan db:seed --force

# Clear caches
echo -e "${YELLOW}Clearing caches...${NC}"
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Create storage link
echo -e "${YELLOW}Creating storage link...${NC}"
docker-compose exec app php artisan storage:link

echo -e "${GREEN}✅ Setup complete!${NC}"
echo -e "${GREEN}Application is running at: http://localhost${NC}"
echo -e "${GREEN}Mailpit interface: http://localhost:8025${NC}"
echo -e "${GREEN}PHPMyAdmin: http://localhost:8081${NC}"
echo -e "${GREEN}Redis Commander: http://localhost:8082${NC}"