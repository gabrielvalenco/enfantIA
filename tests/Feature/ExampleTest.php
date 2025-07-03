<?php

namespace Tests\Feature;

use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }
}
