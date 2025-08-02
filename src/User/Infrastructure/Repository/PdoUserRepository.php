<?php

declare(strict_types=1);

namespace Minimal\User\Infrastructure\Repository;

use DateTimeImmutable;
use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Query\QueryBuilder;
use Minimal\User\Domain\Entity\User;
use Minimal\User\Domain\Entity\UserRole;
use Minimal\User\Domain\Entity\UserStatus;
use Minimal\User\Domain\Repository\UserRepositoryInterface;
use PDO;

use function assert;

/**
 * PDO implementation of UserRepositoryInterface.
 */
class PdoUserRepository implements UserRepositoryInterface
{
    private PDO $pdo;

    public function __construct(DatabaseConnectionFactory $connectionFactory)
    {
        $this->pdo = $connectionFactory->getConnection('user');
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->pdo);
    }

    public function findById(string $id): ?User
    {
        $data = $this->createQueryBuilder()
            ->table('users')
            ->where('id', '=', $id)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $data = $this->createQueryBuilder()
            ->table('users')
            ->where('email', '=', $email)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByUsername(string $username): ?User
    {
        $data = $this->createQueryBuilder()
            ->table('users')
            ->where('username', '=', $username)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByEmailVerificationToken(string $token): ?User
    {
        $data = $this->createQueryBuilder()
            ->table('users')
            ->where('email_verification_token', '=', $token)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByPasswordResetToken(string $token): ?User
    {
        $data = $this->createQueryBuilder()
            ->table('users')
            ->where('password_reset_token', '=', $token)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findAll(): array
    {
        $results = $this->createQueryBuilder()
            ->table('users')
            ->orderBy('created_at', 'DESC')
            ->get();

        return array_map([$this, 'mapToEntity'], $results);
    }

    public function findByRole(UserRole $role): array
    {
        $results = $this->createQueryBuilder()
            ->table('users')
            ->where('role', '=', $role->value)
            ->orderBy('created_at', 'DESC')
            ->get();

        return array_map([$this, 'mapToEntity'], $results);
    }

    public function findByStatus(UserStatus $status): array
    {
        $results = $this->createQueryBuilder()
            ->table('users')
            ->where('status', '=', $status->value)
            ->orderBy('created_at', 'DESC')
            ->get();

        return array_map([$this, 'mapToEntity'], $results);
    }

    public function save(User $user): void
    {
        $data = $this->mapToArray($user);

        if ($this->exists($user->getId())) {
            $this->createQueryBuilder()
                ->table('users')
                ->where('id', '=', $user->getId())
                ->update($data);
        } else {
            $this->createQueryBuilder()
                ->table('users')
                ->insert($data);
        }
    }

    public function delete(string $id): void
    {
        $this->createQueryBuilder()
            ->table('users')
            ->where('id', '=', $id)
            ->delete();
    }

    public function exists(string $id): bool
    {
        $count = $this->createQueryBuilder()
            ->table('users')
            ->where('id', '=', $id)
            ->count();

        return $count > 0;
    }

    public function emailExists(string $email): bool
    {
        $count = $this->createQueryBuilder()
            ->table('users')
            ->where('email', '=', $email)
            ->count();

        return $count > 0;
    }

    public function usernameExists(string $username): bool
    {
        $count = $this->createQueryBuilder()
            ->table('users')
            ->where('username', '=', $username)
            ->count();

        return $count > 0;
    }

    public function count(): int
    {
        return $this->createQueryBuilder()
            ->table('users')
            ->count();
    }

    public function countByStatus(UserStatus $status): int
    {
        return $this->createQueryBuilder()
            ->table('users')
            ->where('status', '=', $status->value)
            ->count();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function mapToEntity(array $data): User
    {
        assert(is_string($data['id']));
        assert(is_string($data['email']));
        assert(is_string($data['username']));
        assert(is_string($data['password_hash']));
        assert(is_string($data['first_name']));
        assert(is_string($data['last_name']));
        assert(is_string($data['role']));
        assert(is_string($data['status']));

        $createdAt = null;
        if ($data['created_at']) {
            assert(is_string($data['created_at']));
            $createdAt = new DateTimeImmutable($data['created_at']);
        }

        $updatedAt = null;
        if ($data['updated_at']) {
            assert(is_string($data['updated_at']));
            $updatedAt = new DateTimeImmutable($data['updated_at']);
        }

        $emailVerifiedAt = null;
        if ($data['email_verified_at']) {
            assert(is_string($data['email_verified_at']));
            $emailVerifiedAt = new DateTimeImmutable($data['email_verified_at']);
        }

        $passwordResetExpiresAt = null;
        if ($data['password_reset_expires_at']) {
            assert(is_string($data['password_reset_expires_at']));
            $passwordResetExpiresAt = new DateTimeImmutable($data['password_reset_expires_at']);
        }

        $lastLoginAt = null;
        if ($data['last_login_at']) {
            assert(is_string($data['last_login_at']));
            $lastLoginAt = new DateTimeImmutable($data['last_login_at']);
        }

        return new User(
            id: $data['id'],
            email: $data['email'],
            username: $data['username'],
            passwordHash: $data['password_hash'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            role: UserRole::from($data['role']),
            status: UserStatus::from($data['status']),
            emailVerificationToken: $data['email_verification_token'],
            emailVerifiedAt: $emailVerifiedAt,
            passwordResetToken: $data['password_reset_token'],
            passwordResetExpiresAt: $passwordResetExpiresAt,
            lastLoginAt: $lastLoginAt,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function mapToArray(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'password_hash' => $user->getPasswordHash(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'role' => $user->getRole()->value,
            'status' => $user->getStatus()->value,
            'email_verification_token' => $user->getEmailVerificationToken(),
            'email_verified_at' => $user->getEmailVerifiedAt()?->format('Y-m-d H:i:s'),
            'password_reset_token' => $user->getPasswordResetToken(),
            'password_reset_expires_at' => $user->getPasswordResetExpiresAt()?->format('Y-m-d H:i:s'),
            'last_login_at' => $user->getLastLoginAt()?->format('Y-m-d H:i:s'),
            'created_at' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
