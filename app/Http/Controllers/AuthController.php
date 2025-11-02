<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login user and return token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Try to authenticate
        $credentials = $request->only('email', 'password');
        
        // Check if user exists with email or username
        $user = User::where('email', $credentials['email'])
            ->orWhere('username', $credentials['email'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Generate token using Laravel Sanctum
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => [
                    'id' => (string)$user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username ?? $user->email,
                    'role' => $user->role ?? 'admin',
                    'avatar' => $user->avatar ?? null,
                ],
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'data' => [
                'id' => (string)$user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username ?? $user->email,
                'role' => $user->role ?? 'admin',
                'avatar' => $user->avatar ?? null,
            ],
        ], 200);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        try {
            if (method_exists($request, 'user') && $request->user()) {
                $user = $request->user();
                if (method_exists($user, 'currentAccessToken')) {
                    $user->currentAccessToken()->delete();
                }
            }
        } catch (\Exception $e) {
            // Token deletion failed, but still return success
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
}

