<?php

namespace App\Domains\User\Controllers;

use App\Domains\User\Services\UserService;
use App\Http\Controllers\Controller;
use App\Shared\Http\Responses\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Get all users.
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers();

            return ApiResponse::success(
                data: ['users' => $users],
                message: 'Users retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to retrieve users');
        }
    }

    /**
     * Get user by ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);

            return ApiResponse::success(
                data: ['user' => $user],
                message: 'User retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('User not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to retrieve user');
        }
    }

    /**
     * Update user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            ]);

            $user = $this->userService->updateUser($id, $validated);

            return ApiResponse::success(
                data: ['user' => $user],
                message: 'User updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('User not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to update user');
        }
    }

    /**
     * Delete user.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);

            return ApiResponse::success(
                message: 'User deleted successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('User not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to delete user');
        }
    }

    /**
     * Search users.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');

            if (empty($query)) {
                return ApiResponse::error('Search query is required', null, 400);
            }

            $users = $this->userService->searchUsers($query);

            return ApiResponse::success(
                data: ['users' => $users],
                message: 'Search completed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::serverError('Search failed');
        }
    }
}
