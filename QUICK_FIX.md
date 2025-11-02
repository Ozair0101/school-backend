# Quick Fix for API Timeout Issue

## Problem
API requests are timing out even though the server appears to be running.

## Root Cause
1. **API routes were not registered** in `bootstrap/app.php` (FIXED ✅)
2. **Database connection might be hanging** (Most likely issue)

## Immediate Steps to Fix

### Step 1: Restart Your Laravel Server
**IMPORTANT**: After fixing `bootstrap/app.php`, you MUST restart the server:

1. Stop your current server (Ctrl+C in the terminal where it's running)
2. Start it again:
```powershell
cd school-backend
php artisan serve --port=8000 --host=127.0.0.1
```

### Step 2: Check Database Connection
The timeout is most likely caused by database connection issues:

1. **Check if MySQL is running:**
   - Open MySQL Workbench or check Windows Services
   - Service name: `MySQL80` or similar

2. **Verify database credentials in `.env` file:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school
DB_USERNAME=root
DB_PASSWORD=your_password
```

3. **Test database connection:**
```powershell
cd school-backend
php artisan tinker
```
Then in tinker:
```php
DB::connection()->getPdo();
```
If this hangs or errors, your database connection is the problem.

### Step 3: Clear Caches
```powershell
cd school-backend
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Step 4: Verify Server is Responding
Run the diagnostic script:
```powershell
cd school-backend
.\check-server.ps1
```

Or manually test:
```powershell
Invoke-RestMethod -Uri "http://localhost:8000" -Method GET
```

### Step 5: Test API Endpoint
Open browser and go to:
```
http://localhost:8000/api/monthly-exams
```

If you see:
- **JSON response**: ✅ Server is working!
- **Timeout/No response**: ❌ Database connection issue
- **500 error**: Check Laravel logs in `storage/logs/laravel.log`

## What Was Fixed

1. ✅ **bootstrap/app.php**: Added API routes registration
2. ✅ **bootstrap/app.php**: Added CORS middleware
3. ✅ **MonthlyExamController**: Added database connection check with better error handling

## If Still Not Working

1. Check Laravel logs: `school-backend/storage/logs/laravel.log`
2. Check MySQL is actually running
3. Verify database exists: `php artisan migrate:status`
4. Try SQLite instead of MySQL temporarily to test:
   - Change `.env`: `DB_CONNECTION=sqlite`
   - Remove `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`
   - Run: `php artisan migrate`

## Common Errors

**"Connection refused"**: MySQL not running
**"Access denied"**: Wrong database credentials
**"Unknown database"**: Database doesn't exist, run migrations
**Timeout**: Database connection hanging (check MySQL service)

