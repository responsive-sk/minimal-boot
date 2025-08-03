<?php

declare(strict_types=1);

namespace Minimal\User\Middleware;

use Laminas\Diactoros\Response\RedirectResponse;
use Minimal\User\Domain\Service\AuthenticationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Authentication middleware - requires user to be logged in.
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthenticationService $authService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->authService->isAuthenticated()) {
            // Store the requested URL for redirect after login
            $requestedUrl = (string) $request->getUri();
            $loginUrl = '/login?redirect=' . urlencode($requestedUrl);

            $this->authService->addFlashMessage('warning', 'Please log in to access this page.');

            return new RedirectResponse($loginUrl);
        }

        // Add authenticated user to request attributes for easy access
        $user = $this->authService->getAuthenticatedUser();
        $request = $request->withAttribute('authenticated_user', $user);

        return $handler->handle($request);
    }
}
