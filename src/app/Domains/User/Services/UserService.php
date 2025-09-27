<?php

namespace App\Domains\User\Services;

use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\Events\UserUpdated;
use App\Domains\User\Exceptions\UserNotFoundException;
use App\Domains\User\Exceptions\EmailAlreadyTakenException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get all users.
     */
    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * Get user by ID.
     */
    public function getUserById(int $id): User
    {
        $user = $this->userRepository->findById($id);

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
        $user = $this->userRepository->findByEmail($email);

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
            if ($this->userRepository->emailExists($data['email'])) {
                throw new EmailAlreadyTakenException($data['email']);
            }
        }

        $updatedUser = $this->userRepository->update($user, $data);

        // Fire user updated event
        event(new UserUpdated($updatedUser, $data));

        return $updatedUser;
    }

    /**
     * Delete user.
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);

        return $this->userRepository->delete($user);
    }

    /**
     * Search users by name or email.
     */
    public function searchUsers(string $query): Collection
    {
        return $this->userRepository->search($query);
    }
}
