<?php

declare(strict_types=1);

namespace Minimal\Core\Factory;

use Minimal\Core\Log\PathAwareStreamWriter;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Paths\Paths;

/**
 * Factory for PathAwareStreamWriter that injects Paths service
 */
class PathAwareStreamWriterFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): PathAwareStreamWriter
    {
        $options = $options ?? [];
        
        // Get Paths service from container
        $paths = $container->has(Paths::class) ? $container->get(Paths::class) : null;
        
        // Extract stream URL from options
        $streamOrUrl = $options['stream'] ?? 'php://output';
        $mode = $options['mode'] ?? 'a';
        $logSeparator = $options['log_separator'] ?? null;
        
        return new PathAwareStreamWriter($streamOrUrl, $mode, $logSeparator, $paths);
    }
}
