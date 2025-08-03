<?php

declare(strict_types=1);

namespace Minimal\User\Domain\Entity;

/**
 * User role enumeration.
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case USER = 'user';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::EDITOR => 'Editor',
            self::USER => 'User',
        };
    }

    /**
     * @return array<string>
     */
    public function getPermissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                'user.create',
                'user.read',
                'user.update',
                'user.delete',
                'page.create',
                'page.read',
                'page.update',
                'page.delete',
                'admin.access',
            ],
            self::EDITOR => [
                'page.create',
                'page.read',
                'page.update',
                'page.delete',
            ],
            self::USER => [
                'page.read',
            ],
        };
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions(), true);
    }
}
