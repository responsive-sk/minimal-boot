<?php

declare(strict_types=1);

namespace Minimal\User\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Minimal\User\Application\Form\RegistrationForm;
use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Domain\Service\UserService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Registration page handler.
 */
class RegisterHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template,
        private UserService $userService,
        private AuthenticationService $authService
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
            return $this->showRegistrationForm($request);
        }
        
        if ($method === 'POST') {
            return $this->processRegistration($request);
        }

        return new HtmlResponse('Method not allowed', 405);
    }

    private function showRegistrationForm(ServerRequestInterface $request): ResponseInterface
    {
        $form = new RegistrationForm();
        $flashMessages = $this->authService->getFlashMessages();

        $html = $this->template->render('user::register', [
            'title' => 'Register',
            'form' => $form,
            'flash_messages' => $flashMessages,
        ]);

        return new HtmlResponse($html);
    }

    private function processRegistration(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $form = RegistrationForm::fromArray($data);

        if (!$form->validate()) {
            $html = $this->template->render('user::register', [
                'title' => 'Register',
                'form' => $form,
                'flash_messages' => [],
            ]);

            return new HtmlResponse($html, 400);
        }

        try {
            $user = $this->userService->createUser(
                $form->getEmail(),
                $form->getUsername(),
                $form->getPassword(),
                $form->getFirstName(),
                $form->getLastName()
            );

            $this->authService->addFlashMessage(
                'success',
                'Registration successful! Please check your email to verify your account.'
            );

            return new RedirectResponse('/login');
        } catch (\InvalidArgumentException $e) {
            // Handle business logic errors (email taken, etc.)
            $this->authService->addFlashMessage('error', $e->getMessage());
            
            $flashMessages = $this->authService->getFlashMessages();
            
            $html = $this->template->render('user::register', [
                'title' => 'Register',
                'form' => $form,
                'flash_messages' => $flashMessages,
            ]);

            return new HtmlResponse($html, 400);
        } catch (\Exception $e) {
            // Handle unexpected errors
            $this->authService->addFlashMessage('error', 'Registration failed. Please try again.');
            
            $flashMessages = $this->authService->getFlashMessages();
            
            $html = $this->template->render('user::register', [
                'title' => 'Register',
                'form' => $form,
                'flash_messages' => $flashMessages,
            ]);

            return new HtmlResponse($html, 500);
        }
    }
}
