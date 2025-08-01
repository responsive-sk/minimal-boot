<?php

declare(strict_types=1);

namespace Minimal\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BootstrapDemoHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template
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

        // Vite compiled assets for Bootstrap
        $cssUrl = '/themes/bootstrap/assets/main.css';
        $jsUrl  = '/themes/bootstrap/assets/main.js';

        $html = $this->template->render('page::bootstrap-demo', [
            'themeInfo' => $themeInfo,
            'cssUrl'    => $cssUrl,
            'jsUrl'     => $jsUrl,
        ]);

        return new HtmlResponse($html);
    }
}
