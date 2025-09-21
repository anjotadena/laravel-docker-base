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
     * List all users
     *
     * Returns a paginated list of all users in the system.
     *
     * @tag Users
     * @authenticated
     * @response 200 {"success": true, "message": "Users retrieved successfully", "data": {"users": [{"id": 1, "name": "John Doe", "email": "john@example.com", "created_at": "2025-09-20T16:52:40.000000Z"}]}}
     * @response 401 {"message": "Unauthenticated."}
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
     * Get user by ID
     *
     * Returns detailed information about a specific user.
     *
     * @tag Users
     * @authenticated
     * @urlParam id integer required The ID of the user. Example: 1
     * @response 200 {"success": true, "message": "User retrieved successfully", "data": {"user": {"id": 1, "name": "John Doe", "email": "john@example.com", "created_at": "2025-09-20T16:52:40.000000Z"}}}
     * @response 404 {"success": false, "message": "User not found", "errors": null}
     * @response 401 {"message": "Unauthenticated."}
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
     * Update user
     *
     * Updates user information such as name and email.
     *
     * @tag Users
     * @authenticated
     * @urlParam id integer required The ID of the user. Example: 1
     * @bodyParam name string optional The user's full name. Example: John Updated
     * @bodyParam email string optional The user's email address. Example: john.updated@example.com
     * @response 200 {"success": true, "message": "User updated successfully", "data": {"user": {"id": 1, "name": "John Updated", "email": "john.updated@example.com"}}}
     * @response 404 {"success": false, "message": "User not found", "errors": null}
     * @response 422 {"success": false, "message": "Validation failed", "errors": {"email": ["The email has already been taken."]}}
     * @response 401 {"message": "Unauthenticated."}
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
     * Delete user
     *
     * Permanently removes a user from the system.
     *
     * @tag Users
     * @authenticated
     * @urlParam id integer required The ID of the user to delete. Example: 1
     * @response 200 {"success": true, "message": "User deleted successfully", "data": null}
     * @response 404 {"success": false, "message": "User not found", "errors": null}
     * @response 401 {"message": "Unauthenticated."}
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
     * Search users
     *
     * Search for users by name or email address.
     *
     * @tag Users
     * @authenticated
     * @queryParam q string required The search query to match against name or email. Example: john
     * @response 200 {"success": true, "message": "Search completed successfully", "data": {"users": [{"id": 1, "name": "John Doe", "email": "john@example.com"}]}}
     * @response 400 {"success": false, "message": "Search query is required", "errors": null}
     * @response 401 {"message": "Unauthenticated."}
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
