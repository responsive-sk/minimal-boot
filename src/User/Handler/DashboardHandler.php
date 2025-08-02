<?php

declare(strict_types=1);

namespace Minimal\User\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Minimal\User\Domain\Service\AuthenticationService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * User dashboard handler.
 */
class DashboardHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template,
        private AuthenticationService $authService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->authService->requireAuthentication();
        $flashMessages = $this->authService->getFlashMessages();

        $html = $this->template->render('user::dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
            'flash_messages' => $flashMessages,
        ]);

        return new HtmlResponse($html);
    }
}
