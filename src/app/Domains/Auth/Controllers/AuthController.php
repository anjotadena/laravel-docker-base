<?php

namespace App\Domains\Auth\Controllers;

use App\Domains\Auth\DTOs\LoginDto;
use App\Domains\Auth\DTOs\RegisterDto;
use App\Domains\Auth\Requests\LoginRequest;
use App\Domains\Auth\Requests\RegisterRequest;
use App\Domains\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Shared\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $dto = RegisterDto::fromArray($request->validated());
            $result = $this->authService->register($dto);

            return ApiResponse::success(
                data: [
                    'user' => $result['user'],
                    'token' => $result['token'],
                ],
                message: 'User registered successfully',
                statusCode: 201
            );
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), $e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::serverError('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = LoginDto::fromArray($request->validated());
            $result = $this->authService->login($dto);

            return ApiResponse::success(
                data: [
                    'user' => $result['user'],
                    'token' => $result['token'],
                ],
                message: 'Login successful'
            );
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), $e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::serverError('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout();

            return ApiResponse::success(
                message: 'Logout successful'
            );
        } catch (\Exception $e) {
            return ApiResponse::serverError('Logout failed: ' . $e->getMessage());
        }
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getAuthenticatedUser();

            return ApiResponse::success(
                data: ['user' => $user],
                message: 'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::unauthorized('User not authenticated');
        }
    }

    /**
     * Refresh user token.
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken();

            return ApiResponse::success(
                data: [
                    'user' => $result['user'],
                    'token' => $result['token'],
                ],
                message: 'Token refreshed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::unauthorized('Token refresh failed');
        }
    }
}
