# Authentication Setup Guide

## Backend Setup

### 1. Install Laravel Sanctum (if not already installed)

```bash
cd school-backend
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Seed Default User

```bash
php artisan db:seed --class=UserSeeder
```

This will create a default user:
- **Email/Username:** dev@dev.com or developer
- **Password:** dev
- **Role:** admin

### 4. Update User Model

Make sure the User model has the `HasApiTokens` trait:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    // ...
}
```

### 5. Configure Sanctum Middleware

In `bootstrap/app.php`, ensure Sanctum middleware is configured for API routes.

### 6. CORS Configuration

Make sure CORS is properly configured to allow requests from your frontend URL.

## Frontend Setup

The frontend is already configured to:
1. Call `/api/login` endpoint
2. Store the token in localStorage
3. Include token in Authorization header for all requests
4. Protect routes using ProtectedRoute component
5. Redirect to login on 401 errors

## Testing

1. Start the backend server: `php artisan serve`
2. Start the frontend: `npm run dev`
3. Login with:
   - Email: `dev@dev.com` or `developer`
   - Password: `dev`

## Notes

- All API routes are protected except `/login`
- Token is automatically included in request headers
- Protected routes check authentication before rendering
- 401 responses automatically redirect to login page

