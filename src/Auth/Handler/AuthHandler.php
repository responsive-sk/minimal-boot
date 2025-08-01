<?php

declare(strict_types=1);

namespace Minimal\Auth\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Auth handler.
 *
 * Handles Auth related requests.
 */
class AuthHandler implements RequestHandlerInterface
{
    public function __construct(
        private TemplateRendererInterface $template
    ) {
    }

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = [
            'module' => 'Auth',
            'title' => 'Auth Page',
        ];

        return new HtmlResponse(
            $this->template->render('auth::index', $data)
        );
    }
}
