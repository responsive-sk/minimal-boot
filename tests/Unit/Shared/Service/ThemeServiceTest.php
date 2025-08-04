<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Shared\Service;

use Minimal\Shared\Service\ThemeService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use ResponsiveSk\Slim4Session\SessionInterface;

/**
 * Unit tests for ThemeService
 */
class ThemeServiceTest extends TestCase
{
    private ThemeService $themeService;
    private SessionInterface|MockObject $session;

    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionInterface::class);
        $this->themeService = new ThemeService($this->session);
    }

    public function testGetCurrentThemeReturnsDefaultWhenNoSessionSet(): void
    {
        $this->session
            ->expects($this->once())
            ->method('get')
            ->with('selected_theme', 'tailwind')
            ->willReturn('tailwind');

        $theme = $this->themeService->getCurrentTheme();
        $this->assertEquals('tailwind', $theme);
    }

    public function testSetThemeUpdatesCurrentTheme(): void
    {
        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('selected_theme', 'tailwind');

        $this->session
            ->expects($this->once())
            ->method('get')
            ->with('selected_theme', 'tailwind')
            ->willReturn('tailwind');

        $this->themeService->setTheme('tailwind');
        $theme = $this->themeService->getCurrentTheme();
        $this->assertEquals('tailwind', $theme);
    }

    public function testSetInvalidThemeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid theme: invalid-theme');

        $this->themeService->setTheme('invalid-theme');
    }

    public function testIsValidThemeReturnsTrueForValidThemes(): void
    {
        $this->assertTrue($this->themeService->isValidTheme('bootstrap'));
        $this->assertTrue($this->themeService->isValidTheme('tailwind'));
    }

    public function testIsValidThemeReturnsFalseForInvalidThemes(): void
    {
        $this->assertFalse($this->themeService->isValidTheme('invalid'));
        $this->assertFalse($this->themeService->isValidTheme(''));
    }

    public function testGetThemeCssUrlReturnsCorrectUrlForBootstrap(): void
    {
        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('selected_theme', 'bootstrap');

        $this->session
            ->expects($this->once())
            ->method('get')
            ->with('selected_theme', 'tailwind')
            ->willReturn('bootstrap');

        $this->themeService->setTheme('bootstrap');
        $cssUrl = $this->themeService->getThemeCssUrl();
        $this->assertEquals('themes/bootstrap/assets/main.css', $cssUrl);
    }

    public function testGetThemeCssUrlReturnsCorrectUrlForTailwind(): void
    {
        $this->themeService->setTheme('tailwind');
        $cssUrl = $this->themeService->getThemeCssUrl();
        $this->assertEquals('themes/main/assets/main.css', $cssUrl);
    }

    public function testGetThemeJsUrlReturnsCorrectUrlForBootstrap(): void
    {
        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('selected_theme', 'bootstrap');

        $this->session
            ->expects($this->once())
            ->method('get')
            ->with('selected_theme', 'tailwind')
            ->willReturn('bootstrap');

        $this->themeService->setTheme('bootstrap');
        $jsUrl = $this->themeService->getThemeJsUrl();
        $this->assertEquals('themes/bootstrap/assets/main.js', $jsUrl);
    }

    public function testGetThemeJsUrlReturnsCorrectUrlForTailwind(): void
    {
        $this->themeService->setTheme('tailwind');
        $jsUrl = $this->themeService->getThemeJsUrl();
        $this->assertEquals('themes/main/assets/main.js', $jsUrl);
    }

    public function testGetAvailableThemesReturnsAllThemes(): void
    {
        $themes = $this->themeService->getAvailableThemes();
        
        $this->assertIsArray($themes);
        $this->assertArrayHasKey('bootstrap', $themes);
        $this->assertArrayHasKey('tailwind', $themes);
        
        // Check bootstrap theme structure
        $this->assertArrayHasKey('name', $themes['bootstrap']);
        $this->assertArrayHasKey('css', $themes['bootstrap']);
        $this->assertArrayHasKey('js', $themes['bootstrap']);
        $this->assertArrayHasKey('description', $themes['bootstrap']);
        
        // Check tailwind theme structure
        $this->assertArrayHasKey('name', $themes['tailwind']);
        $this->assertArrayHasKey('css', $themes['tailwind']);
        $this->assertArrayHasKey('js', $themes['tailwind']);
        $this->assertArrayHasKey('description', $themes['tailwind']);
    }

    public function testThemeNamesAreCorrect(): void
    {
        $themes = $this->themeService->getAvailableThemes();
        
        $this->assertEquals('Bootstrap 5', $themes['bootstrap']['name']);
        $this->assertEquals('Tailwind CSS', $themes['tailwind']['name']);
    }

    public function testThemeDescriptionsExist(): void
    {
        $themes = $this->themeService->getAvailableThemes();
        
        $this->assertNotEmpty($themes['bootstrap']['description']);
        $this->assertNotEmpty($themes['tailwind']['description']);
    }
}
