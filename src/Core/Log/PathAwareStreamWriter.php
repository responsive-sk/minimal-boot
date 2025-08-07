<?php

declare(strict_types=1);

namespace Minimal\Core\Log;

use Dot\Log\Writer\Stream;
use ResponsiveSk\Slim4Paths\Paths;

/**
 * Stream writer that uses Paths service for resolving file paths
 * This prevents issues with relative paths on shared hosting
 */
class PathAwareStreamWriter extends Stream
{
    private ?Paths $paths;

    public function __construct(mixed $streamOrUrl, ?string $mode = null, ?string $logSeparator = null, ?Paths $paths = null)
    {
        $this->paths = $paths;

        // If stream is a relative path and we have Paths service, resolve it
        if ($this->paths && is_string($streamOrUrl) && !$this->isAbsolutePath($streamOrUrl)) {
            $streamOrUrl = $this->paths->getPath($streamOrUrl);
        }

        parent::__construct($streamOrUrl, $mode, $logSeparator);
    }

    /**
     * Check if path is absolute
     */
    private function isAbsolutePath(string $path): bool
    {
        return $path[0] === '/' || (PHP_OS_FAMILY === 'Windows' && preg_match('/^[A-Z]:/i', $path));
    }
}
