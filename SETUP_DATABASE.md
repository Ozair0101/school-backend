# Database Setup Instructions

## Current Error
The API is returning a 500 error because the MySQL database is not properly configured.

## Quick Fix

### Option 1: Create the Database (Recommended if MySQL is running)

1. **Connect to MySQL:**
   ```bash
   mysql -u root -p
   ```
   (Enter your MySQL password when prompted, or just press Enter if no password is set)

2. **Create the database:**
   ```sql
   CREATE DATABASE school;
   EXIT;
   ```

3. **Run migrations:**
   ```bash
   php artisan migrate
   ```

4. **Seed sample data (optional):**
   ```bash
   php artisan db:seed --class=ExamSystemSeeder
   ```

### Option 2: Start MySQL Server

If MySQL is not running:

**XAMPP:**
- Open XAMPP Control Panel
- Click "Start" next to MySQL

**WAMP:**
- Open WAMP Control Panel
- Click on MySQL → Start Service

**Windows Service:**
- Open Services (Win+R, type `services.msc`)
- Find "MySQL" service
- Right-click → Start

**Standalone MySQL:**
- Run: `net start MySQL` (as Administrator)

### Option 3: Use SQLite (Alternative)

If you prefer SQLite for development:

1. **Enable SQLite extension in php.ini:**
   - Find `php.ini` (run `php --ini` to find location)
   - Uncomment: `;extension=pdo_sqlite` → `extension=pdo_sqlite`
   - Uncomment: `;extension=sqlite3` → `extension=sqlite3`
   - Restart PHP/web server

2. **Update .env:**
   ```
   DB_CONNECTION=sqlite
   ```
   (Remove DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

3. **Create SQLite database:**
   ```bash
   touch database/database.sqlite
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate
   ```

## Verify Setup

After setup, test the API:
```bash
curl http://localhost:8000/api/monthly-exams
```

You should get a JSON response instead of a 500 error.

