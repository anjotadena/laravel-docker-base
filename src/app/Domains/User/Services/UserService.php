<?php

namespace App\Domains\User\Services;

use App\Domains\User\Exceptions\UserNotFoundException;
use App\Domains\User\Exceptions\EmailAlreadyTakenException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    /**
     * Get all users.
     */
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    /**
     * Get user by ID.
     */
    public function getUserById(int $id): User
    {
        $user = User::find($id);

        if (!$user) {
            throw new UserNotFoundException("User with ID {$id} not found");
        }

        return $user;
    }

    /**
     * Get user by email.
     */
    public function getUserByEmail(string $email): User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new UserNotFoundException("User with email {$email} not found");
        }

        return $user;
    }

    /**
     * Update user profile.
     */
    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);

        // Check if email is being updated and already exists
        if (isset($data['email']) && $data['email'] !== $user->email) {
            if (User::where('email', $data['email'])->exists()) {
                throw new EmailAlreadyTakenException($data['email']);
            }
        }

        $user->update($data);

        return $user->fresh();
    }

    /**
     * Delete user.
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);

        return $user->delete();
    }

    /**
     * Search users by name or email.
     */
    public function searchUsers(string $query): Collection
    {
        return User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->get();
    }
}
