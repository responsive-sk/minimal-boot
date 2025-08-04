<?php

declare(strict_types=1);

namespace Minimal\User\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Minimal\Shared\Service\ThemeService;
use Minimal\User\Application\Form\LoginForm;
use Minimal\User\Domain\Service\AuthenticationService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Login page handler.
 */
class LoginHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template,
        private AuthenticationService $authService,
        private ThemeService $themeService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Redirect if already authenticated
        if ($this->authService->isAuthenticated()) {
            return new RedirectResponse('/dashboard');
        }

        $method = $request->getMethod();

        if ($method === 'GET') {
            return $this->showLoginForm($request);
        }

        if ($method === 'POST') {
            return $this->processLogin($request);
        }

        return new HtmlResponse('Method not allowed', 405);
    }

    private function showLoginForm(ServerRequestInterface $request): ResponseInterface
    {
        $form = new LoginForm();
        $flashMessages = $this->authService->getFlashMessages();

        $html = $this->template->render('user::login', [
            'title' => 'Login',
            'form' => $form,
            'flash_messages' => $flashMessages,
            'cssUrl' => $this->themeService->getThemeCssUrl(),
            'jsUrl' => $this->themeService->getThemeJsUrl(),
        ]);

        return new HtmlResponse($html);
    }

    private function processLogin(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        if (!is_array($data)) {
            $data = [];
        }
        /** @var array<string, mixed> $data */
        $form = LoginForm::fromArray($data);

        if (!$form->validate()) {
            $html = $this->template->render('user::login', [
                'title' => 'Login',
                'form' => $form,
                'flash_messages' => [],
            ]);

            return new HtmlResponse($html, 400);
        }

        $success = $this->authService->login(
            $form->getEmailOrUsername(),
            $form->getPassword()
        );

        if ($success) {
            // Get redirect URL from query parameter or default to dashboard
            $redirectUrl = $request->getQueryParams()['redirect'] ?? '/dashboard';
            $redirectUrl = is_string($redirectUrl) ? $redirectUrl : '/dashboard';
            return new RedirectResponse($redirectUrl);
        }

        // Login failed, show form with flash messages
        $flashMessages = $this->authService->getFlashMessages();

        $html = $this->template->render('user::login', [
            'title' => 'Login',
            'form' => $form,
            'flash_messages' => $flashMessages,
        ]);

        return new HtmlResponse($html, 400);
    }
}
