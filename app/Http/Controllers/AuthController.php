<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create token with default scopes
        $token = $user->createToken('Personal Access Token', ['read-movies']);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $token->token->expires_at,
            ],
        ], 201);
    }

    /**
     * User login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'scopes' => 'array', // Optional scopes
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        
        // Define scopes based on user or request
        $scopes = $request->input('scopes', ['read-movies']);
        
        // You can add logic here to determine scopes based on user role
        // For example, if user is admin, add admin scope
        if ($user->email === 'admin@example.com') {
            $scopes = ['read-movies', 'write-movies', 'delete-movies', 'admin'];
        }

        $token = $user->createToken('Personal Access Token', $scopes);

        return response()->json([
            'success' => true,
            'message' => 'User logged in successfully',
            'data' => [
                'user' => $user,
                'access_token' => $token->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $token->token->expires_at,
                'scopes' => $scopes,
            ],
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Revoke all tokens for the user
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out successfully',
        ]);
    }

    /**
     * Refresh token (simplified version)
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        
        // Create new token with default scopes
        $scopes = ['read-movies'];
        
        // Add admin scopes if user is admin
        if ($user->email === 'admin@example.com') {
            $scopes = ['read-movies', 'write-movies', 'delete-movies', 'admin'];
        }
        
        // Create new token
        $newToken = $user->createToken('Personal Access Token', $scopes);

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'access_token' => $newToken->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $newToken->token->expires_at,
                'scopes' => $scopes,
            ],
        ]);
    }
}
