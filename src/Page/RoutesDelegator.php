<?php

declare(strict_types=1);

namespace Light\Page;

use Light\Page\Handler\DebugPagesHandler;
use Light\Page\Handler\DemoHandler;
use Light\Page\Handler\GetPageViewHandler;
use Light\Page\Handler\IndexHandler;
use Mezzio\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;
use function is_array;
use function is_string;
use function sprintf;

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
        $app->get('/debug-pages', [DebugPagesHandler::class], 'page::debug');

        // Dynamic page route with slug parameter
        $app->get('/page/{slug}', [GetPageViewHandler::class], 'page::view');

        // Note: Static routes from config are no longer used
        // All pages are now handled by the dynamic /page/{slug} route

        return $app;
    }
}
