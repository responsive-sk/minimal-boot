<?php

declare(strict_types=1);

namespace Minimal\Shared\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Minimal\Shared\Service\ThemeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Theme switch handler.
 */
class ThemeSwitchHandler implements RequestHandlerInterface
{
    public function __construct(
        private ThemeService $themeService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            return $this->switchTheme($request);
        }

        if ($method === 'GET') {
            // Check if theme parameter is provided for switching
            $queryParams = $request->getQueryParams();
            $theme = $queryParams['theme'] ?? null;

            if ($theme && is_string($theme)) {
                // Switch theme via GET request
                return $this->switchTheme($request);
            }

            // Otherwise just return current theme info
            return $this->getCurrentTheme($request);
        }

        return new JsonResponse(['error' => 'Method not allowed'], 405);
    }

    private function switchTheme(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $queryParams = $request->getQueryParams();

        // Ensure data is an array
        if (!is_array($data)) {
            $data = [];
        }

        // Get theme from POST data or query parameter
        $theme = $data['theme'] ?? $queryParams['theme'] ?? null;

        if ($theme && is_string($theme)) {
            try {
                error_log("DEBUG: Attempting to set theme to: {$theme}");
                $this->themeService->setTheme($theme);
                $newTheme = $theme;
                error_log("DEBUG: Theme set successfully to: {$newTheme}");

                // Verify theme was actually set
                $currentTheme = $this->themeService->getCurrentTheme();
                error_log("DEBUG: Current theme after setting: {$currentTheme}");
            } catch (\InvalidArgumentException $e) {
                error_log("DEBUG: Error setting theme: " . $e->getMessage());
                return new JsonResponse(['error' => $e->getMessage()], 400);
            }
        } else {
            // Switch to next theme if no specific theme provided
            $newTheme = $this->themeService->switchToNextTheme();
        }

        // Check if this is an AJAX request
        $isAjax = $request->hasHeader('X-Requested-With') &&
                  $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if ($isAjax) {
            return new JsonResponse([
                'success' => true,
                'theme' => $newTheme,
                'config' => $this->themeService->getCurrentThemeConfig(),
                'debug' => [
                    'requested_theme' => $theme,
                    'new_theme' => $newTheme,
                    'current_theme_after_set' => $this->themeService->getCurrentTheme()
                ]
            ]);
        }

        // Redirect back to the referring page or home
        $referer = $request->getHeaderLine('Referer');
        $redirectUrl = $referer ?: '/';

        return new RedirectResponse($redirectUrl);
    }

    private function getCurrentTheme(ServerRequestInterface $request): ResponseInterface
    {
        $currentTheme = $this->themeService->getCurrentTheme();
        $config = $this->themeService->getCurrentThemeConfig();
        $availableThemes = $this->themeService->getAvailableThemes();

        return new JsonResponse([
            'current' => $currentTheme,
            'config' => $config,
            'available' => $availableThemes,
            'debug' => [
                'session_id' => session_id(),
                'session_started' => session_status() === PHP_SESSION_ACTIVE,
                'method' => $request->getMethod(),
                'query_params' => $request->getQueryParams()
            ]
        ]);
    }
}
