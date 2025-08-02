<?php

declare(strict_types=1);

namespace MinimalTest\Unit\User\Domain\Service;

use InvalidArgumentException;
use Minimal\User\Domain\Entity\User;
use Minimal\User\Domain\Entity\UserRole;
use Minimal\User\Domain\Entity\UserStatus;
use Minimal\User\Domain\Repository\UserRepositoryInterface;
use Minimal\User\Domain\Service\UserService;
use MinimalTest\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepositoryInterface|MockObject $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    public function testCreateUserSuccess(): void
    {
        $email = 'test@example.com';
        $username = 'testuser';
        $password = 'password123';
        $firstName = 'John';
        $lastName = 'Doe';

        $this->userRepository
            ->expects($this->once())
            ->method('emailExists')
            ->with($email)
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('usernameExists')
            ->with($username)
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($email, $username, $firstName, $lastName) {
                return $user->getEmail() === $email
                    && $user->getUsername() === $username
                    && $user->getFirstName() === $firstName
                    && $user->getLastName() === $lastName
                    && $user->getRole() === UserRole::USER
                    && $user->getStatus() === UserStatus::PENDING
                    && $user->getEmailVerificationToken() !== null;
            }));

        $user = $this->userService->createUser($email, $username, $password, $firstName, $lastName);

        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
        $this->assertEquals(UserRole::USER, $user->getRole());
        $this->assertEquals(UserStatus::PENDING, $user->getStatus());
        $this->assertNotNull($user->getEmailVerificationToken());
        $this->assertTrue($user->verifyPassword($password));
    }

    public function testCreateUserWithInvalidEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        $this->userService->createUser(
            'invalid-email',
            'testuser',
            'password123',
            'John',
            'Doe'
        );
    }

    public function testCreateUserWithShortPassword(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters long');

        $this->userService->createUser(
            'test@example.com',
            'testuser',
            '123',
            'John',
            'Doe'
        );
    }

    public function testCreateUserWithExistingEmail(): void
    {
        $email = 'test@example.com';

        $this->userRepository
            ->expects($this->once())
            ->method('emailExists')
            ->with($email)
            ->willReturn(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email is already taken');

        $this->userService->createUser(
            $email,
            'testuser',
            'password123',
            'John',
            'Doe'
        );
    }

    public function testCreateUserWithExistingUsername(): void
    {
        $email = 'test@example.com';
        $username = 'testuser';

        $this->userRepository
            ->expects($this->once())
            ->method('emailExists')
            ->with($email)
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('usernameExists')
            ->with($username)
            ->willReturn(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Username is already taken');

        $this->userService->createUser(
            $email,
            $username,
            'password123',
            'John',
            'Doe'
        );
    }

    public function testAuthenticateSuccess(): void
    {
        $email = 'test@example.com';
        $password = 'password123';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $user = new User(
            'user_123',
            $email,
            'testuser',
            $passwordHash,
            'John',
            'Doe',
            UserRole::USER,
            UserStatus::ACTIVE
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $savedUser) {
                return $savedUser->getLastLoginAt() !== null;
            }));

        $authenticatedUser = $this->userService->authenticate($email, $password);

        $this->assertNotNull($authenticatedUser);
        $this->assertEquals($email, $authenticatedUser->getEmail());
        $this->assertNotNull($authenticatedUser->getLastLoginAt());
    }

    public function testAuthenticateWithWrongPassword(): void
    {
        $email = 'test@example.com';
        $password = 'password123';
        $wrongPassword = 'wrongpassword';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $user = new User(
            'user_123',
            $email,
            'testuser',
            $passwordHash,
            'John',
            'Doe',
            UserRole::USER,
            UserStatus::ACTIVE
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($user);

        $authenticatedUser = $this->userService->authenticate($email, $wrongPassword);

        $this->assertNull($authenticatedUser);
    }

    public function testAuthenticateWithNonExistentUser(): void
    {
        $email = 'nonexistent@example.com';
        $password = 'password123';

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn(null);

        $this->userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with($email)
            ->willReturn(null);

        $authenticatedUser = $this->userService->authenticate($email, $password);

        $this->assertNull($authenticatedUser);
    }
}
