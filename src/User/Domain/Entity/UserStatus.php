<?php

declare(strict_types=1);

namespace Minimal\User\Domain\Entity;

/**
 * User status enumeration.
 */
enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING = 'pending';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
            self::PENDING => 'Pending Verification',
        };
    }

    public function canLogin(): bool
    {
        return $this === self::ACTIVE;
    }
}
