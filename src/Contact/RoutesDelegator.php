<?php

declare(strict_types=1);

namespace Minimal\Contact;

use Minimal\Contact\Handler\ContactHandler;
use Minimal\Contact\Handler\TestHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Session\Middleware\SessionMiddleware;

/**
 * Contact routes delegator.
 *
 * Registers routes for the Contact module.
 */
class RoutesDelegator
{
    /**
     * @param ContainerInterface $container
     * @param string $serviceName
     * @param callable $callback
     * @return Application
     */
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): Application {
        /** @var Application $app */
        $app = $callback();

        // Debug: Log that routes are being registered
        @file_put_contents('var/logs/debug.log', "ContactRoutesDelegator: Registering contact routes\n", FILE_APPEND);

        // Add Contact routes with session middleware
        $app->get('/contact', [
            SessionMiddleware::class,
            ContactHandler::class,
        ], 'contact');

        $app->post('/contact', [
            SessionMiddleware::class,
            ContactHandler::class,
        ], 'contact.post');

        // Test routes
        $app->get('/test-layout', TestHandler::class, 'test.layout');

        // Simple test route without any complex CSS
        $app->get('/test-simple', function() {
            $content = file_get_contents(__DIR__ . '/../../templates/themes/svelte/pages/test-simple.phtml');
            if ($content === false) {
                throw new \RuntimeException('Could not read test template file');
            }
            return new \Laminas\Diactoros\Response\HtmlResponse($content);
        }, 'test.simple');

        return $app;
    }
}
