# VCMS Production Deployment Workflow

A step-by-step guide for deploying the VCMS application to production hosting.

## Quick Start (5-10 minutes)

If you're familiar with Laravel deployment, follow these quick steps:

### Local Preparation
```bash
# 1. Run deployment check
./deployment-check.sh  # or deployment-check.ps1 on Windows

# 2. Build production assets
npm install
npm run build

# 3. Install production dependencies
composer install --no-dev --optimize-autoloader

# 4. Create production .env from template
cp .env.production .env
```

### On Hosting Server
```bash
# 1. Upload all files (except vendor, node_modules, storage/logs)
# 2. Copy .env and update credentials
# 3. Set permissions (if SSH available)
chmod -R 755 storage bootstrap/cache

# 4. Run setup
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

---

## Detailed Deployment Steps

### Phase 1: Pre-Deployment Preparation (Local Machine)

#### Step 1.1: Verify Application Status
```bash
# Run the deployment checklist
./deployment-check.sh
```

**Expected Output:**
- All critical items should pass (✓)
- Warnings are acceptable but should be reviewed
- No failures should exist

#### Step 1.2: Test Locally
```bash
# Clear all caches first
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart development server
php artisan serve

# Run critical tests if available
php artisan test
```

#### Step 1.3: Build Production Assets
```bash
# Install dependencies (if not already done)
npm install

# Build optimized frontend assets
npm run build

# Verify build was successful
ls -la public/build/
```

Should see:
- `manifest.json`
- CSS files
- JavaScript files

#### Step 1.4: Generate Production Dependencies
```bash
# Install production PHP dependencies only
composer install --no-dev --optimize-autoloader

# This should significantly reduce vendor size
du -sh vendor/
```

#### Step 1.5: Prepare .env File
```bash
# Copy production template
cp .env.production .env.for-upload

# OR manually create .env with:
# - APP_ENV=production
# - APP_DEBUG=false
# - APP_URL=https://yourdomain.com
# - Database credentials (from hosting provider)
# - Email configuration
# - PayMongo production keys (if needed)
```

### Phase 2: Upload to Hosting

#### Step 2.1: Choose Upload Method

**Option A: FTP/SFTP (Most Common)**
```
Use FileZilla, WinSCP, or similar FTP client:
1. Connect to hosting server
2. Navigate to public_html or appropriate directory
3. Upload all files EXCEPT:
   - .env (upload separately)
   - node_modules/
   - vendor/ (install fresh on server)
   - storage/ (create fresh)
   - bootstrap/cache (create fresh)
```

**Option B: Git (If Available)**
```bash
# Clone repository directly on server
cd public_html
git clone https://github.com/yourusername/VCMS-final .

# Or pull if already cloned
git pull origin master
```

**Option C: cPanel File Manager**
1. Log in to cPanel
2. File Manager → public_html
3. Create .zip of application locally
4. Upload .zip via File Manager
5. Extract on server

#### Step 2.2: Upload Environment File
```bash
# Upload .env.for-upload as .env
# Use SFTP or cPanel File Manager
# NEVER commit .env to Git
```

#### Step 2.3: Verify Upload
- [ ] All application files are on server
- [ ] .env file exists (not .env.example)
- [ ] public/index.php exists

### Phase 3: Configure Hosting Environment

#### Step 3.1: Set Up Database (cPanel Example)

1. **Create Database:**
   - cPanel → Databases → MySQL Databases
   - Database Name: `yourdomain_vcms`
   - Create User: `yourdomain_vcms`
   - Password: Generate strong password
   - Add all privileges to user

2. **Update .env:**
   ```dotenv
   DB_HOST=localhost
   DB_DATABASE=yourdomain_vcms
   DB_USERNAME=yourdomain_vcms
   DB_PASSWORD=your_generated_password
   ```

#### Step 3.2: Configure PHP Version

1. **Via cPanel:**
   - Select PHP Version → PHP 8.2 or higher
   - Enable required extensions:
     - mbstring
     - curl
     - json
     - pdo_mysql
     - gd (for image handling)
     - xml (for parsing)

2. **Via SSH:**
   ```bash
   # Check current PHP version
   php -v
   
   # Check enabled extensions
   php -m
   ```

#### Step 3.3: Set Directory Permissions

**Via cPanel Terminal or SSH:**
```bash
# Navigate to application directory
cd /path/to/application

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env

# If you can identify web server user
chown -R www-data:www-data .
chmod -R 755 storage bootstrap/cache
```

#### Step 3.4: Configure SSL/HTTPS

1. **Via cPanel:**
   - Go to AutoSSL or Let's Encrypt SSL
   - Install free SSL certificate
   - Wait for installation (usually 30 minutes)

2. **Update .env:**
   ```dotenv
   APP_URL=https://yourdomain.com
   SESSION_SECURE_COOKIES=true
   ```

3. **Force HTTPS Redirect:**
   
   Update `bootstrap/app.php`:
   ```php
   if (env('APP_ENV') === 'production') {
       URL::forceScheme('https');
   }
   ```

### Phase 4: Run Installation Commands

**Via SSH Terminal (Recommended):**

```bash
cd /path/to/application

# 1. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 2. Run database migrations
php artisan migrate --force

# 3. Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Clear any development caches
php artisan cache:clear
php artisan event:cache

# 5. Create storage symlink (if needed)
php artisan storage:link
```

**Via cPanel (if no SSH):**
- Use "Terminal" in cPanel if available
- Or create a file `php-run.php` in public_html:
  ```php
  <?php
  chdir(dirname(__FILE__));
  require 'artisan';
  ?>
  ```
- Then run commands via URL

### Phase 5: Verify Deployment

#### Step 5.1: Basic Checks
- [ ] Website loads at https://yourdomain.com
- [ ] No "500 Internal Server Error" messages
- [ ] CSS and JavaScript load correctly (check browser console)
- [ ] Database tables exist (check phpMyAdmin)

#### Step 5.2: Functional Tests
- [ ] User login works
- [ ] Create/edit functionality works (if applicable)
- [ ] File uploads work
- [ ] Payment processing works (if applicable)
- [ ] Email sending works

#### Step 5.3: Check Error Logs
```bash
# View application errors
tail -f storage/logs/laravel.log

# Or access via cPanel -> Raw Access Logs
```

#### Step 5.4: Performance Check
- Load website and check load time
- Verify assets are cached/minified
- Check database query performance

### Phase 6: Post-Deployment Setup

#### Step 6.1: Set Up Monitoring
- [ ] Enable error tracking (Sentry, Bugsnag)
- [ ] Set up uptime monitoring
- [ ] Configure log aggregation if needed

#### Step 6.2: Configure Backups
```bash
# Via cPanel:
# 1. Go to Backup Wizard
# 2. Set up automatic daily/weekly backups
# 3. Download backup to external storage
```

#### Step 6.3: Set Up Scheduled Tasks (if needed)

**In cPanel Cron Jobs:**
```bash
# Add if you have scheduled commands
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

#### Step 6.4: Configure Email
1. **Update .env:**
   ```dotenv
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   MAIL_FROM_ADDRESS=support@yourdomain.com
   ```

2. **Test Email:**
   ```bash
   php artisan tinker
   Mail::raw('Test email', fn($m) => $m->to('test@example.com'))
   ```

#### Step 6.5: Domain Setup
- [ ] Point domain A record to hosting server IP
- [ ] Set up www subdomain (if needed)
- [ ] Wait for DNS propagation (5-48 hours)
- [ ] Verify domain resolves to your site

---

## Troubleshooting Common Issues

### Issue: 500 Internal Server Error

**Diagnosis:**
1. Check error logs: `storage/logs/laravel.log`
2. Enable debug mode temporarily:
   ```bash
   # In .env (DO NOT keep in production)
   APP_DEBUG=true
   
   # Reload page to see error
   
   # Then set back to false
   APP_DEBUG=false
   ```

**Common Causes:**
- Missing database credentials
- Insufficient file permissions
- Missing PHP extensions
- Incorrect APP_KEY

**Solution:**
```bash
# Recache configuration
php artisan config:clear
php artisan config:cache

# Check storage permissions
chmod -R 755 storage bootstrap/cache
```

### Issue: CSS/JS Not Loading (404 Errors)

**Cause:** Missing public/build directory

**Solution:**
```bash
# Option 1: Build on server
npm install
npm run build

# Option 2: Upload pre-built public/build directory
# Via FTP: Upload public/build folder

# Option 3: Create symlink if assets in different folder
php artisan storage:link
```

### Issue: Database Connection Failed

**Diagnosis:**
```bash
# Test connection from server
php artisan tinker
DB::connection()->getPDO()
```

**Common Causes:**
- Wrong database credentials
- Database user doesn't have permissions
- Database host is restricted

**Solution:**
1. Verify credentials in .env
2. In cPanel → Databases → Modify → Check user privileges
3. Ensure remote MySQL is enabled (if needed)
4. Recreate database user with full privileges

### Issue: Can't Write to Storage

**Cause:** Permission denied on storage folder

**Solution:**
```bash
# Fix permissions
chmod -R 755 storage bootstrap/cache

# Or with web server user
chown -R www-data:www-data storage bootstrap/cache
```

### Issue: PayMongo Payment Processing Fails

**Cause:** Using test keys instead of production keys

**Solution:**
```dotenv
# In .env, update to production keys:
PAYMONGO_PUBLIC_KEY=pk_live_xxxxxxxxxxxxxxxx
PAYMONGO_SECRET_KEY=sk_live_xxxxxxxxxxxxxxxx
```

### Issue: Emails Not Sending

**Cause:** Mail configuration incorrect

**Solution:**
```bash
# 1. Update MAIL_MAILER (not 'log')
MAIL_MAILER=smtp

# 2. Verify SMTP credentials
# 3. Test sending
php artisan tinker
Mail::raw('Test', fn($m) => $m->to('your@email.com'))

# 4. Check logs for errors
tail storage/logs/laravel.log
```

---

## Performance Optimization

### Enable OPcache
```bash
# Add to php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.validate_timestamps=0
```

### Use CDN (Optional)
```php
// In config/app.php
'asset_url' => env('ASSET_URL', null),
```

### Database Optimization
```bash
# Analyze tables
php artisan tinker
DB::statement('ANALYZE TABLE users, posts, ...')
```

### Image Optimization
- Upload optimized images only
- Use WebP format when possible
- Implement image caching

---

## Rollback Plan

If something goes wrong in production:

### Quick Rollback
```bash
# 1. Restore previous .env
# 2. Restore previous database from backup
php artisan migrate:rollback

# 3. Clear all caches
php artisan cache:clear
php artisan config:clear
```

### Full Rollback
1. Keep a backup of working version
2. If major issues, upload previous version
3. Restore database from backup
4. Test thoroughly in development before re-deploying

---

## Maintenance Mode

### Enable Maintenance Mode
```bash
# During updates/maintenance
php artisan down --render="vendor/laravel/framework/src/Illuminate/Foundation/down.html"

# Custom message
php artisan down --message="Upgrading database. Please check back soon."
```

### Disable Maintenance Mode
```bash
php artisan up
```

---

## Security Checklist (Final)

- [ ] APP_DEBUG = false
- [ ] APP_ENV = production
- [ ] HTTPS/SSL enabled
- [ ] SESSION_ENCRYPT = true
- [ ] Strong database password
- [ ] .env file not in Git
- [ ] Sensitive files not publicly accessible
- [ ] Regular backups enabled
- [ ] Security headers configured
- [ ] Update all packages regularly

---

## Quick Command Reference

```bash
# Cache commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

# Database
php artisan migrate --force
php artisan migrate:rollback --force
php artisan db:seed --force

# Assets
npm run build

# Maintenance
php artisan down
php artisan up

# Debugging
php artisan tinker
php artisan logs
tail -f storage/logs/laravel.log
```

---

## Support Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Laravel Deployment Guide:** https://laravel.com/docs/deployment
- **Vite Documentation:** https://vitejs.dev/
- **cPanel Documentation:** https://docs.cpanel.net/
- **PayMongo API Docs:** https://developers.paymongo.com/

---

**Last Updated:** December 2, 2025
**Version:** 1.0
**Maintained by:** Development Team
