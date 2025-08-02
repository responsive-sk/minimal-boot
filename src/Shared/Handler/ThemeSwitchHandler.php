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
            return $this->getCurrentTheme($request);
        }

        return new JsonResponse(['error' => 'Method not allowed'], 405);
    }

    private function switchTheme(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $queryParams = $request->getQueryParams();
        
        // Get theme from POST data or query parameter
        $theme = $data['theme'] ?? $queryParams['theme'] ?? null;
        
        if ($theme) {
            try {
                $this->themeService->setTheme($theme);
                $newTheme = $theme;
            } catch (\InvalidArgumentException $e) {
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
        ]);
    }
}
