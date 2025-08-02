<?php

declare(strict_types=1);

namespace MinimalTest;

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\ServiceManager\ServiceManager;
use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Migration\MigrationRunner;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Container\ContainerInterface;

/**
 * Base Test Case for Minimal Boot Framework
 *
 * Provides common testing utilities and setup for all test classes.
 */
abstract class TestCase extends PHPUnitTestCase
{
    protected ContainerInterface $container;
    protected DatabaseConnectionFactory $databaseFactory;
    protected array $config;

    protected function setUp(): void
    {
        parent::setUp();

        // Load test configuration
        $this->config = $this->loadTestConfig();

        // Create container with test configuration
        $this->container = new ServiceManager($this->config['dependencies'] ?? []);

        // Setup test database
        $this->setupTestDatabase();
    }

    protected function tearDown(): void
    {
        // Clean up test databases
        $this->cleanupTestDatabase();

        parent::tearDown();
    }

    /**
     * Load test configuration
     */
    protected function loadTestConfig(): array
    {
        $configAggregator = new ConfigAggregator([
            new PhpFileProvider('config/autoload/*.global.php'),
            // Override with test-specific config
            function () {
                return [
                    'database' => [
                        'path' => ':memory:',
                        'modules' => [
                            'page' => ':memory:',
                            'contact' => ':memory:',
                            'test' => ':memory:',
                        ],
                    ],
                ];
            },
        ]);

        return $configAggregator->getMergedConfig();
    }

    /**
     * Setup test database with in-memory SQLite
     */
    protected function setupTestDatabase(): void
    {
        $this->databaseFactory = new DatabaseConnectionFactory(':memory:');

        // Run migrations for test modules
        $migrationRunner = new MigrationRunner($this->databaseFactory, 'migrations');

        // Create test database structure
        $this->createTestTables();
    }

    /**
     * Create test database tables
     */
    protected function createTestTables(): void
    {
        // Create pages table for testing
        $pageDb = $this->databaseFactory->getConnection('page');
        $pageDb->exec("
            CREATE TABLE IF NOT EXISTS pages (
                id VARCHAR(255) PRIMARY KEY,
                slug VARCHAR(255) NOT NULL UNIQUE,
                title VARCHAR(500) NOT NULL,
                content TEXT NOT NULL,
                meta_description TEXT DEFAULT '',
                meta_keywords TEXT DEFAULT '',
                is_published BOOLEAN DEFAULT 0,
                published_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL,
                author VARCHAR(255) DEFAULT 'System'
            )
        ");

        // Create test data
        $this->seedTestData();
    }

    /**
     * Seed test database with sample data
     */
    protected function seedTestData(): void
    {
        $pageDb = $this->databaseFactory->getConnection('page');

        // Clear existing data first
        $pageDb->exec("DELETE FROM pages");

        $testPages = [
            [
                'id' => 'test_page_' . uniqid(),
                'slug' => 'test-page-' . uniqid(),
                'title' => 'Test Page',
                'content' => '<h1>Test Page Content</h1><p>This is a test page.</p>',
                'meta_description' => 'Test page description',
                'is_published' => 1,
                'author' => 'Test Author',
            ],
            [
                'id' => 'test_page_' . uniqid(),
                'slug' => 'draft-page-' . uniqid(),
                'title' => 'Draft Page',
                'content' => '<h1>Draft Content</h1>',
                'meta_description' => 'Draft page',
                'is_published' => 0,
                'author' => 'Test Author',
            ],
        ];

        $stmt = $pageDb->prepare("
            INSERT INTO pages (id, slug, title, content, meta_description, is_published, author, created_at)
            VALUES (:id, :slug, :title, :content, :meta_description, :is_published, :author, CURRENT_TIMESTAMP)
        ");

        foreach ($testPages as $page) {
            $stmt->execute($page);
        }
    }

    /**
     * Clean up test database
     */
    protected function cleanupTestDatabase(): void
    {
        if (isset($this->databaseFactory)) {
            $this->databaseFactory->closeConnections();
        }
    }

    /**
     * Get service from container
     */
    protected function getService(string $serviceName): mixed
    {
        return $this->container->get($serviceName);
    }

    /**
     * Create mock HTTP request
     */
    protected function createRequest(string $method = 'GET', string $uri = '/', array $headers = []): \Psr\Http\Message\ServerRequestInterface
    {
        $request = new \Laminas\Diactoros\ServerRequest([], [], $uri, $method);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    /**
     * Assert that response has specific status code
     */
    protected function assertResponseStatus(int $expectedStatus, \Psr\Http\Message\ResponseInterface $response): void
    {
        $this->assertEquals(
            $expectedStatus,
            $response->getStatusCode(),
            sprintf(
                'Expected response status %d, got %d. Response body: %s',
                $expectedStatus,
                $response->getStatusCode(),
                (string) $response->getBody()
            )
        );
    }

    /**
     * Assert that response contains specific content
     */
    protected function assertResponseContains(string $expectedContent, \Psr\Http\Message\ResponseInterface $response): void
    {
        $body = (string) $response->getBody();
        $this->assertStringContainsString(
            $expectedContent,
            $body,
            sprintf('Response body does not contain expected content: %s', $expectedContent)
        );
    }

    /**
     * Assert that response has specific header
     */
    protected function assertResponseHeader(string $headerName, string $expectedValue, \Psr\Http\Message\ResponseInterface $response): void
    {
        $this->assertTrue(
            $response->hasHeader($headerName),
            sprintf('Response does not have header: %s', $headerName)
        );

        $this->assertEquals(
            $expectedValue,
            $response->getHeaderLine($headerName),
            sprintf('Header %s has unexpected value', $headerName)
        );
    }
}
