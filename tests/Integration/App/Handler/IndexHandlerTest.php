<?php

declare(strict_types=1);

namespace MinimalTest\Integration\App\Handler;

use Laminas\Diactoros\ServerRequest;
use Minimal\App\Handler\IndexHandler;
use Minimal\Shared\Service\ThemeService;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;

/**
 * Integration tests for IndexHandler
 */
class IndexHandlerTest extends TestCase
{
    private IndexHandler $handler;
    private TemplateRendererInterface|MockObject $templateRenderer;
    private ThemeService|MockObject $themeService;

    protected function setUp(): void
    {
        $this->templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $this->themeService = $this->createMock(ThemeService::class);
        
        $this->handler = new IndexHandler(
            $this->templateRenderer,
            $this->themeService
        );
    }

    public function testHandleReturnsHtmlResponse(): void
    {
        // Arrange
        $request = new ServerRequest();
        
        $this->themeService
            ->expects($this->once())
            ->method('getCurrentTheme')
            ->willReturn('bootstrap');
            
        $this->themeService
            ->expects($this->once())
            ->method('getThemeCssUrl')
            ->willReturn('themes/bootstrap/assets/main.css');
            
        $this->themeService
            ->expects($this->once())
            ->method('getThemeJsUrl')
            ->willReturn('themes/bootstrap/assets/main.js');
        
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->with(
                'bootstrap_pages::home',
                $this->callback(function ($data) {
                    return isset($data['title']) 
                        && isset($data['cssUrl']) 
                        && isset($data['jsUrl'])
                        && $data['title'] === 'Home - Mezzio Light Application'
                        && $data['cssUrl'] === 'themes/bootstrap/assets/main.css'
                        && $data['jsUrl'] === 'themes/bootstrap/assets/main.js';
                })
            )
            ->willReturn('<html>Home Page</html>');

        // Act
        $response = $this->handler->handle($request);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/html; charset=utf-8', $response->getHeaderLine('Content-Type'));
    }

    public function testHandleWithTailwindTheme(): void
    {
        // Arrange
        $request = new ServerRequest();
        
        $this->themeService
            ->expects($this->once())
            ->method('getCurrentTheme')
            ->willReturn('tailwind');
            
        $this->themeService
            ->expects($this->once())
            ->method('getThemeCssUrl')
            ->willReturn('themes/main/assets/main.css');
            
        $this->themeService
            ->expects($this->once())
            ->method('getThemeJsUrl')
            ->willReturn('themes/main/assets/main.js');
        
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->with(
                'tailwind_pages::home',
                $this->callback(function ($data) {
                    return $data['cssUrl'] === 'themes/main/assets/main.css'
                        && $data['jsUrl'] === 'themes/main/assets/main.js';
                })
            )
            ->willReturn('<html>Tailwind Home Page</html>');

        // Act
        $response = $this->handler->handle($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHandleIncludesCorrectTemplateData(): void
    {
        // Arrange
        $request = new ServerRequest();
        
        $this->themeService
            ->method('getCurrentTheme')
            ->willReturn('bootstrap');
            
        $this->themeService
            ->method('getThemeCssUrl')
            ->willReturn('themes/bootstrap/assets/main.css');
            
        $this->themeService
            ->method('getThemeJsUrl')
            ->willReturn('themes/bootstrap/assets/main.js');
        
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->with(
                'bootstrap_pages::home',
                $this->callback(function ($data) {
                    // Verify all required template data is present
                    $requiredKeys = ['title', 'description', 'author', 'cssUrl', 'jsUrl'];
                    foreach ($requiredKeys as $key) {
                        if (!isset($data[$key])) {
                            return false;
                        }
                    }
                    
                    // Verify specific values
                    return $data['title'] === 'Home - Mezzio Light Application'
                        && $data['description'] === 'Welcome to Mezzio Light - A modern, fast, and secure PHP application framework'
                        && $data['author'] === 'Dotkernel Team';
                })
            )
            ->willReturn('<html>Test</html>');

        // Act
        $this->handler->handle($request);
    }
}
