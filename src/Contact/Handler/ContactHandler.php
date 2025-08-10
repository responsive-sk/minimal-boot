<?php

declare(strict_types=1);

namespace Minimal\Contact\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Minimal\Shared\Service\ThemeService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ResponsiveSk\Slim4Session\SessionInterface;

/**
 * Contact handler.
 *
 * Handles contact form related requests.
 */
class ContactHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template,
        private ThemeService $themeService
    ) {
    }

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get session from request attributes
        /** @var SessionInterface|null $session */
        $session = $request->getAttribute('session');

        // Handle POST request (form submission)
        if ($request->getMethod() === 'POST') {
            return $this->handleFormSubmission($request, $session);
        }

        // Handle GET request (display form)
        // Get current theme to determine template
        $currentTheme = $this->themeService->getCurrentTheme();
        $templateName = $this->getContactTemplate($currentTheme);

        $data = [
            'module' => 'Contact',
            'title' => 'Contact Us',
            'csrf_token' => $session?->get('csrf_token') ?? $this->generateCsrfToken($session),
            'flash_messages' => $session?->get('flash_messages', []) ?? [],
            'contactInfo' => $this->getContactInfo(),
            'cssUrl' => $this->themeService->getThemeCssUrl(),
            'jsUrl' => $this->themeService->getThemeJsUrl(),
        ];

        // Clear flash messages after displaying
        if ($session) {
            $session->remove('flash_messages');
        }

        return new HtmlResponse(
            $this->template->render($templateName, $data)
        );
    }

    /**
     * Handle form submission.
     */
    private function handleFormSubmission(
        ServerRequestInterface $request,
        ?SessionInterface $session
    ): ResponseInterface {
        $parsedBody = $request->getParsedBody();

        // Ensure parsed body is array
        if (!is_array($parsedBody)) {
            $this->addFlashMessage($session, 'Invalid form data.');
            return $this->redirectToContact();
        }

        // Validate CSRF token
        $csrfToken = $parsedBody['csrf_token'] ?? '';
        if (!is_string($csrfToken) || !$this->validateCsrfToken($csrfToken, $session)) {
            $this->addFlashMessage(
                $session,
                'Invalid security token. Please try again.'
            );
            return $this->redirectToContact();
        }

        // Validate form data
        /** @var array<string, mixed> $parsedBody */
        $errors = $this->validateFormData($parsedBody);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addFlashMessage($session, $error);
            }
            return $this->redirectToContact();
        }

        // Process form (send email, save to database, etc.)
        $this->processContactForm($parsedBody);

        // Add success message
        $this->addFlashMessage(
            $session,
            'Thank you for your message! We\'ll get back to you soon.'
        );

        return $this->redirectToContact();
    }

    /**
     * Generate CSRF token and store in session.
     */
    private function generateCsrfToken(?SessionInterface $session): string
    {
        if (!$session) {
            return '';
        }

        $token = bin2hex(random_bytes(32));
        $session->set('csrf_token', $token);

        return $token;
    }

    /**
     * Validate CSRF token.
     */
    private function validateCsrfToken(string $token, ?SessionInterface $session): bool
    {
        if (!$session) {
            return false;
        }

        $sessionToken = $session->get('csrf_token');
        return is_string($sessionToken) && hash_equals($sessionToken, $token);
    }

    /**
     * Validate form data.
     *
     * @param array<string, mixed> $data
     * @return array<string>
     */
    private function validateFormData(array $data): array
    {
        $errors = [];

        if (empty($data['first-name'])) {
            $errors[] = 'First name is required.';
        }

        if (empty($data['last-name'])) {
            $errors[] = 'Last name is required.';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($data['message'])) {
            $errors[] = 'Message is required.';
        }

        return $errors;
    }

    /**
     * Process contact form (placeholder for actual implementation).
     *
     * @param array<string, mixed> $data
     */
    private function processContactForm(array $data): void
    {
        // TODO: Implement actual form processing
        // - Send email
        // - Save to database
        // - Log the submission

        // For now, just log the submission
        error_log('Contact form submitted: ' . json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * Add flash message to session.
     */
    private function addFlashMessage(?SessionInterface $session, string $message): void
    {
        if (!$session) {
            return;
        }

        $messages = $session->get('flash_messages', []);
        if (!is_array($messages)) {
            $messages = [];
        }
        $messages[] = $message;
        $session->set('flash_messages', $messages);
    }

    /**
     * Redirect to contact page.
     */
    private function redirectToContact(): ResponseInterface
    {
        return new \Laminas\Diactoros\Response\RedirectResponse('/contact');
    }

    /**
     * Get contact information.
     *
     * @return array<string, mixed>
     */
    private function getContactInfo(): array
    {
        return [
            'email' => 'info@responsive.sk',
            'phone' => '+421 123 456 789',
            'address' => [
                'street' => 'Bratislava Street 123',
                'city' => 'Bratislava',
                'country' => 'Slovakia',
            ],
            'social' => [
                [
                    'name' => 'GitHub',
                    'url' => 'https://github.com/responsive-sk',
                    'icon' => 'github',
                ],
                [
                    'name' => 'LinkedIn',
                    'url' => 'https://linkedin.com/company/responsive-sk',
                    'icon' => 'linkedin',
                ],
            ],
        ];
    }

    /**
     * Get appropriate contact template based on theme.
     */
    private function getContactTemplate(string $theme): string
    {
        // List of themes that have contact templates
        $supportedThemes = ['tailwind', 'bootstrap', 'svelte', 'vue', 'react'];

        if (in_array($theme, $supportedThemes, true)) {
            return $theme . '_pages::contact';
        }

        // Fallback to generic contact template
        return 'contact::contact';
    }
}
