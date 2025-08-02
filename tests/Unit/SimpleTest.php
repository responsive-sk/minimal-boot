<?php

declare(strict_types=1);

namespace MinimalTest\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Simple test to verify PHPUnit setup
 */
class SimpleTest extends TestCase
{
    public function testTrueIsTrue(): void
    {
        $this->assertTrue(true);
    }

    public function testStringConcatenation(): void
    {
        $result = 'Hello' . ' ' . 'World';
        $this->assertEquals('Hello World', $result);
    }

    public function testArrayCount(): void
    {
        $array = [1, 2, 3, 4, 5];
        $this->assertCount(5, $array);
    }
}
