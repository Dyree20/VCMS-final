# VCMS Hosting Provider Setup Guide

Quick setup guides for popular hosting providers.

## Table of Contents
1. [cPanel-Based Hosting (Most Common)](#cpanel-based-hosting)
2. [SiteGround](#siteground)
3. [Bluehost](#bluehost)
4. [DigitalOcean](#digitalocean)
5. [Heroku](#heroku)
6. [Vercel/Netlify](#vernelvercel-and-netlify-not-recommended-for-laravel)

---

## cPanel-Based Hosting

**Providers:** SiteGround, Bluehost, GoDaddy, Hostinger, etc.

### Step 1: Create Database

1. Log in to cPanel
2. Click **Databases** → **MySQL Databases**
3. Create new database:
   - Name: `yourdomain_vcms` (or similar)
   - Create new user: `yourdomain_vcmsuser`
   - Password: Generate strong password
   - Add user to database
   - Check "All Privileges"

### Step 2: Set PHP Version

1. Select **PHP Version** or **EasyApache**
2. Choose **PHP 8.2** or higher
3. Enable extensions:
   - curl
   - json
   - mbstring
   - pdo_mysql
   - gd
   - xml

### Step 3: Upload Application

**Method 1: File Manager**
```
1. cPanel → File Manager → public_html
2. Upload application ZIP file
3. Extract ZIP
4. Delete ZIP file
```

**Method 2: FTP**
```
1. Get FTP credentials from cPanel → FTP Accounts
2. Use FileZilla or WinSCP
3. Upload all files to public_html
```

**Method 3: Git**
```bash
# If SSH available (cPanel → Terminal)
cd public_html
git clone https://github.com/yourusername/VCMS-final .
```

### Step 4: Configure Application

1. **Upload .env:**
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   DB_HOST=localhost
   DB_DATABASE=yourdomain_vcms
   DB_USERNAME=yourdomain_vcmsuser
   DB_PASSWORD=your_generated_password
   ```

2. **Install Dependencies (via SSH/Terminal):**
   ```bash
   cd public_html
   composer install --no-dev --optimize-autoloader
   npm run build
   php artisan migrate --force
   php artisan config:cache
   ```

3. **Set Permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

### Step 5: Enable SSL/HTTPS

1. cPanel → **AutoSSL** or **Let's Encrypt SSL**
2. Install free SSL certificate
3. Update .env:
   ```dotenv
   APP_URL=https://yourdomain.com
   ```

4. Force HTTPS in `bootstrap/app.php`:
   ```php
   if (env('APP_ENV') === 'production') {
       \Illuminate\Support\Facades\URL::forceScheme('https');
   }
   ```

### Step 6: Set Up Cron Jobs (if needed)

1. cPanel → **Cron Jobs**
2. Add new job:
   ```bash
   * * * * * cd /home/yourusername/public_html && php artisan schedule:run >> /dev/null 2>&1
   ```

### Step 7: Configure Email

1. In cPanel, set up email account for `noreply@yourdomain.com`
2. Update .env:
   ```dotenv
   MAIL_MAILER=smtp
   MAIL_HOST=mail.yourdomain.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@yourdomain.com
   MAIL_PASSWORD=your_email_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="VCMS"
   ```

---

## SiteGround

**Website:** https://www.siteground.com/

### Pre-Deployment
- PHP 8.2+ selected
- MySQL database created
- SSL certificate installed (free)

### Setup

1. **Create Database:**
   - Dashboard → Databases → MySQL
   - New Database
   - Add User (same panel)

2. **Upload Files:**
   - File Manager or SFTP
   - Upload to `/public_html`

3. **Install & Configure:**
   ```bash
   # SSH in to server
   composer install --no-dev
   npm run build
   php artisan migrate --force
   php artisan config:cache
   ```

4. **Special SiteGround Settings:**
   - Auto-scaling: Enabled
   - Caching: Use built-in caching
   - PHP Opcode Cache: Enabled (recommended)

### Support
- SiteGround has 24/7 live chat support
- Excellent for beginners

---

## Bluehost

**Website:** https://www.bluehost.com/

### Recommended Plan
- Plus plan or higher (has SSH access)
- Unmetered storage
- Unlimited add-on domains

### Setup

1. **Create Database:**
   - cPanel → MySQL Databases
   - Create DB and user

2. **Upload Application:**
   - Via FTP with provided credentials
   - Or File Manager in cPanel

3. **Configure PHP:**
   ```bash
   # Via SSH
   php -v  # Should show 8.2+
   
   # Check extensions
   php -m | grep -i mysql
   php -m | grep -i curl
   ```

4. **Install Dependencies:**
   ```bash
   cd public_html
   composer install --no-dev --optimize-autoloader
   npm run build
   php artisan migrate --force
   php artisan config:cache
   ```

5. **Fix Permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

### Bluehost Specific Notes
- Default email: Use Bluehost email or external SMTP
- Backups: Use Bluehost automatic backups
- Support: Good for basic issues

---

## DigitalOcean

**Website:** https://www.digitalocean.com/**

### Recommended Setup
- App Platform (easiest) OR
- Droplet with Laravel Stack

### Via App Platform (Easiest)

1. **Create New App:**
   - Connect GitHub repository
   - Select "PHP/Laravel" runtime
   - Configure environment variables

2. **Set Environment:**
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   DB_HOST=db-mysql-xxx.ondigitalocean.com
   DB_DATABASE=vcms
   DB_USERNAME=doadmin
   DB_PASSWORD=xxxxxxxxxxxx
   ```

3. **Add Database:**
   - MySQL database cluster
   - Link to App Platform

4. **Deploy:**
   - Push to GitHub
   - App Platform auto-deploys

### Via Droplet (More Control)

1. **Create Droplet:**
   - Ubuntu 22.04
   - Laravel Stack app ($12/month starting)

2. **SSH into Droplet:**
   ```bash
   ssh root@your_droplet_ip
   ```

3. **Setup Application:**
   ```bash
   cd /home/deploy
   git clone your-repo
   cd VCMS-final
   composer install --no-dev
   npm run build
   php artisan migrate --force
   php artisan config:cache
   ```

4. **Configure Nginx:**
   ```nginx
   # /etc/nginx/sites-available/default
   root /home/deploy/VCMS-final/public;
   ```

5. **Enable HTTPS:**
   ```bash
   certbot --nginx -d yourdomain.com
   ```

---

## Heroku

**Website:** https://www.heroku.com/

### Note
Heroku requires significant configuration for Laravel. Not recommended for beginners.

### Basic Setup

1. **Create Procfile:**
   ```
   web: vendor/bin/heroku-php-apache2 public/
   ```

2. **Create Heroku App:**
   ```bash
   heroku create your-app-name
   heroku addons:create cleardb:ignite  # Free MySQL
   ```

3. **Set Environment:**
   ```bash
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   heroku config:set APP_KEY=base64:xxxxx
   ```

4. **Deploy:**
   ```bash
   git push heroku master
   heroku run php artisan migrate
   ```

### Limitations
- Limited free tier
- Paid after trial period
- Database add-ons cost extra

---

## Vercel and Netlify (Not Recommended for Laravel)

### Why Not?
- **Vercel/Netlify** are designed for static sites and serverless functions
- **Laravel** requires a persistent server
- Use these ONLY if you've separated frontend/backend

### If You Must:
- Deploy Laravel separately (use cPanel, DigitalOcean, etc.)
- Deploy front-end to Vercel/Netlify
- Use API calls between them

**Better:** Deploy entire app to cPanel or DigitalOcean

---

## Comparison Table

| Provider | Price | Ease | Support | PHP Version | Recommended |
|----------|-------|------|---------|-------------|-------------|
| SiteGround | $3-15/mo | Very Easy | Excellent | 8.2+ | ⭐⭐⭐ Beginner |
| Bluehost | $2-12/mo | Easy | Good | 8.2+ | ⭐⭐ Budget |
| GoDaddy | $3-20/mo | Medium | Fair | 8.2+ | ⭐⭐ |
| Kinsta | $35+/mo | Medium | Excellent | 8.2+ | ⭐⭐⭐ Premium |
| DigitalOcean | $5-50/mo | Hard | Community | 8.2+ | ⭐⭐⭐ Developers |
| Heroku | $50+/mo | Hard | Community | 8.2+ | ⭐ Not Recommended |
| AWS | Variable | Very Hard | Email | 8.2+ | ⭐ Enterprise |

---

## Post-Deployment Checklist

For ANY hosting provider:

- [ ] Website loads on HTTPS
- [ ] CSS/JS loads correctly (Ctrl+Shift+K to check console)
- [ ] Login functionality works
- [ ] Database queries work
- [ ] File uploads work (if applicable)
- [ ] Email sends correctly
- [ ] Payment processing works (if applicable)
- [ ] Error logs are clean (`storage/logs/laravel.log`)
- [ ] Backups are configured
- [ ] SSL certificate auto-renews
- [ ] Domain resolves correctly

---

## Quick Troubleshooting by Provider

### SiteGround Specific
**Issue:** Composer memory limit
```bash
# SiteGround allows increasing memory
php -d memory_limit=512M composer install
```

### Bluehost Specific
**Issue:** SSH access not working
- Ensure you have Plus plan or higher
- Contact Bluehost support to enable SSH

### DigitalOcean Specific
**Issue:** Firewall blocking port 443
```bash
ufw allow 443
ufw allow 80
ufw reload
```

### Heroku Specific
**Issue:** Database reset after deployment
```bash
heroku run php artisan migrate --force
```

---

## Emergency Support

If something breaks:

1. **Check error logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Enable debug mode temporarily:**
   ```bash
   APP_DEBUG=true
   php artisan config:cache
   ```

3. **Check permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

4. **Restore from backup:**
   - Most providers have backup snapshots
   - Restore database from backup
   - Re-upload working version of code

5. **Contact provider support:**
   - SiteGround, Bluehost: 24/7 live chat
   - DigitalOcean: Community forums + documentation
   - Custom VPS: SSH into server and debug

---

**Last Updated:** December 2, 2025
**Version:** 1.0
