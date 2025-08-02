<?php

declare(strict_types=1);

namespace Minimal\User\Domain\Repository;

use Minimal\User\Domain\Entity\User;
use Minimal\User\Domain\Entity\UserRole;
use Minimal\User\Domain\Entity\UserStatus;

/**
 * User repository interface.
 */
interface UserRepositoryInterface
{
    /**
     * Find user by ID.
     */
    public function findById(string $id): ?User;

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by username.
     */
    public function findByUsername(string $username): ?User;

    /**
     * Find user by email verification token.
     */
    public function findByEmailVerificationToken(string $token): ?User;

    /**
     * Find user by password reset token.
     */
    public function findByPasswordResetToken(string $token): ?User;

    /**
     * Find all users.
     *
     * @return array<User>
     */
    public function findAll(): array;

    /**
     * Find users by role.
     *
     * @return array<User>
     */
    public function findByRole(UserRole $role): array;

    /**
     * Find users by status.
     *
     * @return array<User>
     */
    public function findByStatus(UserStatus $status): array;

    /**
     * Save user (create or update).
     */
    public function save(User $user): void;

    /**
     * Delete user by ID.
     */
    public function delete(string $id): void;

    /**
     * Check if user exists by ID.
     */
    public function exists(string $id): bool;

    /**
     * Check if email is already taken.
     */
    public function emailExists(string $email): bool;

    /**
     * Check if username is already taken.
     */
    public function usernameExists(string $username): bool;

    /**
     * Count total users.
     */
    public function count(): int;

    /**
     * Count users by status.
     */
    public function countByStatus(UserStatus $status): int;
}
