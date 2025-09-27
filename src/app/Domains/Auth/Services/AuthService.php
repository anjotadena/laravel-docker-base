<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\DTOs\LoginDto;
use App\Domains\Auth\DTOs\RegisterDto;
use App\Domains\Auth\Exceptions\InvalidCredentialsException;
use App\Domains\Auth\Exceptions\EmailNotVerifiedException;
use App\Domains\Auth\Exceptions\TokenExpiredException;
use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\Events\UserLoggedIn;
use App\Domains\User\Events\UserRegistered;
use App\Domains\User\Exceptions\EmailAlreadyTakenException;
use App\Domains\User\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterDto $dto): array
    {
        // Check if email already exists
        if ($this->userRepository->emailExists($dto->email->value())) {
            throw new EmailAlreadyTakenException($dto->email->value());
        }

        $user = $this->userRepository->create([
            'name' => $dto->name->value(),
            'email' => $dto->email->value(),
            'password' => $dto->password->value(), // User model will automatically hash this
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        // Fire user registered event
        event(new UserRegistered($user));

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
        $user = $this->userRepository->findByEmail($dto->email->value());
        if (!$user) {
            throw new InvalidCredentialsException();
        }

        // Check if password is correct
        if (!Hash::check($dto->password->value(), $user->password)) {
            throw new InvalidCredentialsException();
        }

        // Check if email is verified (if your app requires it)
        if ($user->email_verified_at === null) {
            throw new EmailNotVerifiedException($user->email);
        }

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Fire user logged in event
        event(new UserLoggedIn($user));

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
