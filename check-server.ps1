# Server Diagnostic Script

Write-Host "=== Laravel Server Diagnostic ===" -ForegroundColor Cyan
Write-Host ""

# Check if server is running
Write-Host "1. Checking if server is running on port 8000..." -ForegroundColor Yellow
$response = $null
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000" -Method GET -TimeoutSec 3 -UseBasicParsing -ErrorAction Stop
    Write-Host "✓ Server is responding on port 8000" -ForegroundColor Green
} catch {
    Write-Host "✗ Server is NOT responding on port 8000" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "To start the server, run:" -ForegroundColor Yellow
    Write-Host "  php artisan serve --port=8000 --host=127.0.0.1" -ForegroundColor Cyan
    exit 1
}

Write-Host ""

# Check API endpoint
Write-Host "2. Testing API endpoint..." -ForegroundColor Yellow
try {
    $apiResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/monthly-exams" -Method GET -TimeoutSec 5 -ErrorAction Stop
    Write-Host "✓ API endpoint is working" -ForegroundColor Green
    Write-Host "  Response: $($apiResponse | ConvertTo-Json -Depth 2)" -ForegroundColor Gray
} catch {
    Write-Host "✗ API endpoint failed" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    
    if ($_.Exception.Message -like "*timeout*") {
        Write-Host ""
        Write-Host "This usually means:" -ForegroundColor Yellow
        Write-Host "  - Database connection is failing/hanging" -ForegroundColor Yellow
        Write-Host "  - MySQL server is not running" -ForegroundColor Yellow
        Write-Host "  - Database credentials in .env are incorrect" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "Check your database connection:" -ForegroundColor Cyan
        Write-Host "  1. Is MySQL running?" -ForegroundColor Cyan
        Write-Host "  2. Check .env file for DB_* settings" -ForegroundColor Cyan
        Write-Host "  3. Run: php artisan migrate" -ForegroundColor Cyan
    }
}

Write-Host ""
Write-Host "=== Diagnostic Complete ===" -ForegroundColor Cyan

