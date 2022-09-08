<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_if_application_returns_a_team()
    {
        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->postJson('/api/team', ['name' => 'Corinthians']);

        $response->assertStatus(200);
    }
}