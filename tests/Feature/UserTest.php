<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test welcome page returns successful response.
     */
    public function test_welcome_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test application returns HTML content.
     */
    public function test_welcome_page_contains_laravel(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Laravel', false);
    }
}
