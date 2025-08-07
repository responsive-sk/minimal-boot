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
    /**
     * @param array<string, mixed>|null $options
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): PathAwareStreamWriter
    {
        $options = $options ?? [];

        // Get Paths service from container
        $paths = null;
        if ($container->has(Paths::class)) {
            $pathsService = $container->get(Paths::class);
            $paths = $pathsService instanceof Paths ? $pathsService : null;
        }

        // Extract stream URL from options
        $streamOrUrl = $options['stream'] ?? 'php://output';
        $mode = is_string($options['mode'] ?? null) ? $options['mode'] : 'a';
        $logSeparator = is_string($options['log_separator'] ?? null) ? $options['log_separator'] : null;

        return new PathAwareStreamWriter($streamOrUrl, $mode, $logSeparator, $paths);
    }
}
