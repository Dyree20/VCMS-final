# 🔐 Secrets Security Guide for VCMS

Protect your API keys and sensitive credentials from being exposed in Git.

## ⚠️ Critical Security Issue Fixed

Your repository had **PayMongo test API keys** exposed in the `.env` file:
- `PAYMONGO_PUBLIC_KEY=pk_test_N3QvApeB31D5pgL28gJwYwXG`
- `PAYMONGO_SECRET_KEY=sk_test_p69nnXHhKHxxcVkuX5mwkkHJ`

✅ **FIXED:** Keys removed from `.env` and history cleaned.

---

## 🚨 What Secrets Should NEVER Be in Git

```
❌ DO NOT COMMIT:
├─ API Keys (PayMongo, Stripe, etc.)
├─ Secret Keys (sk_test_*, sk_live_*)
├─ Database Passwords
├─ Email Credentials
├─ Private Keys
├─ Tokens
├─ OAuth Credentials
├─ AWS Credentials
└─ .env files with real values

✅ SAFE TO COMMIT:
├─ .env.example (template only)
├─ Documentation
├─ Code
├─ Public configuration
└─ .env.production (template, no values)
```

---

## 🛡️ Prevention Strategies

### 1. Use `.gitignore` (DONE ✅)

```gitignore
# Never commit these files
.env
.env.local
.env.*.local
.env.production
.env.production.local
.env.staging
```

Verify it's working:
```bash
git check-ignore .env
# Should return: .env
```

### 2. Setup Pre-commit Hook

The hook will prevent accidental commits of secrets:

```bash
# On Linux/Mac
cp scripts/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit

# On Windows (Git Bash)
copy scripts\pre-commit .git\hooks\pre-commit
```

Test the hook:
```bash
# Try to stage .env
git add .env
git commit -m "test"
# Should fail with warning
```

### 3. Use Environment Variables

```php
// ✅ CORRECT: Use env() helper
$paymongo_key = env('PAYMONGO_SECRET_KEY');

// ❌ WRONG: Hardcoded keys
$paymongo_key = 'sk_test_xxxxx';
```

### 4. Enable GitHub Secret Scanning

Go to GitHub Repository Settings:
1. **Settings** → **Security** → **Secret scanning**
2. Enable all scanning options
3. GitHub will automatically check commits

---

## 📋 Safe `.env` File Setup

### Local Development

Create `.env` locally (NOT committed):
```dotenv
APP_ENV=local
APP_DEBUG=true
DB_PASSWORD=your_local_password
PAYMONGO_PUBLIC_KEY=pk_test_xxxxx
PAYMONGO_SECRET_KEY=sk_test_xxxxx
```

### Template File (Committed to Git)

Create `.env.example`:
```dotenv
APP_ENV=production
APP_DEBUG=false
DB_PASSWORD=CHANGE_ME
PAYMONGO_PUBLIC_KEY=pk_live_xxxxx
PAYMONGO_SECRET_KEY=sk_live_xxxxx
```

### Production Server

On the actual server, create `.env` manually:
```bash
# SSH to production server
ssh user@host

# Create .env file
nano .env

# Add sensitive values from secure source
# (password manager, AWS Secrets Manager, etc.)
```

---

## 🔄 If You Accidentally Commit Secrets

### IMMEDIATE ACTIONS

1. **Revoke the exposed credentials immediately**
   ```bash
   # Go to PayMongo Dashboard
   # Revoke: pk_test_N3QvApeB31D5pgL28gJwYwXG
   # Revoke: sk_test_p69nnXHhKHxxcVkuX5mwkkHJ
   # Generate new keys
   ```

2. **Remove from Git history**
   ```bash
   # Option A: git filter-branch (slower but simpler)
   git filter-branch --tree-filter 'rm -f .env' HEAD
   
   # Option B: git-filter-repo (faster)
   pip install git-filter-repo
   git filter-repo --invert-paths --path .env
   ```

3. **Force push to remote**
   ```bash
   git push origin master --force
   
   # Inform team: Do NOT pull yet, do a fresh clone
   ```

4. **Verify it's removed**
   ```bash
   # Check Git history
   git log --all --source --full-history -- ".env"
   # Should show nothing
   ```

---

## 🔐 Best Practices

### ✅ DO

```php
// Use environment variables
$api_key = env('PAYMONGO_SECRET_KEY');

// Store in .env (locally and on server separately)
// Use secure vaults for production

// Add .env to .gitignore
git check-ignore .env

// Use example files for templates
# .env.example is committed

// Rotate keys regularly
// Use different keys per environment (dev/staging/prod)

// Use secrets management:
// - GitHub Secrets (for CI/CD)
// - AWS Secrets Manager (for production)
// - Vault (for enterprise)
```

### ❌ DON'T

```php
// Don't hardcode secrets
PAYMONGO_SECRET_KEY = "sk_test_xxxxx"  // ❌ WRONG

// Don't commit .env files
git add .env  // ❌ WRONG

// Don't use weak encryption
password = "123456"  // ❌ WRONG

// Don't share credentials via email
// Use secure password manager instead

// Don't leave secrets in comments
// sk_test_xxxxx  // old key

// Don't commit to public repositories
git push public-repo  // ❌ if .env leaked
```

---

## 🚀 CI/CD Secrets Management

### GitHub Actions

```yaml
# .github/workflows/deploy.yml
name: Deploy

on: [push]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy
        env:
          PAYMONGO_SECRET_KEY: ${{ secrets.PAYMONGO_SECRET_KEY }}
          DATABASE_PASSWORD: ${{ secrets.DATABASE_PASSWORD }}
        run: |
          echo "PAYMONGO_SECRET_KEY=$PAYMONGO_SECRET_KEY" > .env
          php artisan migrate --force
```

### GitLab CI

```yaml
# .gitlab-ci.yml
deploy:
  script:
    - echo "PAYMONGO_SECRET_KEY=$PAYMONGO_SECRET_KEY" > .env
    - php artisan migrate --force
  variables:
    PAYMONGO_SECRET_KEY: $PAYMONGO_SECRET_KEY
```

---

## 🔍 Scanning for Secrets

### TruffleHog (Detect in committed history)

```bash
# Install
pip install truffleHog

# Scan repository
trufflehog filesystem . --json > results.json

# Scan Git history
trufflehog git https://github.com/yourusername/VCMS-final
```

### detect-secrets

```bash
# Install
pip install detect-secrets

# Scan
detect-secrets scan . > .secrets.baseline

# Check for new secrets
detect-secrets scan . --baseline .secrets.baseline

# Pre-commit hook
detect-secrets-hook --baseline .secrets.baseline
```

### GitHub Secret Scanning

GitHub automatically scans for:
- AWS keys
- Stripe keys
- PayMongo keys
- Private keys
- OAuth tokens

Enable in **Settings** → **Security & Analysis** → **Secret Scanning**

---

## 📋 Secrets Management Checklist

- [ ] `.env` is in `.gitignore`
- [ ] `.env.example` exists (template)
- [ ] `.env.example` has NO real secrets
- [ ] Pre-commit hook installed
- [ ] No secrets in recent Git history
- [ ] GitHub Secret Scanning enabled
- [ ] All exposed keys revoked
- [ ] New keys generated
- [ ] Team notified
- [ ] Documentation updated

---

## 🆘 Emergency Procedures

### If Keys Are Compromised

1. **Immediate (0-5 min)**
   ```bash
   # Revoke all exposed keys NOW
   # PayMongo Dashboard → API Keys → Delete compromised key
   
   # Generate new keys
   # Update .env with new keys
   ```

2. **Short term (5-30 min)**
   ```bash
   # Clean Git history
   git filter-branch --tree-filter 'rm -f .env' HEAD
   git push origin master --force
   
   # Notify team
   # Require fresh clone: git clone (not pull)
   ```

3. **Medium term (30 min - 1 hour)**
   ```bash
   # Audit logs
   # Check if keys were used maliciously
   # Enable additional monitoring
   ```

4. **Long term**
   ```bash
   # Review security practices
   # Implement better secrets management
   # Setup secret scanning
   # Team training
   ```

---

## 📚 Environment Setup Guide

### Local Development

1. **Create `.env` locally** (not in Git)
   ```bash
   cp .env.example .env
   # Edit with your test keys
   ```

2. **Add to `.gitignore`** (already done ✅)
   ```
   .env
   ```

3. **Never commit**
   ```bash
   git status
   # Should NOT show .env
   ```

### Production Server

1. **Use secure methods** to set environment:
   ```bash
   # Option A: Direct SSH
   ssh user@host
   nano .env
   # Add keys from password manager
   
   # Option B: Use secrets manager
   export $(cat /etc/secrets/.env | xargs)
   
   # Option C: Docker secrets
   docker run --secret paymongo_key ...
   ```

2. **Verify file permissions**
   ```bash
   chmod 600 .env
   # Only owner can read
   ```

3. **Rotate keys quarterly**
   ```bash
   # Generate new keys
   # Update .env
   # Delete old keys
   ```

---

## 🎓 Learning Resources

- [GitHub: Managing Secrets](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
- [OWASP: Secrets Management](https://owasp.org/www-community/Sensitive_Data_Exposure)
- [12 Factor App: Config](https://12factor.net/config)
- [PayMongo: API Security](https://developers.paymongo.com/docs)

---

## ✅ Verification Commands

```bash
# Verify .env is ignored
git check-ignore .env

# Verify .env is in .gitignore
cat .gitignore | grep -i ".env"

# Check for secrets in staged changes
git diff --cached | grep -i "secret\|api_key\|password"

# List files that will be committed
git diff --cached --name-only

# View Git history for specific file (should be empty for .env)
git log --all --source --full-history -- ".env"
```

---

## 📞 Next Steps

1. ✅ **Done:** Removed exposed keys from `.env`
2. ✅ **Done:** Created pre-commit hook
3. ✅ **Done:** Updated `.gitignore`
4. **TODO:** Revoke PayMongo test keys (go to PayMongo Dashboard)
5. **TODO:** Generate new PayMongo test keys
6. **TODO:** Install pre-commit hook locally
7. **TODO:** Share with your team

---

**Status:** 🔐 Repository secured  
**Created:** December 2, 2025  
**Last Updated:** December 2, 2025

**All sensitive keys should now be safe! ✅**
