<?php

declare(strict_types=1);

namespace Minimal\User\Handler;

use Laminas\Diactoros\Response\RedirectResponse;
use Minimal\User\Domain\Service\AuthenticationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Logout handler.
 */
class LogoutHandler implements RequestHandlerInterface
{
    public function __construct(
        private AuthenticationService $authService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->authService->logout();
        
        return new RedirectResponse('/login');
    }
}
