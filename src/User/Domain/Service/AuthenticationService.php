<?php

declare(strict_types=1);

namespace Minimal\User\Domain\Service;

use Minimal\User\Domain\Entity\User;
use ResponsiveSk\Slim4Session\SessionInterface;

/**
 * Authentication service using sessions.
 */
class AuthenticationService
{
    private const SESSION_USER_KEY = 'authenticated_user';
    private const SESSION_USER_ID_KEY = 'user_id';
    private const SESSION_FLASH_KEY = 'flash_messages';

    public function __construct(
        private SessionInterface $session,
        private UserService $userService
    ) {
    }

    /**
     * Login user with email/username and password.
     */
    public function login(string $emailOrUsername, string $password): bool
    {
        try {
            $user = $this->userService->authenticate($emailOrUsername, $password);
            
            if ($user) {
                $this->setAuthenticatedUser($user);
                $this->addFlashMessage('success', 'Successfully logged in!');
                return true;
            }
            
            $this->addFlashMessage('error', 'Invalid credentials.');
            return false;
        } catch (\Exception $e) {
            $this->addFlashMessage('error', $e->getMessage());
            return false;
        }
    }

    /**
     * Logout current user.
     */
    public function logout(): void
    {
        $this->session->remove(self::SESSION_USER_KEY);
        $this->session->remove(self::SESSION_USER_ID_KEY);
        $this->addFlashMessage('success', 'Successfully logged out!');
    }

    /**
     * Check if user is authenticated.
     */
    public function isAuthenticated(): bool
    {
        return $this->session->has(self::SESSION_USER_ID_KEY);
    }

    /**
     * Get current authenticated user.
     */
    public function getAuthenticatedUser(): ?User
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        // Try to get user from session cache first
        $cachedUser = $this->session->get(self::SESSION_USER_KEY);
        if ($cachedUser instanceof User) {
            return $cachedUser;
        }

        // If not cached, load from database
        $userId = $this->session->get(self::SESSION_USER_ID_KEY);
        if ($userId) {
            $user = $this->userService->getUserById($userId);
            if ($user) {
                $this->setAuthenticatedUser($user); // Cache in session
                return $user;
            }
        }

        // User not found, clear session
        $this->logout();
        return null;
    }

    /**
     * Check if current user has permission.
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->getAuthenticatedUser();
        return $user && $user->getRole()->hasPermission($permission);
    }

    /**
     * Check if current user is admin.
     */
    public function isAdmin(): bool
    {
        $user = $this->getAuthenticatedUser();
        return $user && $user->isAdmin();
    }

    /**
     * Require authentication (throws exception if not authenticated).
     */
    public function requireAuthentication(): User
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            throw new \RuntimeException('Authentication required');
        }
        return $user;
    }

    /**
     * Require permission (throws exception if not authorized).
     */
    public function requirePermission(string $permission): User
    {
        $user = $this->requireAuthentication();
        if (!$user->getRole()->hasPermission($permission)) {
            throw new \RuntimeException('Insufficient permissions');
        }
        return $user;
    }

    /**
     * Add flash message.
     */
    public function addFlashMessage(string $type, string $message): void
    {
        $messages = $this->session->get(self::SESSION_FLASH_KEY, []);
        $messages[$type][] = $message;
        $this->session->set(self::SESSION_FLASH_KEY, $messages);
    }

    /**
     * Get and clear flash messages.
     *
     * @return array<string, array<string>>
     */
    public function getFlashMessages(): array
    {
        $messages = $this->session->get(self::SESSION_FLASH_KEY, []);
        $this->session->remove(self::SESSION_FLASH_KEY);
        return $messages;
    }

    /**
     * Get flash messages of specific type.
     *
     * @return array<string>
     */
    public function getFlashMessagesOfType(string $type): array
    {
        $messages = $this->getFlashMessages();
        return $messages[$type] ?? [];
    }

    /**
     * Set authenticated user in session.
     */
    private function setAuthenticatedUser(User $user): void
    {
        $this->session->set(self::SESSION_USER_ID_KEY, $user->getId());
        $this->session->set(self::SESSION_USER_KEY, $user);
    }
}
