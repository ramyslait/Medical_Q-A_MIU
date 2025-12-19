<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PDO;
use PDOStatement;

/**
 * Base test case class with common utilities for controller testing
 * 
 * Provides helper methods for:
 * - Database mocking
 * - Session management
 * - Request simulation
 * - Output buffering
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @var PDO|\PHPUnit\Framework\MockObject\MockObject Mock PDO instance
     */
    protected $mockPdo;

    /**
     * @var PDOStatement|\PHPUnit\Framework\MockObject\MockObject Mock PDOStatement instance
     */
    protected $mockStmt;

    /**
     * @var array Original superglobals backup
     */
    protected $originalSuperglobals = [];

    /**
     * @var int Output buffer level at test start
     */
    private $obLevel;

    /**
     * Set up test environment before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Remember current output buffer level
        $this->obLevel = ob_get_level();

        // Start output buffering to prevent headers already sent errors
        if ($this->obLevel === 0) {
            ob_start();
        }

        // Backup original superglobals
        $this->originalSuperglobals = [
            '_SERVER' => $_SERVER ?? [],
            '_POST' => $_POST ?? [],
            '_GET' => $_GET ?? [],
            '_SESSION' => $_SESSION ?? [],
            '_COOKIE' => $_COOKIE ?? [],
            '_ENV' => $_ENV ?? []
        ];

        // Initialize session only if headers haven't been sent
        if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION = [];

        // Set up default request method
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';

        // Clear POST data
        $_POST = [];
        $_GET = [];
        $_COOKIE = [];

        // Set test environment variables
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_USER'] = 'test';
        $_ENV['DB_PASS'] = 'test';
        $_ENV['DB_NAME'] = 'test_db';
        $_ENV['ENCRYPTION_KEY'] = 'test_encryption_key_32_chars!!';
        $_ENV['GROQ_API_KEY'] = 'test_groq_key';
        $_ENV['MAIL_USERNAME'] = 'test@example.com';
        $_ENV['MAIL_PASSWORD'] = 'test_password';

        // Define PHPUNIT_RUNNING constant to prevent redirects in tests
        if (!defined('PHPUNIT_RUNNING')) {
            define('PHPUNIT_RUNNING', true);
        }

        // Create mock PDO and statement
        $this->createMockDatabase();
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        // Restore original superglobals
        $_SERVER = $this->originalSuperglobals['_SERVER'] ?? [];
        $_POST = $this->originalSuperglobals['_POST'] ?? [];
        $_GET = $this->originalSuperglobals['_GET'] ?? [];
        $_COOKIE = $this->originalSuperglobals['_COOKIE'] ?? [];
        $_ENV = $this->originalSuperglobals['_ENV'] ?? [];

        // Clear session
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_destroy();
        }

        // Restore output buffer to original level
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }

        parent::tearDown();
    }

    /**
     * Create mock PDO and PDOStatement objects
     */
    protected function createMockDatabase(): void
    {
        $this->mockStmt = $this->createMock(PDOStatement::class);
        $this->mockPdo = $this->createMock(PDO::class);

        // Default behavior: prepare returns mock statement
        $this->mockPdo->method('prepare')
            ->willReturn($this->mockStmt);
    }

    /**
     * Simulate a POST request
     * 
     * @param array $data POST data
     */
    protected function simulatePostRequest(array $data): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $data;
    }

    /**
     * Simulate a GET request
     * 
     * @param array $data GET data
     */
    protected function simulateGetRequest(array $data = []): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = $data;
    }

    /**
     * Set session data
     * 
     * @param array $data Session data
     */
    protected function setSession(array $data): void
    {
        $_SESSION = array_merge($_SESSION, $data);
    }

    /**
     * Set cookie data
     * 
     * @param string $name Cookie name
     * @param string $value Cookie value
     */
    protected function setCookie(string $name, string $value): void
    {
        $_COOKIE[$name] = $value;
    }

    /**
     * Mock database query result
     * 
     * @param array|null $result Result data (null for no result)
     * @param int $fetchMode PDO fetch mode
     */
    protected function mockDatabaseResult(?array $result, int $fetchMode = PDO::FETCH_ASSOC): void
    {
        if ($result === null) {
            $this->mockStmt->method('fetch')
                ->willReturn(false);
        } else {
            $this->mockStmt->method('fetch')
                ->with($fetchMode)
                ->willReturn($result);
        }
    }

    /**
     * Mock database execute to return success
     * 
     * @param bool $success Whether execute should succeed
     */
    protected function mockExecute(bool $success = true): void
    {
        $this->mockStmt->method('execute')
            ->willReturn($success);
    }

    /**
     * Mock database rowCount
     * 
     * @param int $count Number of affected rows
     */
    protected function mockRowCount(int $count): void
    {
        $this->mockStmt->method('rowCount')
            ->willReturn($count);
    }

    /**
     * Capture output from a callable
     * 
     * @param callable $callback Function to execute
     * @return string Captured output
     */
    protected function captureOutput(callable $callback): string
    {
        ob_start();
        try {
            $callback();
            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Assert that session contains a key
     * 
     * @param string $key Session key
     * @param string $message Optional message
     */
    protected function assertSessionHas(string $key, string $message = ''): void
    {
        $this->assertArrayHasKey($key, $_SESSION, $message ?: "Session should contain key: $key");
    }

    /**
     * Assert that session contains a specific value
     * 
     * @param string $key Session key
     * @param mixed $value Expected value
     * @param string $message Optional message
     */
    protected function assertSessionEquals(string $key, $value, string $message = ''): void
    {
        $this->assertSessionHas($key, $message);
        $this->assertEquals($value, $_SESSION[$key], $message ?: "Session value for '$key' should match expected value");
    }

    /**
     * Assert redirect header was set
     * 
     * @param string $expectedUrl Expected redirect URL
     */
    protected function assertRedirect(string $expectedUrl): void
    {
        $headers = headers_list();
        $redirectFound = false;

        foreach ($headers as $header) {
            if (stripos($header, 'Location:') === 0) {
                $actualUrl = trim(substr($header, 9));
                $this->assertEquals($expectedUrl, $actualUrl, "Expected redirect to: $expectedUrl");
                $redirectFound = true;
                break;
            }
        }

        $this->assertTrue($redirectFound, "No redirect header found. Expected: $expectedUrl");
    }
}
