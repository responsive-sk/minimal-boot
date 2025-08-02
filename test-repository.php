<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\ServiceManager\ServiceManager;
use Minimal\Page\Domain\Repository\PageRepositoryInterface;

// Load configuration
$config = (new ConfigAggregator([
    new PhpFileProvider('config/autoload/*.global.php'),
    new PhpFileProvider('config/autoload/*.local.php'),
]))->getMergedConfig();

// Create container
$container = new ServiceManager($config['dependencies']);

// Get repository
$repository = $container->get(PageRepositoryInterface::class);

echo "Repository class: " . get_class($repository) . PHP_EOL;

// Test finding a page
$page = $repository->findBySlug('about');

if ($page) {
    echo "Found page: " . $page->getTitle() . PHP_EOL;
    echo "Content preview: " . substr($page->getContent(), 0, 100) . "..." . PHP_EOL;
} else {
    echo "Page not found!" . PHP_EOL;
}
