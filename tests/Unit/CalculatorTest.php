<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * Test basic addition.
     */
    public function test_addition(): void
    {
        $result = 2 + 2;
        $this->assertEquals(4, $result);
    }

    /**
     * Test basic subtraction.
     */
    public function test_subtraction(): void
    {
        $result = 10 - 5;
        $this->assertEquals(5, $result);
    }

    /**
     * Test basic multiplication.
     */
    public function test_multiplication(): void
    {
        $result = 3 * 4;
        $this->assertEquals(12, $result);
    }

    /**
     * Test string operations.
     */
    public function test_string_concatenation(): void
    {
        $result = 'Hello ' . 'World';
        $this->assertEquals('Hello World', $result);
    }
}
