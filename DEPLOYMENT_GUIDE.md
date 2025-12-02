# VCMS - Public Web Hosting Deployment Guide

This guide provides step-by-step instructions to deploy your VCMS Laravel application to public web hosting.

## Table of Contents
1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Environment Configuration](#environment-configuration)
3. [Production Build](#production-build)
4. [Deployment Steps](#deployment-steps)
5. [Post-Deployment Setup](#post-deployment-setup)
6. [Troubleshooting](#troubleshooting)

---

## Pre-Deployment Checklist

- [ ] Review and update all environment variables
- [ ] Ensure APP_DEBUG is set to false in production
- [ ] Update APP_URL to your domain
- [ ] Generate a production APP_KEY (or use existing one)
- [ ] Test database connectivity on hosting server
- [ ] Verify PHP version requirements (PHP 8.2+)
- [ ] Check hosting server has required extensions: mysql, mbstring, json, curl
- [ ] Set up database backups
- [ ] Test all critical features locally
- [ ] Review and update PayMongo credentials for production
- [ ] Configure HTTPS/SSL certificate
- [ ] Test email configuration

---

## Environment Configuration

### 1. Update .env for Production

Create a production `.env` file with the following adjustments:

```dotenv
APP_NAME="VCMS"
APP_ENV=production
APP_KEY=base64:KuosuITO3CXYTNOxCekQD4dKMclELD6K406niU5mwag=
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

# Logging
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning

# Database Configuration (Update with hosting provider details)
DB_CONNECTION=mysql
DB_HOST=your-hosting-db-host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_secure_db_password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache Configuration
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

# Email Configuration (Update with your provider)
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="support@yourdomain.com"
MAIL_FROM_NAME="VCMS Support"

# PayMongo Credentials (Update with production keys)
PAYMONGO_PUBLIC_KEY=pk_live_xxxxxxxxxxxxxxxx
PAYMONGO_SECRET_KEY=sk_live_xxxxxxxxxxxxxxxx

# AWS Configuration (if using S3 for file storage)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### 2. Important Configuration Notes

**Debug Mode:**
- **Local Development:** `APP_DEBUG=true`
- **Production:** `APP_DEBUG=false` (Never expose detailed errors publicly)

**Database:**
- Obtain database credentials from your hosting provider
- Create database and user before deployment
- Ensure remote connections are allowed if needed

**Email Configuration:**
- Update MAIL_MAILER to use SMTP (not 'log')
- Use a transactional email service (Mailtrap, SendGrid, Mailgun, AWS SES)
- Test email sending after deployment

**Session Encryption:**
- Set `SESSION_ENCRYPT=true` for production

---

## Production Build

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm install
npm run build
```

### 2. Generate Assets

```bash
# Build Vite assets for production
npm run build

# This generates optimized files in public/build/
```

### 3. Clear Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Deployment Steps

### For Most Web Hosting Providers (Shared Hosting, VPS)

#### Step 1: Upload Files

```bash
# Using FTP or SFTP:
# Upload all files EXCEPT:
#   - .env (handle separately)
#   - node_modules/
#   - storage/
#   - bootstrap/cache/
#   - vendor/ (will install fresh)

# Or using Git (if available):
git clone your-repo-url .
```

#### Step 2: Set Directory Permissions

```bash
# Via SSH (if available)
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public

# Ensure proper ownership if needed
chown -R www-data:www-data .
```

#### Step 3: Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

#### Step 4: Copy Environment File

```bash
cp .env.example .env
# Then edit .env with production values
```

#### Step 5: Generate Application Key

```bash
# If you haven't already
php artisan key:generate

# Or use existing key in .env
```

#### Step 6: Run Migrations

```bash
# First time setup
php artisan migrate --force

# Or with fresh seed (if needed)
# php artisan migrate:fresh --seed --force
```

#### Step 7: Build Front-end Assets

```bash
npm install
npm run build
```

#### Step 8: Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### For cPanel/WHM Hosting

1. **Create Database:**
   - Go to cPanel → Databases → MySQL Databases
   - Create new database and user
   - Add user to database with all privileges

2. **Upload Application:**
   - Use File Manager or SFTP
   - Upload to `public_html` or subdirectory

3. **Set Public Root (if using subdirectory):**
   - Go to cPanel → Addon Domains or Parked Domains
   - Set document root to `/public` directory

4. **Configure PHP Version:**
   - Ensure PHP 8.2+ is selected
   - Enable required extensions in EasyApache

5. **Run Setup Commands:**
   ```bash
   composer install --no-dev
   php artisan migrate --force
   npm run build
   php artisan config:cache
   ```

---

## Post-Deployment Setup

### 1. Verify Everything is Working

- [ ] Visit your domain and verify the app loads
- [ ] Test login functionality
- [ ] Verify payment processing (if applicable)
- [ ] Check file uploads work
- [ ] Test email notifications
- [ ] Review error logs in `storage/logs/`

### 2. Set Up SSL/HTTPS

**Via cPanel:**
- Go to AutoSSL or Let's Encrypt SSL
- Install free SSL certificate
- Update APP_URL to use HTTPS in .env

**Update .env:**
```dotenv
APP_URL=https://yourdomain.com
```

### 3. Configure Backups

- Set up regular database backups through hosting control panel
- Configure file backup schedule
- Test backup restoration process

### 4. Enable Cronjob (if needed)

For scheduled tasks (if you have any):

```bash
# Add to crontab (usually via hosting control panel)
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Monitor Application

- Check error logs regularly: `storage/logs/laravel.log`
- Set up error monitoring (optional: Sentry, Bugsnag)
- Monitor database performance
- Track server resources

---

## Hosting Provider Recommendations

### Shared Hosting
- **Recommended:** SiteGround, Kinsta, Bluehost (with cPanel)
- **Requirements:** PHP 8.2+, MySQL 5.7+, SSH access
- **Cost:** $5-$20/month

### VPS Hosting
- **Recommended:** DigitalOcean, Vultr, Linode
- **Benefits:** More control, better performance
- **Cost:** $5-$50/month

### Platform-as-a-Service (PaaS)
- **Recommended:** Heroku, Railway, Render
- **Benefits:** Easy deployment, auto-scaling
- **Cost:** $7-$50/month

---

## Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
1. Check error logs: `storage/logs/laravel.log`
2. Ensure storage and bootstrap/cache are writable
3. Verify database connection
4. Run: `php artisan config:cache`

### Issue: CSS/JS Not Loading

**Solution:**
1. Run: `npm run build`
2. Clear browser cache
3. Check assets are in `public/build/`
4. Update APP_URL in .env if domain changed

### Issue: Database Connection Failed

**Solution:**
1. Verify database credentials in .env
2. Check database user has proper permissions
3. Confirm remote access is enabled on hosting
4. Test connection locally first

### Issue: Composer Installation Fails

**Solution:**
1. Check PHP memory limit: `memory_limit=512M` in php.ini
2. Update composer: `composer self-update`
3. Clear cache: `composer clear-cache`
4. Try again: `composer install --no-dev`

### Issue: Files Can't Be Written to Storage

**Solution:**
```bash
# Fix permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Or if you have server access
chown -R www-data:www-data storage bootstrap
```

### Issue: Emails Not Sending

**Solution:**
1. Verify MAIL_MAILER is set to 'smtp' (not 'log')
2. Check SMTP credentials are correct
3. Verify sender email is authorized with provider
4. Test with: `php artisan tinker` and `Mail::raw('test', fn($m) => $m->to('test@example.com'))`

---

## Security Checklist

- [ ] APP_DEBUG = false
- [ ] SESSION_ENCRYPT = true
- [ ] Use HTTPS/SSL certificate
- [ ] Update PayMongo to production keys
- [ ] Disable registration if not needed
- [ ] Implement rate limiting
- [ ] Regular security updates (composer update)
- [ ] Use strong database passwords
- [ ] Keep .env file secure
- [ ] Regular backups enabled
- [ ] Monitor access logs for suspicious activity

---

## Quick Reference Commands

```bash
# Development
php artisan serve
npm run dev

# Production Build
composer install --no-dev --optimize-autoloader
npm run build

# Cache Commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Database
php artisan migrate --force
php artisan migrate:fresh --force
php artisan db:seed --force

# Troubleshooting
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan tinker
tail -f storage/logs/laravel.log
```

---

## Support & Additional Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Laravel Deployment:** https://laravel.com/docs/deployment
- **PayMongo Docs:** https://developers.paymongo.com/
- **Vite Documentation:** https://vitejs.dev/

---

**Last Updated:** December 2, 2025
**Laravel Version:** 12.0
**PHP Version Required:** 8.2+
