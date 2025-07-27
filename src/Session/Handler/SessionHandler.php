<?php

declare(strict_types=1);

namespace Light\Session\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Session handler.
 *
 * Handles Session related requests.
 */
class SessionHandler implements RequestHandlerInterface
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
            'module' => 'Session',
            'title' => 'Session Page',
        ];

        return new HtmlResponse(
            $this->template->render('session::index', $data)
        );
    }
}
