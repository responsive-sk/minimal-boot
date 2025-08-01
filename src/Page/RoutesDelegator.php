<?php

declare(strict_types=1);

namespace Minimal\Page;

use Minimal\Page\Handler\DemoHandler;
use Minimal\Page\Handler\GetPageViewHandler;
use Minimal\Page\Handler\IndexHandler;
use Mezzio\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class RoutesDelegator
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $app = $callback();
        assert($app instanceof Application);

        // Main routes
        $app->get('/', [IndexHandler::class], 'page::index');
        $app->get('/demo', [DemoHandler::class], 'page::demo');

        // Dynamic page route with slug parameter
        $app->get('/page/{slug}', [GetPageViewHandler::class], 'page::view');

        // Note: Static routes from config are no longer used
        // All pages are now handled by the dynamic /page/{slug} route

        return $app;
    }
}
