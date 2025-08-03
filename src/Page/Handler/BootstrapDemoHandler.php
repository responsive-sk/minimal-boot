<?php

declare(strict_types=1);

namespace Minimal\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Minimal\Shared\Service\ThemeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BootstrapDemoHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly ThemeService $themeService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Bootstrap theme info
        $themeInfo = [
            'name'        => 'Bootstrap 5',
            'version'     => '5.3.0',
            'description' => 'The world\'s most popular CSS framework for responsive design',
        ];

        // Force Bootstrap theme and get its assets
        $this->themeService->setTheme('bootstrap');
        $cssUrl = $this->themeService->getThemeCssUrl();
        $jsUrl  = $this->themeService->getThemeJsUrl();

        // Use Bootstrap theme template from new structure
        $templateName = 'bootstrap_pages::demo';

        $html = $this->template->render($templateName, [
            'themeInfo' => $themeInfo,
            'cssUrl'    => $cssUrl,
            'jsUrl'     => $jsUrl,
        ]);

        return new HtmlResponse($html);
    }
}
