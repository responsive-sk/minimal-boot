<?php

declare(strict_types=1);

namespace Minimal\User\Middleware;

use Laminas\Diactoros\Response\HtmlResponse;
use Minimal\User\Domain\Service\AuthenticationService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * Permission middleware - requires specific permission.
 */
class PermissionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthenticationService $authService,
        private TemplateRendererInterface $template,
        private string $requiredPermission
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->authService->getAuthenticatedUser();

        if (!$user || !$user->getRole()->hasPermission($this->requiredPermission)) {
            $html = $this->template->render('error::403', [
                'title' => 'Access Denied',
                'message' => 'You do not have permission to access this resource.',
                'required_permission' => $this->requiredPermission,
            ]);

            return new HtmlResponse($html, 403);
        }

        return $handler->handle($request);
    }

    /**
     * Factory method to create middleware with specific permission.
     */
    public static function requirePermission(string $permission): callable
    {
        return function ($container) use ($permission) {
            assert($container instanceof \Psr\Container\ContainerInterface);
            $authService = $container->get(AuthenticationService::class);
            $template = $container->get(TemplateRendererInterface::class);

            assert($authService instanceof AuthenticationService);
            assert($template instanceof TemplateRendererInterface);

            return new self($authService, $template, $permission);
        };
    }
}
