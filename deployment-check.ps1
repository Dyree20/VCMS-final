# VCMS Production Deployment Checklist Script (Windows PowerShell)
# Run this script to verify your application is ready for production deployment

Write-Host "======================================"
Write-Host "VCMS Production Deployment Checklist"
Write-Host "======================================"
Write-Host ""

# Counters
$PASSED = 0
$FAILED = 0
$WARNINGS = 0

# Helper functions
function Check-Pass {
    param([string]$message)
    Write-Host "✓ $message" -ForegroundColor Green
    $script:PASSED++
}

function Check-Fail {
    param([string]$message)
    Write-Host "✗ $message" -ForegroundColor Red
    $script:FAILED++
}

function Check-Warn {
    param([string]$message)
    Write-Host "⚠ $message" -ForegroundColor Yellow
    $script:WARNINGS++
}

# 1. Environment File Checks
Write-Host "1. Checking Environment Configuration..."
Write-Host ""

if (Test-Path ".env") {
    Check-Pass ".env file exists"
    
    $envContent = Get-Content ".env"
    
    if ($envContent -match "APP_ENV=production") {
        Check-Pass "APP_ENV is set to production"
    } else {
        $appEnv = ($envContent | Select-String "APP_ENV=").ToString()
        Check-Warn "APP_ENV is not set to production (current: $appEnv)"
    }
    
    if ($envContent -match "APP_DEBUG=false") {
        Check-Pass "APP_DEBUG is set to false"
    } else {
        Check-Fail "APP_DEBUG should be false"
    }
    
    if ($envContent -match "APP_KEY=base64:" -and -not ($envContent -match "APP_KEY=\s*$")) {
        Check-Pass "APP_KEY is set"
    } else {
        Check-Fail "APP_KEY is not set or is empty"
    }
    
    if (-not ($envContent -match "APP_URL=http://192.168.1.10")) {
        Check-Pass "APP_URL appears to be updated from local"
    } else {
        Check-Warn "APP_URL is still set to local development value"
    }
} else {
    Check-Fail ".env file does not exist"
}

Write-Host ""
Write-Host "2. Checking Directory Permissions..."
Write-Host ""

# Check storage directory
if (Test-Path "storage") {
    try {
        $testFile = Join-Path "storage" "test_write.tmp"
        [System.IO.File]::WriteAllText($testFile, "test")
        Remove-Item $testFile
        Check-Pass "storage/ directory is writable"
    } catch {
        Check-Fail "storage/ directory is not writable"
    }
} else {
    Check-Fail "storage/ directory does not exist"
}

# Check bootstrap/cache directory
if (Test-Path "bootstrap/cache") {
    try {
        $testFile = Join-Path "bootstrap/cache" "test_write.tmp"
        [System.IO.File]::WriteAllText($testFile, "test")
        Remove-Item $testFile
        Check-Pass "bootstrap/cache/ directory is writable"
    } catch {
        Check-Fail "bootstrap/cache/ directory is not writable"
    }
} else {
    Check-Fail "bootstrap/cache/ directory does not exist"
}

Write-Host ""
Write-Host "3. Checking Dependencies..."
Write-Host ""

# Check if vendor exists
if (Test-Path "vendor") {
    Check-Pass "vendor/ directory exists (dependencies installed)"
} else {
    Check-Warn "vendor/ directory not found - run 'composer install --no-dev'"
}

# Check if node_modules exists (optional)
if (Test-Path "node_modules") {
    Check-Pass "node_modules/ exists"
} elseif (Test-Path "package.json") {
    Check-Warn "package.json exists but node_modules/ not found - run 'npm install && npm run build'"
}

# Check if public/build exists
if (Test-Path "public/build") {
    Check-Pass "public/build/ exists (assets built)"
} else {
    Check-Warn "public/build/ not found - run 'npm run build' in production"
}

Write-Host ""
Write-Host "4. Checking PHP Requirements..."
Write-Host ""

# Check PHP version
try {
    $phpVersion = & php -r "echo PHP_VERSION;"
    $phpMajorMinor = $phpVersion -split '\.' | Select-Object -First 2
    $version = [version]"$($phpMajorMinor -join '.')"
    
    if ($version -ge [version]"8.2") {
        Check-Pass "PHP version is $phpVersion (requires 8.2+)"
    } else {
        Check-Fail "PHP version is $phpVersion (requires 8.2+)"
    }
} catch {
    Check-Fail "Could not determine PHP version - ensure PHP is in PATH"
}

# Check required extensions
$requiredExtensions = @("mbstring", "curl", "json", "pdo_mysql")
foreach ($ext in $requiredExtensions) {
    try {
        $phpModules = & php -m
        if ($phpModules -match $ext) {
            Check-Pass "PHP extension '$ext' is enabled"
        } else {
            Check-Fail "PHP extension '$ext' is NOT enabled"
        }
    } catch {
        Check-Fail "Could not check PHP extension '$ext'"
    }
}

Write-Host ""
Write-Host "5. Checking Application Files..."
Write-Host ""

# Check key files
$keyFiles = @("app/Http/Controllers", "config/app.php", "routes/web.php", "resources/views")
foreach ($file in $keyFiles) {
    if (Test-Path $file) {
        Check-Pass "$file exists"
    } else {
        Check-Fail "$file is missing"
    }
}

Write-Host ""
Write-Host "6. Database Configuration..."
Write-Host ""

if (Test-Path ".env") {
    $envContent = Get-Content ".env"
    $dbHost = ($envContent | Select-String "^DB_HOST=" -ErrorAction SilentlyContinue).ToString() -replace "DB_HOST=", ""
    $dbName = ($envContent | Select-String "^DB_DATABASE=" -ErrorAction SilentlyContinue).ToString() -replace "DB_DATABASE=", ""
    
    if ($dbHost -and $dbHost -ne "127.0.0.1" -and $dbHost -ne "localhost") {
        Check-Pass "Database host is configured for remote server: $dbHost"
    } elseif ($dbHost) {
        Check-Warn "Database host is localhost - verify this is correct for production"
    }
    
    if ($dbName) {
        Check-Pass "Database name is configured: $dbName"
    } else {
        Check-Warn "Database name not found in .env"
    }
} else {
    Check-Fail "Database configuration is missing (.env not found)"
}

Write-Host ""
Write-Host "7. Security Checks..."
Write-Host ""

# Check .gitignore
if (Test-Path ".gitignore") {
    $gitignore = Get-Content ".gitignore"
    if ($gitignore -match "\.env") {
        Check-Pass ".env is in .gitignore"
    } else {
        Check-Fail ".env is NOT in .gitignore"
    }
    
    if ($gitignore -match "vendor/") {
        Check-Pass "vendor/ is in .gitignore"
    } else {
        Check-Fail "vendor/ is NOT in .gitignore"
    }
} else {
    Check-Warn ".gitignore file not found"
}

Write-Host ""
Write-Host "8. Configuration Caching..."
Write-Host ""

try {
    $output = & php artisan config:cache 2>&1
    if ($output -match "Configuration cached" -or $output -match "already") {
        Check-Pass "Configuration can be cached"
    } else {
        Check-Warn "Configuration caching may have issues"
    }
} catch {
    Check-Warn "Could not verify configuration caching"
}

Write-Host ""
Write-Host "======================================"
Write-Host "DEPLOYMENT READINESS SUMMARY"
Write-Host "======================================"
Write-Host "Passed: $PASSED" -ForegroundColor Green
Write-Host "Warnings: $WARNINGS" -ForegroundColor Yellow
Write-Host "Failed: $FAILED" -ForegroundColor Red
Write-Host ""

if ($FAILED -eq 0) {
    Write-Host "✓ Your application is ready for production deployment!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:"
    Write-Host "1. Upload files to hosting server (excluding vendor, node_modules)"
    Write-Host "2. Copy .env to server and update database credentials"
    Write-Host "3. Run: composer install --no-dev --optimize-autoloader"
    Write-Host "4. Run: npm run build (or upload pre-built public/build)"
    Write-Host "5. Run: php artisan migrate --force"
    Write-Host "6. Run: php artisan cache:clear && php artisan config:cache"
    exit 0
} else {
    Write-Host "✗ Please fix the failures above before deploying to production" -ForegroundColor Red
    exit 1
}
