# Docker Quick Start Guide

Get your VCMS application running in Docker in 5 minutes!

## Prerequisites

- Install [Docker Desktop](https://www.docker.com/products/docker-desktop)
- Install [Git](https://git-scm.com/)

## 🚀 Quick Start (5 minutes)

### Step 1: Clone/Navigate to Project
```bash
cd /path/to/VCMS-final
```

### Step 2: Start Docker Containers
```bash
docker-compose up -d
```

This starts:
- 🐘 PHP Application
- 🗄️ MySQL Database
- 💾 Redis Cache
- 🌐 Nginx Web Server (optional)

### Step 3: Install Dependencies
```bash
docker-compose exec app composer install
docker-compose exec app npm install && npm run build
```

### Step 4: Setup Database
```bash
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan key:generate
```

### Step 5: Access Application
Open your browser to: **http://localhost:8000**

✅ Done! Your app is running!

---

## Common Commands

```bash
# View all running containers
docker-compose ps

# View application logs
docker-compose logs -f app

# Access Laravel shell
docker-compose exec app php artisan tinker

# Stop all containers
docker-compose down

# Restart containers
docker-compose restart

# View database
# Use a database client connecting to localhost:3306
# Username: root / vcms_user
# Password: secret / secure_password
```

---

## Troubleshooting

### Port 8000 Already in Use
```bash
# Find what's using port 8000
lsof -i :8000

# Or change port in docker-compose.yml
# Change: "8000:8000" to "8001:8000"
docker-compose restart
```

### Database Connection Failed
```bash
# Check if MySQL is running
docker-compose ps mysql

# View MySQL logs
docker-compose logs mysql

# Restart MySQL
docker-compose restart mysql
```

### Can't Connect to Application
```bash
# Check if app container is running
docker-compose ps app

# View application logs
docker-compose logs app

# Rebuild containers
docker-compose up -d --build
```

---

## Environment Variables

Default values in `docker-compose.yml`:

```yaml
DB_HOST=mysql
DB_DATABASE=vcms
DB_USERNAME=vcms_user
DB_PASSWORD=secure_password
```

To change, edit `docker-compose.yml` before running `docker-compose up -d`

---

## Production Deployment

See **DOCKER_DEPLOYMENT_GUIDE.md** for:
- ✅ Using production Dockerfile
- ✅ Deploying to Railway
- ✅ Deploying to DigitalOcean
- ✅ Using Docker Hub
- ✅ Security best practices

---

## File Organization

```
VCMS-final/
├── Dockerfile              # Development image
├── Dockerfile.production   # Production image
├── docker-compose.yml      # Development containers
├── docker-compose.production.yml  # Production containers
├── docker/
│   ├── nginx.conf         # Web server config
│   └── ssl/               # SSL certificates
├── .dockerignore           # Files excluded from Docker build
└── docker-helper.sh        # Helper script for common tasks
```

---

## Next Steps

1. ✅ Get app running with Docker (done above)
2. 📖 Read: **DOCKER_DEPLOYMENT_GUIDE.md** for production
3. 🚀 Deploy to Railway, DigitalOcean, or Docker Hub
4. 📊 Monitor logs and performance

---

**For detailed information, see DOCKER_DEPLOYMENT_GUIDE.md**
