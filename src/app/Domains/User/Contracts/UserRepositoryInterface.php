<?php

namespace App\Domains\User\Contracts;

use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Get all users.
     */
    public function getAll(): Collection;

    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User;

    /**
     * Delete a user.
     */
    public function delete(User $user): bool;

    /**
     * Check if email exists.
     */
    public function emailExists(string $email): bool;

    /**
     * Search users by name or email.
     */
    public function search(string $query): Collection;
}
