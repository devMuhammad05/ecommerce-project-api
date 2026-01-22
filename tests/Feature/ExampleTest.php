<?php

declare(strict_types=1);

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_api_returns_a_successful_response(): void
    {
        $response = $this->get('/api/v1');

        $response->assertStatus(200);
        $response->assertSee('API is active');
    }
}
