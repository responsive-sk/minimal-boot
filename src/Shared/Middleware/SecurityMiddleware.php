<?php

declare(strict_types=1);

namespace Minimal\Shared\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Security middleware for HTTPS enforcement and security headers.
 */
class SecurityMiddleware implements MiddlewareInterface
{
    /** @var array<string, mixed> */
    private array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            // Debug: Log SecurityMiddleware execution
            @file_put_contents('var/logs/debug.log', "SecurityMiddleware: Processing request for " . $request->getUri()->getPath() . "\n", FILE_APPEND);

            // Check if HTTPS enforcement is enabled
            if ($this->shouldEnforceHttps($request)) {
                @file_put_contents('var/logs/debug.log', "SecurityMiddleware: HTTPS enforcement enabled\n", FILE_APPEND);
                // Redirect to HTTPS if not already secure
                if (!$this->isHttps($request)) {
                    @file_put_contents('var/logs/debug.log', "SecurityMiddleware: Redirecting to HTTPS\n", FILE_APPEND);
                    $uri = $request->getUri();
                    $httpsUri = $uri->withScheme('https')->withPort(443);

                    $response = new \Laminas\Diactoros\Response();
                    return $response
                        ->withStatus(301)
                        ->withHeader('Location', (string) $httpsUri);
                }
            } else {
                @file_put_contents('var/logs/debug.log', "SecurityMiddleware: HTTPS enforcement disabled for localhost\n", FILE_APPEND);
            }

            $response = $handler->handle($request);
            @file_put_contents('var/logs/debug.log', "SecurityMiddleware: Handler completed, adding security headers\n", FILE_APPEND);

            // Add security headers
            return $this->addSecurityHeaders($response, $request);
        } catch (\Throwable $e) {
            @file_put_contents('var/logs/debug.log', "SecurityMiddleware: ERROR - " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }

    private function shouldEnforceHttps(ServerRequestInterface $request): bool
    {
        // Don't enforce HTTPS in development or for localhost
        $host = $request->getUri()->getHost();
        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return false;
        }

        // Check environment setting
        $enforceHttps = $this->config['enforce_https'] ?? true;
        return is_bool($enforceHttps) ? $enforceHttps : true;
    }

    private function isHttps(ServerRequestInterface $request): bool
    {
        $uri = $request->getUri();
        
        // Check URI scheme
        if ($uri->getScheme() === 'https') {
            return true;
        }

        // Check server variables for proxy/load balancer scenarios
        $serverParams = $request->getServerParams();
        
        // Standard HTTPS check
        if (!empty($serverParams['HTTPS']) && $serverParams['HTTPS'] !== 'off') {
            return true;
        }

        // Check forwarded protocol (common with reverse proxies)
        if (!empty($serverParams['HTTP_X_FORWARDED_PROTO']) && $serverParams['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }

        // Check port
        if (!empty($serverParams['SERVER_PORT'])) {
            $port = is_numeric($serverParams['SERVER_PORT']) ? (int)$serverParams['SERVER_PORT'] : 0;
            if ($port === 443) {
                return true;
            }
        }

        return false;
    }

    private function addSecurityHeaders(ResponseInterface $response, ServerRequestInterface $request): ResponseInterface
    {
        $configHeaders = $this->config['security']['headers'] ?? [];
        $configHeaders = is_array($configHeaders) ? $configHeaders : [];

        // Default security headers
        $defaultHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ];

        // Merge with config
        $headers = array_merge($defaultHeaders, $configHeaders);

        // Add HSTS header for HTTPS connections
        if ($this->isHttps($request)) {
            $hstsConfig = $this->config['security']['hsts'] ?? [];
            $hstsConfig = is_array($hstsConfig) ? $hstsConfig : [];
            $maxAge = $hstsConfig['max_age'] ?? 31536000; // 1 year
            $maxAge = is_numeric($maxAge) ? (int)$maxAge : 31536000;
            $includeSubdomains = $hstsConfig['include_subdomains'] ?? true;
            $preload = $hstsConfig['preload'] ?? false;

            $hstsValue = "max-age={$maxAge}";
            if ($includeSubdomains) {
                $hstsValue .= '; includeSubDomains';
            }
            if ($preload) {
                $hstsValue .= '; preload';
            }

            $headers['Strict-Transport-Security'] = $hstsValue;
        }

        // Apply headers to response
        foreach ($headers as $name => $value) {
            if (is_string($name) && is_string($value)) {
                $response = $response->withHeader($name, $value);
            } elseif (is_string($name) && is_array($value)) {
                // Ensure array contains only strings
                $stringValues = array_filter($value, 'is_string');
                if (!empty($stringValues)) {
                    $response = $response->withHeader($name, $stringValues);
                }
            }
        }

        return $response;
    }
}
