# Troubleshooting API Timeout Issues

## Problem
Getting timeout errors when making API requests from the frontend, even though the server appears to be running.

## Solution Applied
1. **API Routes Not Registered**: Fixed `bootstrap/app.php` to properly register API routes
2. **CORS Middleware**: Added HandleCors middleware to API routes

## Steps to Verify Server is Running

### 1. Check if Server is Running
Open a new terminal and run:
```powershell
cd school-backend
php artisan serve --port=8000 --host=127.0.0.1
```

You should see:
```
INFO  Server running on [http://127.0.0.1:8000]
```

### 2. Test API Endpoint
Open your browser and navigate to:
```
http://localhost:8000/api/monthly-exams
```

You should see a JSON response or an error message (not a timeout).

### 3. Check Database Connection
The timeout might be caused by database connection issues. Make sure:
- MySQL is running
- Database exists (run migrations if needed)
- `.env` file has correct database credentials

### 4. Clear Laravel Cache
Sometimes cached routes/config can cause issues:
```powershell
cd school-backend
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### 5. Restart the Server
After making changes to `bootstrap/app.php`, you MUST restart the server:
1. Stop the current server (Ctrl+C)
2. Start it again: `php artisan serve --port=8000 --host=127.0.0.1`

## Common Issues

### Port Already in Use
If port 8000 is already in use:
```powershell
# Find process using port 8000
netstat -ano | findstr :8000

# Kill the process (replace PID with actual process ID)
taskkill /PID <PID> /F

# Or use a different port
php artisan serve --port=8001 --host=127.0.0.1
```

Then update frontend `api.ts` to use port 8001.

### Database Connection Issues
If the database connection is failing:
1. Check MySQL is running
2. Verify `.env` database credentials
3. Run: `php artisan migrate` if tables don't exist

### CORS Issues
If you see CORS errors in browser console:
- The fix in `bootstrap/app.php` should handle this
- Make sure to restart the server after changes

## Quick Test
Run this in PowerShell to test the API:
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/monthly-exams" -Method GET
```

If this works, the server is fine and the issue might be with the frontend connection or browser cache.

