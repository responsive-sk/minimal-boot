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

        // Add simple test route first
        $app->get('/contact-test', function() {
            return new \Laminas\Diactoros\Response\HtmlResponse('<h1>Contact Test Works!</h1>');
        }, 'contact.test');

        // Add Contact routes (temporarily without session middleware for debugging)
        $app->get('/contact', ContactHandler::class, 'contact');
        $app->post('/contact', ContactHandler::class, 'contact.post');

        // Test route
        $app->get('/test-layout', TestHandler::class, 'test.layout');

        return $app;
    }
}
