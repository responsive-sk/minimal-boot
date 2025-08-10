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
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Check if HTTPS enforcement is enabled
        if ($this->shouldEnforceHttps($request)) {
            // Redirect to HTTPS if not already secure
            if (!$this->isHttps($request)) {
                $uri = $request->getUri();
                $httpsUri = $uri->withScheme('https')->withPort(443);
                
                $response = new \Laminas\Diactoros\Response();
                return $response
                    ->withStatus(301)
                    ->withHeader('Location', (string) $httpsUri);
            }
        }

        $response = $handler->handle($request);

        // Add security headers
        return $this->addSecurityHeaders($response, $request);
    }

    private function shouldEnforceHttps(ServerRequestInterface $request): bool
    {
        // Don't enforce HTTPS in development or for localhost
        $host = $request->getUri()->getHost();
        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return false;
        }

        // Check environment setting
        return $this->config['enforce_https'] ?? true;
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
        if (!empty($serverParams['SERVER_PORT']) && (int)$serverParams['SERVER_PORT'] === 443) {
            return true;
        }

        return false;
    }

    private function addSecurityHeaders(ResponseInterface $response, ServerRequestInterface $request): ResponseInterface
    {
        $headers = $this->config['security']['headers'] ?? [];
        
        // Default security headers
        $defaultHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ];

        // Merge with config
        $headers = array_merge($defaultHeaders, $headers);

        // Add HSTS header for HTTPS connections
        if ($this->isHttps($request)) {
            $hstsConfig = $this->config['security']['hsts'] ?? [];
            $maxAge = $hstsConfig['max_age'] ?? 31536000; // 1 year
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
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}
