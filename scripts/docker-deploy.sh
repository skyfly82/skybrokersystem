#!/bin/bash

set -e

echo "🚀 Deploying SkyBrokerSystem v6..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
IMAGE_NAME="skybrokersystem"
VERSION="6.0.0"
REGISTRY="${DOCKER_REGISTRY:-}"

# Build new image
echo -e "${YELLOW}Building new image...${NC}"
docker build -t ${IMAGE_NAME}:${VERSION} .
docker tag ${IMAGE_NAME}:${VERSION} ${IMAGE_NAME}:latest

# Push to registry if configured
if [ ! -z "$REGISTRY" ]; then
    echo -e "${YELLOW}Pushing to registry...${NC}"
    docker tag ${IMAGE_NAME}:${VERSION} ${REGISTRY}/${IMAGE_NAME}:${VERSION}
    docker tag ${IMAGE_NAME}:${VERSION} ${REGISTRY}/${IMAGE_NAME}:latest
    docker push ${REGISTRY}/${IMAGE_NAME}:${VERSION}
    docker push ${REGISTRY}/${IMAGE_NAME}:latest
fi

# Update running containers
echo -e "${YELLOW}Updating containers...${NC}"
docker-compose pull
docker-compose up -d --force-recreate

# Run migrations
echo -e "${YELLOW}Running migrations...${NC}"
docker-compose exec app php artisan migrate --force

# Clear caches
echo -e "${YELLOW}Clearing caches...${NC}"
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan queue:restart

# Health check
echo -e "${YELLOW}Performing health check...${NC}"
sleep 10
if curl -f http://localhost/health; then
    echo -e "${GREEN}✅ Deployment successful!${NC}"
else
    echo -e "${RED}❌ Health check failed!${NC}"
    exit 1
fi