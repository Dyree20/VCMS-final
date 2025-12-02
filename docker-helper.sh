#!/bin/bash

# Docker deployment helper script
# Automates common Docker operations

set -e

echo "======================================"
echo "VCMS Docker Deployment Helper"
echo "======================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Functions
show_menu() {
    echo "1. Start local development containers"
    echo "2. Stop containers"
    echo "3. View logs"
    echo "4. Run migrations"
    echo "5. Build production image"
    echo "6. Push to Docker Hub"
    echo "7. Start production containers"
    echo "8. Stop production containers"
    echo "9. Database backup"
    echo "10. Container shell access"
    echo "11. Exit"
    echo ""
}

# Check Docker installation
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

echo -e "${GREEN}Docker version: $(docker --version)${NC}"
echo ""

while true; do
    show_menu
    read -p "Select option: " choice

    case $choice in
        1)
            echo -e "${YELLOW}Starting development containers...${NC}"
            docker-compose up -d
            echo -e "${GREEN}✓ Containers started${NC}"
            echo "Access application at: http://localhost:8000"
            ;;
        2)
            echo -e "${YELLOW}Stopping containers...${NC}"
            docker-compose down
            echo -e "${GREEN}✓ Containers stopped${NC}"
            ;;
        3)
            read -p "Container name (default: app): " container
            container=${container:-app}
            docker-compose logs -f $container
            ;;
        4)
            echo -e "${YELLOW}Running migrations...${NC}"
            docker-compose exec app php artisan migrate --force
            echo -e "${GREEN}✓ Migrations complete${NC}"
            ;;
        5)
            read -p "Image name (default: vcms:latest): " image_name
            image_name=${image_name:-vcms:latest}
            echo -e "${YELLOW}Building production image: $image_name${NC}"
            docker build -f Dockerfile.production -t $image_name .
            echo -e "${GREEN}✓ Image built${NC}"
            ;;
        6)
            read -p "Docker Hub username: " username
            read -p "Image name: " image_name
            full_name="$username/$image_name:latest"
            echo -e "${YELLOW}Tagging image as $full_name${NC}"
            docker tag vcms:latest $full_name
            echo -e "${YELLOW}Pushing to Docker Hub...${NC}"
            docker push $full_name
            echo -e "${GREEN}✓ Pushed successfully${NC}"
            ;;
        7)
            echo -e "${YELLOW}Starting production containers...${NC}"
            docker-compose -f docker-compose.production.yml up -d
            echo -e "${GREEN}✓ Production containers started${NC}"
            ;;
        8)
            echo -e "${YELLOW}Stopping production containers...${NC}"
            docker-compose -f docker-compose.production.yml down
            echo -e "${GREEN}✓ Production containers stopped${NC}"
            ;;
        9)
            echo -e "${YELLOW}Backing up database...${NC}"
            timestamp=$(date +%Y%m%d_%H%M%S)
            backup_file="backup_$timestamp.sql"
            docker-compose exec -T mysql mysqldump -u root -p$DB_ROOT_PASSWORD vcms > $backup_file
            echo -e "${GREEN}✓ Backup saved to: $backup_file${NC}"
            ;;
        10)
            read -p "Container name (default: app): " container
            container=${container:-app}
            echo -e "${YELLOW}Accessing $container container shell...${NC}"
            docker-compose exec $container sh
            ;;
        11)
            echo -e "${GREEN}Goodbye!${NC}"
            exit 0
            ;;
        *)
            echo -e "${RED}Invalid option. Please try again.${NC}"
            ;;
    esac
    
    echo ""
    read -p "Press Enter to continue..."
    echo ""
done
