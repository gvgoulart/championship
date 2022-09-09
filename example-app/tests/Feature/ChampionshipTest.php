<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Championship;
use Tests\TestCase;

class ChampionshipsTest extends TestCase
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

    public function test_if_application_creates_a_new_championship()
    {
        $championship = Championship::factory()->definition();

        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->postJson('/api/championship', ['name' => $championship['name']]);

        $response->assertStatus(201);
    }

    public function test_if_application_deny_create_a_new_championship_if_championship_name_already_exists()
    {
        $existing_championship = Championship::first()->name;

        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->postJson('/api/championship', ['name' => $existing_championship]);

        $response->assertStatus(400);
    }

    public function test_if_application_can_get_a_existing_championship()
    {
        $existing_championship = Championship::first()->id;

        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->get("/api/championship/$existing_championship");

        $response->assertStatus(200);
    }

    public function test_if_application_return_not_found_if_try_get_a_non_championship()
    {
        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->get("/api/championship/". rand(90, 500));

        $response->assertStatus(400);
    }
}
