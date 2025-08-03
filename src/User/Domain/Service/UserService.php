<?php

declare(strict_types=1);

namespace Minimal\User\Domain\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use Minimal\User\Domain\Entity\User;
use Minimal\User\Domain\Entity\UserRole;
use Minimal\User\Domain\Entity\UserStatus;
use Minimal\User\Domain\Repository\UserRepositoryInterface;
use RuntimeException;

use function bin2hex;
use function filter_var;
use function password_hash;
use function random_bytes;
use function strlen;

/**
 * User domain service.
 */
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Create a new user.
     */
    public function createUser(
        string $email,
        string $username,
        string $password,
        string $firstName,
        string $lastName,
        UserRole $role = UserRole::USER
    ): User {
        $this->validateEmail($email);
        $this->validateUsername($username);
        $this->validatePassword($password);

        if ($this->userRepository->emailExists($email)) {
            throw new InvalidArgumentException('Email is already taken');
        }

        if ($this->userRepository->usernameExists($username)) {
            throw new InvalidArgumentException('Username is already taken');
        }

        $user = new User(
            id: $this->generateId(),
            email: $email,
            username: $username,
            passwordHash: $this->hashPassword($password),
            firstName: $firstName,
            lastName: $lastName,
            role: $role,
            status: UserStatus::PENDING,
            emailVerificationToken: $this->generateToken()
        );

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * Authenticate user with email/username and password.
     */
    public function authenticate(string $emailOrUsername, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($emailOrUsername)
            ?? $this->userRepository->findByUsername($emailOrUsername);

        if (!$user || !$user->verifyPassword($password)) {
            return null;
        }

        if (!$user->getStatus()->canLogin()) {
            throw new RuntimeException('User account is not active');
        }

        // Update last login
        $userWithLogin = $user->withLastLogin();
        $this->userRepository->save($userWithLogin);

        return $userWithLogin;
    }

    /**
     * Verify user email with token.
     */
    public function verifyEmail(string $token): User
    {
        $user = $this->userRepository->findByEmailVerificationToken($token);

        if (!$user) {
            throw new InvalidArgumentException('Invalid verification token');
        }

        $verifiedUser = $user->withEmailVerified();

        // Activate user after email verification
        if ($verifiedUser->getStatus() === UserStatus::PENDING) {
            $verifiedUser = new User(
                $verifiedUser->getId(),
                $verifiedUser->getEmail(),
                $verifiedUser->getUsername(),
                $verifiedUser->getPasswordHash(),
                $verifiedUser->getFirstName(),
                $verifiedUser->getLastName(),
                $verifiedUser->getRole(),
                UserStatus::ACTIVE,
                null,
                $verifiedUser->getEmailVerifiedAt(),
                $verifiedUser->getPasswordResetToken(),
                $verifiedUser->getPasswordResetExpiresAt(),
                $verifiedUser->getLastLoginAt(),
                $verifiedUser->getCreatedAt(),
                $verifiedUser->getUpdatedAt()
            );
        }

        $this->userRepository->save($verifiedUser);

        return $verifiedUser;
    }

    /**
     * Request password reset.
     */
    public function requestPasswordReset(string $email): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null; // Don't reveal if email exists
        }

        $expiresAt = new DateTimeImmutable('+1 hour');
        $userWithToken = $user->withPasswordResetToken($this->generateToken(), $expiresAt);

        $this->userRepository->save($userWithToken);

        return $userWithToken;
    }

    /**
     * Reset password with token.
     */
    public function resetPassword(string $token, string $newPassword): User
    {
        $user = $this->userRepository->findByPasswordResetToken($token);

        if (!$user) {
            throw new InvalidArgumentException('Invalid reset token');
        }

        if ($user->getPasswordResetExpiresAt() < new DateTimeImmutable()) {
            throw new InvalidArgumentException('Reset token has expired');
        }

        $this->validatePassword($newPassword);

        $userWithNewPassword = $user->withUpdatedPassword($this->hashPassword($newPassword));
        $this->userRepository->save($userWithNewPassword);

        return $userWithNewPassword;
    }

    /**
     * Change user password.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): User
    {
        if (!$user->verifyPassword($currentPassword)) {
            throw new InvalidArgumentException('Current password is incorrect');
        }

        $this->validatePassword($newPassword);

        $userWithNewPassword = $user->withUpdatedPassword($this->hashPassword($newPassword));
        $this->userRepository->save($userWithNewPassword);

        return $userWithNewPassword;
    }

    /**
     * Get user by ID.
     */
    public function getUserById(string $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Get user by email.
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get all users.
     *
     * @return array<User>
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }

    private function validateUsername(string $username): void
    {
        if (strlen($username) < 3 || strlen($username) > 50) {
            throw new InvalidArgumentException('Username must be between 3 and 50 characters');
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            throw new InvalidArgumentException('Username can only contain letters, numbers, underscores and hyphens');
        }
    }

    private function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long');
        }
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function generateId(): string
    {
        return 'user_' . bin2hex(random_bytes(16));
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
