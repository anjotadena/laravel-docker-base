<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\DTOs\LoginDto;
use App\Domains\Auth\DTOs\RegisterDto;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user.
     */
    public function register(RegisterDto $dto): array
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password, // User model will automatically hash this
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Authenticate user and return token.
     */
    public function login(LoginDto $dto): array
    {
        if (!Auth::attempt(['email' => $dto->email, 'password' => $dto->password], $dto->remember)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout user by revoking all tokens.
     */
    public function logout(): void
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('User not authenticated');
        }

        // Revoke all tokens for the user
        $user->tokens()->delete();
    }

    /**
     * Get the authenticated user.
     */
    public function getAuthenticatedUser(): User
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('User not authenticated');
        }

        return $user;
    }

    /**
     * Refresh the user's token.
     */
    public function refreshToken(): array
    {
        $user = $this->getAuthenticatedUser();

        // Revoke current token
        $user->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
