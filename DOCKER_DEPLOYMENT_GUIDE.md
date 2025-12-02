# Docker Deployment Guide for VCMS

Complete guide for deploying VCMS using Docker to various platforms.

## Table of Contents
1. [What is Docker?](#what-is-docker)
2. [Local Development with Docker](#local-development-with-docker)
3. [Production Docker Setup](#production-docker-setup)
4. [Deploying to Cloud Platforms](#deploying-to-cloud-platforms)
5. [Docker Best Practices](#docker-best-practices)
6. [Troubleshooting](#troubleshooting)

---

## What is Docker?

Docker packages your entire application including:
- Operating System
- PHP version
- All dependencies
- Database
- Web server

This ensures consistency across development, testing, and production.

### Benefits
- ✅ Same environment everywhere
- ✅ Easy scaling
- ✅ Simple deployment
- ✅ Version control
- ✅ Rollback capability

### Files Included

| File | Purpose |
|------|---------|
| `Dockerfile` | Development container |
| `Dockerfile.production` | Optimized production container |
| `docker-compose.yml` | Local development setup |
| `docker-compose.production.yml` | Production setup |
| `docker/nginx.conf` | Web server configuration |
| `Dockerfile.railway` | Railway.app deployment |
| `vercel.json` | Vercel configuration |
| `railway.toml` | Railway configuration |

---

## Local Development with Docker

### Prerequisites

1. **Install Docker Desktop**
   - Windows: https://www.docker.com/products/docker-desktop
   - Mac: https://www.docker.com/products/docker-desktop
   - Linux: Follow official Docker installation

2. **Verify Installation**
   ```bash
   docker --version
   docker-compose --version
   ```

### Quick Start

```bash
# 1. Navigate to project directory
cd /path/to/VCMS-final

# 2. Build and start containers
docker-compose up -d

# 3. Run migrations
docker-compose exec app php artisan migrate --seed

# 4. Install dependencies (if not already done in container)
docker-compose exec app composer install
docker-compose exec app npm install && npm run build

# 5. Access application
# Open browser to: http://localhost:8000
```

### Available Services

- **App**: `http://localhost:8000` - Laravel application
- **MySQL**: `localhost:3306` - Database
- **Redis**: `localhost:6379` - Cache/Session store
- **Nginx**: `http://localhost:80` - Web server (if using nginx)

### Docker Commands Reference

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f app

# Execute command in container
docker-compose exec app php artisan command-name

# Build fresh containers
docker-compose up -d --build

# Remove volumes (clears database)
docker-compose down -v

# Access container shell
docker-compose exec app sh
```

---

## Production Docker Setup

### Using Production Compose File

```bash
# 1. Copy production environment variables
cp .env.production .env

# 2. Update database credentials in .env
nano .env

# 3. Start production containers
docker-compose -f docker-compose.production.yml up -d

# 4. Run migrations
docker-compose -f docker-compose.production.yml exec app php artisan migrate --force

# 5. Cache configuration
docker-compose -f docker-compose.production.yml exec app php artisan config:cache
```

### Production Environment Variables

Create `.env` with these critical values:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-app-key
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=vcms
DB_USERNAME=vcms_user
DB_PASSWORD=secure_strong_password
DB_ROOT_PASSWORD=root_secure_password

# Cache/Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_PASSWORD=redis_secure_password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=support@yourdomain.com

# PayMongo
PAYMONGO_PUBLIC_KEY=pk_live_xxx
PAYMONGO_SECRET_KEY=sk_live_xxx
```

### Monitoring Production Containers

```bash
# View running containers
docker ps

# Check container health
docker ps --format "table {{.Names}}\t{{.Status}}"

# View container logs
docker logs container_name -f

# Check resource usage
docker stats

# Enter container shell for debugging
docker exec -it vcms_app_prod sh
```

### Backup Database in Docker

```bash
# Backup MySQL database
docker exec vcms_mysql_prod mysqldump -u vcms_user -p vcms > backup.sql

# Restore database
docker exec -i vcms_mysql_prod mysql -u vcms_user -p vcms < backup.sql
```

---

## Deploying to Cloud Platforms

### Vercel

⚠️ **Note:** Vercel is primarily for frontend/serverless functions. Not ideal for Laravel.

**If you must use Vercel:**

1. **Create Vercel Account**
   - Sign up at https://vercel.com

2. **Configuration**
   - File: `vercel.json` (already created)
   - This requires serverless PHP functions

3. **Deploy**
   ```bash
   npm i -g vercel
   vercel login
   vercel
   ```

**Limitations:**
- No persistent file storage
- Stateless functions only
- Complex Laravel features won't work
- Database must be external
- Sessions won't work properly

**Recommendation:** Use Railway or DigitalOcean instead for Laravel

---

### Railway.app

**Best Option:** Railway supports full Docker deployments perfectly!

#### Setup Steps

1. **Create Railway Account**
   - Sign up at https://railway.app
   - Connect GitHub account

2. **Create New Project**
   - "Create New Project" → "Deploy from GitHub"
   - Select VCMS-final repository
   - Add MySQL plugin from Railway dashboard

3. **Configure Environment Variables**
   - Go to Project Settings → Variables
   - Add all variables from `.env.production`:
     ```
     APP_ENV=production
     APP_DEBUG=false
     APP_KEY=base64:xxx
     APP_URL=https://your-railway-domain.up.railway.app
     DB_HOST=${{ Mysql.MYSQL_HOSTNAME }}
     DB_PORT=${{ Mysql.MYSQL_PORT }}
     DB_DATABASE=${{ Mysql.MYSQL_DB }}
     DB_USERNAME=${{ Mysql.MYSQL_USER }}
     DB_PASSWORD=${{ Mysql.MYSQL_PASSWORD }}
     MAIL_MAILER=smtp
     MAIL_HOST=...
     ```

4. **Deploy**
   - Push to GitHub
   - Railway auto-deploys from `main` or `master` branch
   - Or deploy manually from dashboard

5. **Run Migrations**
   ```bash
   # Via Railway CLI
   railway run php artisan migrate --force
   ```

#### Railway Benefits
- ✅ Full Docker support
- ✅ Easy GitHub integration
- ✅ Free tier available
- ✅ Good performance
- ✅ Simple database management
- ✅ Automatic deployments

---

### DigitalOcean App Platform

#### Setup Steps

1. **Create DigitalOcean Account**
   - Sign up at https://www.digitalocean.com
   - Create new App

2. **Connect GitHub**
   - Select VCMS-final repository
   - Allow DigitalOcean to access your repo

3. **Create Components**
   - **Service:** Web (main app)
   - **Database:** MySQL managed database

4. **Configure Service**
   - **Port:** 8000
   - **Build command:** `composer install --no-dev && npm run build`
   - **Run command:** `php artisan migrate --force && php artisan serve --host=0.0.0.0`

5. **Set Environment Variables**
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:xxx
   APP_URL=https://your-app.ondigitalocean.app
   DB_HOST=$DB_HOST
   DB_PORT=$DB_PORT
   DB_DATABASE=$DB_NAME
   DB_USERNAME=$DB_USER
   DB_PASSWORD=$DB_PASSWORD
   ```

6. **Deploy**
   - Click "Deploy"
   - Subsequent pushes to GitHub auto-deploy

#### DigitalOcean Benefits
- ✅ Professional hosting
- ✅ Good documentation
- ✅ Excellent support
- ✅ Scalable infrastructure
- ✅ Database backups included

---

### Docker Hub & Self-Hosted

#### Push to Docker Hub

```bash
# 1. Create Docker Hub account at https://hub.docker.com

# 2. Login locally
docker login

# 3. Build image
docker build -t yourusername/vcms:latest .

# 4. Push to Docker Hub
docker push yourusername/vcms:latest

# 5. Pull and run on server
docker run -d \
  -e APP_ENV=production \
  -e DB_HOST=db.example.com \
  -p 8000:8000 \
  yourusername/vcms:latest
```

#### Deploy to VPS (DigitalOcean, Linode, etc.)

```bash
# On your VPS server

# 1. Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# 2. Create project directory
mkdir -p /apps/vcms
cd /apps/vcms

# 3. Copy docker-compose.production.yml
# (Upload via SCP or Git clone)

# 4. Create .env file
nano .env

# 5. Start containers
docker-compose -f docker-compose.production.yml up -d

# 6. Run migrations
docker-compose exec app php artisan migrate --force
```

---

## Docker Best Practices

### Security

1. **Use .env for Secrets**
   ```bash
   # Never hardcode passwords
   DB_PASSWORD=${DB_PASSWORD}
   ```

2. **Don't Run as Root**
   ```dockerfile
   # In Dockerfile.production - already done
   USER www-data
   ```

3. **Use HTTPS**
   ```bash
   # Always use https:// in APP_URL
   APP_URL=https://yourdomain.com
   ```

4. **Scan Images for Vulnerabilities**
   ```bash
   docker scan yourusername/vcms:latest
   ```

### Performance

1. **Multi-stage Build**
   - Reduces final image size
   - Removes build tools from production
   - See: `Dockerfile.production`

2. **Layer Caching**
   ```dockerfile
   # Order matters - frequently changing last
   FROM base
   COPY composer.json .
   RUN composer install
   COPY . .  # This changes frequently
   ```

3. **Alpine Linux**
   - Smaller base image
   - Used in production Dockerfile

### Maintenance

1. **Regular Updates**
   ```bash
   docker image prune -a
   docker system prune --volumes
   ```

2. **Log Rotation**
   ```bash
   # Configure in docker-compose
   logging:
     driver: "json-file"
     options:
       max-size: "10m"
       max-file: "3"
   ```

3. **Health Checks**
   ```dockerfile
   HEALTHCHECK --interval=30s --timeout=10s \
     CMD curl -f http://localhost:9000 || exit 1
   ```

---

## Troubleshooting

### Container Won't Start

```bash
# Check logs
docker-compose logs app

# Common issues:
# 1. Port already in use
docker ps  # Check running containers
lsof -i :8000  # Find process using port

# 2. Build failure
docker-compose build --no-cache

# 3. Volume permissions
chmod -R 755 storage bootstrap/cache
```

### Database Connection Failed

```bash
# Check database container
docker-compose ps

# Test connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPDO()

# Check environment variables
docker-compose config | grep DB_
```

### Slow Performance

```bash
# Check resource usage
docker stats

# Increase limits in docker-compose.yml
services:
  app:
    mem_limit: 2g
    cpus: 2
```

### Storage/Cache Issues

```bash
# Clear caches in container
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# Check permissions
docker-compose exec app ls -la storage/
```

### Application Not Accessible

```bash
# Verify port mapping
docker-compose ps

# Check Nginx configuration
docker exec vcms_nginx cat /etc/nginx/conf.d/default.conf

# Restart containers
docker-compose restart
```

---

## Comparison: Which Platform?

| Platform | Ease | Cost | Performance | Recommendation |
|----------|------|------|-------------|----------------|
| Local Docker | Easy | Free | Dev only | Development |
| Railway | Easy | $5-50/mo | Good | ⭐ Recommended |
| DigitalOcean | Medium | $5-50/mo | Excellent | ⭐ Recommended |
| Vercel | Medium | $20+/mo | Poor for Laravel | ❌ Not recommended |
| Self-Hosted VPS | Hard | $5-50/mo | Excellent | For experts |

---

## Quick Reference Commands

### Local Development
```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose logs -f app
docker-compose down
```

### Production
```bash
docker-compose -f docker-compose.production.yml up -d
docker-compose -f docker-compose.production.yml exec app php artisan migrate --force
docker-compose -f docker-compose.production.yml logs -f app
```

### Image Management
```bash
docker build -t vcms:latest .
docker tag vcms:latest yourusername/vcms:latest
docker push yourusername/vcms:latest
docker pull yourusername/vcms:latest
```

---

## Next Steps

1. **Local Testing**
   ```bash
   docker-compose up -d
   # Test at http://localhost:8000
   ```

2. **Choose Platform**
   - Railway (easiest)
   - DigitalOcean (most control)

3. **Deploy**
   - Follow platform-specific guide above

4. **Monitor**
   - Check logs regularly
   - Set up alerts
   - Monitor performance

---

**Last Updated:** December 2, 2025
**Docker Version:** 20.10+
**Compose Version:** 2.0+
