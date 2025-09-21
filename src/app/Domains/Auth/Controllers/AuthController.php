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
     * Register a new user
     *
     * Creates a new user account with the provided credentials and returns
     * an authentication token for immediate use.
     *
     * @tag Authentication
     * @response 201 {"success": true, "message": "User registered successfully", "data": {"user": {"id": 1, "name": "John Doe", "email": "john@example.com"}, "token": "1|abc123..."}}
     * @response 422 {"success": false, "message": "Validation failed", "errors": {"email": ["The email has already been taken."]}}
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
     * Login user
     *
     * Authenticates a user with email and password, returning an access token
     * for API authentication.
     *
     * @tag Authentication
     * @response 200 {"success": true, "message": "Login successful", "data": {"user": {"id": 1, "name": "John Doe", "email": "john@example.com"}, "token": "2|xyz789..."}}
     * @response 422 {"success": false, "message": "The provided credentials are incorrect.", "errors": {"email": ["The provided credentials are incorrect."]}}
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
     * Logout user
     *
     * Revokes the current user's authentication token, effectively logging them out.
     *
     * @tag Authentication
     * @authenticated
     * @response 200 {"success": true, "message": "Logout successful", "data": null}
     * @response 401 {"message": "Unauthenticated."}
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
     * Get current user
     *
     * Returns the authenticated user's profile information.
     *
     * @tag Authentication
     * @authenticated
     * @response 200 {"success": true, "message": "User retrieved successfully", "data": {"user": {"id": 1, "name": "John Doe", "email": "john@example.com", "created_at": "2025-09-20T16:52:40.000000Z"}}}
     * @response 401 {"message": "Unauthenticated."}
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
     * Refresh authentication token
     *
     * Revokes the current token and generates a new one for continued API access.
     *
     * @tag Authentication
     * @authenticated
     * @response 200 {"success": true, "message": "Token refreshed successfully", "data": {"user": {"id": 1, "name": "John Doe", "email": "john@example.com"}, "token": "3|new_token..."}}
     * @response 401 {"message": "Unauthenticated."}
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
