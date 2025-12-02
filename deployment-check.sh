#!/bin/bash

# VCMS Production Deployment Checklist Script
# Run this script to verify your application is ready for production deployment

echo "======================================"
echo "VCMS Production Deployment Checklist"
echo "======================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counters
PASSED=0
FAILED=0
WARNINGS=0

# Helper functions
check_pass() {
    echo -e "${GREEN}✓${NC} $1"
    ((PASSED++))
}

check_fail() {
    echo -e "${RED}✗${NC} $1"
    ((FAILED++))
}

check_warn() {
    echo -e "${YELLOW}⚠${NC} $1"
    ((WARNINGS++))
}

# 1. Environment File Checks
echo "1. Checking Environment Configuration..."
echo ""

if [ -f ".env" ]; then
    check_pass ".env file exists"
    
    if grep -q "APP_ENV=production" .env; then
        check_pass "APP_ENV is set to production"
    else
        check_warn "APP_ENV is not set to production (current: $(grep APP_ENV .env))"
    fi
    
    if grep -q "APP_DEBUG=false" .env; then
        check_pass "APP_DEBUG is set to false"
    else
        check_fail "APP_DEBUG should be false (current: $(grep APP_DEBUG .env))"
    fi
    
    if grep -q "APP_KEY=" .env && ! grep -q "APP_KEY=$" .env; then
        check_pass "APP_KEY is set"
    else
        check_fail "APP_KEY is not set or is empty"
    fi
    
    if ! grep -q "APP_URL=http:/192.168.1.10" .env; then
        check_pass "APP_URL appears to be updated from local"
    else
        check_warn "APP_URL is still set to local development value"
    fi
else
    check_fail ".env file does not exist"
fi

echo ""
echo "2. Checking Directory Permissions..."
echo ""

# Check storage directory
if [ -d "storage" ]; then
    if [ -w "storage" ]; then
        check_pass "storage/ directory is writable"
    else
        check_fail "storage/ directory is not writable"
    fi
else
    check_fail "storage/ directory does not exist"
fi

# Check bootstrap/cache directory
if [ -d "bootstrap/cache" ]; then
    if [ -w "bootstrap/cache" ]; then
        check_pass "bootstrap/cache/ directory is writable"
    else
        check_fail "bootstrap/cache/ directory is not writable"
    fi
else
    check_fail "bootstrap/cache/ directory does not exist"
fi

echo ""
echo "3. Checking Dependencies..."
echo ""

# Check if vendor exists
if [ -d "vendor" ]; then
    check_pass "vendor/ directory exists (dependencies installed)"
else
    check_warn "vendor/ directory not found - run 'composer install --no-dev'"
fi

# Check if node_modules exists (optional)
if [ -d "node_modules" ]; then
    check_pass "node_modules/ exists"
elif [ -f "package.json" ]; then
    check_warn "package.json exists but node_modules/ not found - run 'npm install && npm run build'"
fi

# Check if public/build exists
if [ -d "public/build" ]; then
    check_pass "public/build/ exists (assets built)"
else
    check_warn "public/build/ not found - run 'npm run build' in production"
fi

echo ""
echo "4. Checking PHP Requirements..."
echo ""

# Check PHP version
php_version=$(php -r "echo PHP_VERSION;")
php_major_minor=$(echo $php_version | cut -d. -f1,2)

if [ "$(echo "$php_major_minor >= 8.2" | bc)" -eq 1 ]; then
    check_pass "PHP version is $php_version (requires 8.2+)"
else
    check_fail "PHP version is $php_version (requires 8.2+)"
fi

# Check required extensions
required_extensions=("mbstring" "curl" "json" "pdo_mysql")
for ext in "${required_extensions[@]}"; do
    if php -m | grep -q "$ext"; then
        check_pass "PHP extension '$ext' is enabled"
    else
        check_fail "PHP extension '$ext' is NOT enabled"
    fi
done

echo ""
echo "5. Checking Application Files..."
echo ""

# Check key files
key_files=("app/Http/Controllers" "config/app.php" "routes/web.php" "resources/views")
for file in "${key_files[@]}"; do
    if [ -e "$file" ]; then
        check_pass "$file exists"
    else
        check_fail "$file is missing"
    fi
done

echo ""
echo "6. Database Configuration..."
echo ""

if grep -q "DB_HOST=" .env 2>/dev/null; then
    db_host=$(grep "DB_HOST=" .env | cut -d= -f2)
    db_name=$(grep "DB_DATABASE=" .env | cut -d= -f2)
    
    if [ "$db_host" != "127.0.0.1" ] && [ "$db_host" != "localhost" ]; then
        check_pass "Database host is configured for remote server: $db_host"
    else
        check_warn "Database host is localhost - verify this is correct for production"
    fi
    
    check_pass "Database name is configured: $db_name"
else
    check_fail "Database configuration is missing"
fi

echo ""
echo "7. Security Checks..."
echo ""

# Check .gitignore
if grep -q ".env" .gitignore 2>/dev/null; then
    check_pass ".env is in .gitignore"
else
    check_fail ".env is NOT in .gitignore"
fi

if grep -q "vendor/" .gitignore 2>/dev/null; then
    check_pass "vendor/ is in .gitignore"
else
    check_fail "vendor/ is NOT in .gitignore"
fi

echo ""
echo "8. Configuration Caching..."
echo ""

if php artisan config:cache 2>&1 | grep -q "Configuration cached"; then
    check_pass "Configuration can be cached"
elif php artisan config:cache 2>&1 | grep -q "already"; then
    check_pass "Configuration already cached"
else
    check_warn "Configuration caching may have issues"
fi

echo ""
echo "======================================"
echo "DEPLOYMENT READINESS SUMMARY"
echo "======================================"
echo -e "${GREEN}Passed:${NC} $PASSED"
echo -e "${YELLOW}Warnings:${NC} $WARNINGS"
echo -e "${RED}Failed:${NC} $FAILED"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ Your application is ready for production deployment!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Upload files to hosting server (excluding vendor, node_modules)"
    echo "2. Copy .env to server and update database credentials"
    echo "3. Run: composer install --no-dev --optimize-autoloader"
    echo "4. Run: npm run build (or upload pre-built public/build)"
    echo "5. Run: php artisan migrate --force"
    echo "6. Run: php artisan cache:clear && php artisan config:cache"
    exit 0
else
    echo -e "${RED}✗ Please fix the failures above before deploying to production${NC}"
    exit 1
fi
