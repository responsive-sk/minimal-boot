<?php

declare(strict_types=1);

namespace Minimal\User\Domain\Entity;

use DateTimeImmutable;

/**
 * User entity representing a system user.
 */
class User
{
    public function __construct(
        private string $id,
        private string $email,
        private string $username,
        private string $passwordHash,
        private string $firstName,
        private string $lastName,
        private UserRole $role = UserRole::USER,
        private UserStatus $status = UserStatus::ACTIVE,
        private ?string $emailVerificationToken = null,
        private ?DateTimeImmutable $emailVerifiedAt = null,
        private ?string $passwordResetToken = null,
        private ?DateTimeImmutable $passwordResetExpiresAt = null,
        private ?DateTimeImmutable $lastLoginAt = null,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function getPasswordResetExpiresAt(): ?DateTimeImmutable
    {
        return $this->passwordResetExpiresAt;
    }

    public function getLastLoginAt(): ?DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Business logic methods

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function withUpdatedPassword(string $newPasswordHash): self
    {
        return new self(
            $this->id,
            $this->email,
            $this->username,
            $newPasswordHash,
            $this->firstName,
            $this->lastName,
            $this->role,
            $this->status,
            $this->emailVerificationToken,
            $this->emailVerifiedAt,
            null, // Clear password reset token
            null, // Clear password reset expiry
            $this->lastLoginAt,
            $this->createdAt,
            new DateTimeImmutable()
        );
    }

    public function withEmailVerified(): self
    {
        return new self(
            $this->id,
            $this->email,
            $this->username,
            $this->passwordHash,
            $this->firstName,
            $this->lastName,
            $this->role,
            $this->status,
            null, // Clear verification token
            new DateTimeImmutable(),
            $this->passwordResetToken,
            $this->passwordResetExpiresAt,
            $this->lastLoginAt,
            $this->createdAt,
            new DateTimeImmutable()
        );
    }

    public function withPasswordResetToken(string $token, DateTimeImmutable $expiresAt): self
    {
        return new self(
            $this->id,
            $this->email,
            $this->username,
            $this->passwordHash,
            $this->firstName,
            $this->lastName,
            $this->role,
            $this->status,
            $this->emailVerificationToken,
            $this->emailVerifiedAt,
            $token,
            $expiresAt,
            $this->lastLoginAt,
            $this->createdAt,
            new DateTimeImmutable()
        );
    }

    public function withLastLogin(): self
    {
        return new self(
            $this->id,
            $this->email,
            $this->username,
            $this->passwordHash,
            $this->firstName,
            $this->lastName,
            $this->role,
            $this->status,
            $this->emailVerificationToken,
            $this->emailVerifiedAt,
            $this->passwordResetToken,
            $this->passwordResetExpiresAt,
            new DateTimeImmutable(),
            $this->createdAt,
            new DateTimeImmutable()
        );
    }
}
