<?php

declare(strict_types=1);

namespace Light\Contact;

use Light\Contact\Handler\ContactHandler;
use Light\Contact\Handler\TestHandler;
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

        // Add Contact routes with session middleware
        $app->get('/contact', [
            SessionMiddleware::class,
            ContactHandler::class,
        ], 'contact');

        $app->post('/contact', [
            SessionMiddleware::class,
            ContactHandler::class,
        ], 'contact.post');

        // Test route
        $app->get('/test-layout', TestHandler::class, 'test.layout');

        return $app;
    }
}
