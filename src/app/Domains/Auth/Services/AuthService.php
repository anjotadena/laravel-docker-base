<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\DTOs\LoginDto;
use App\Domains\Auth\DTOs\RegisterDto;
use App\Domains\Auth\Exceptions\InvalidCredentialsException;
use App\Domains\Auth\Exceptions\EmailNotVerifiedException;
use App\Domains\Auth\Exceptions\TokenExpiredException;
use App\Domains\User\Exceptions\EmailAlreadyTakenException;
use App\Domains\User\Exceptions\UserNotFoundException;
use App\Domains\User\Models\User;
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
        // Check if email already exists
        if (User::where('email', $dto->email)->exists()) {
            throw new EmailAlreadyTakenException($dto->email);
        }

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
        // Check if user exists first
        $user = User::where('email', $dto->email)->first();
        if (!$user) {
            throw new InvalidCredentialsException();
        }

        // Check if password is correct
        if (!Hash::check($dto->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        // Check if email is verified (if your app requires it)
        if ($user->email_verified_at === null) {
            throw new EmailNotVerifiedException($user->email);
        }

        // Create token
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
            throw new UserNotFoundException('User not authenticated');
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
            throw new UserNotFoundException('User not authenticated');
        }

        return $user;
    }

    /**
     * Refresh the user's token.
     */
    public function refreshToken(): array
    {
        $user = $this->getAuthenticatedUser();

        /** @var \Laravel\\PersonalAccessToken $currentToken */
        $currentToken = $user->currentAccessToken();
        
        // Revoke current 
        if ($currentToken) {
            $currentToken->delete();
        }

        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
