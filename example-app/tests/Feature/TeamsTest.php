<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Team;
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

    public function test_if_application_creates_a_new_team()
    {
        $team = Team::factory()->definition();

        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->postJson('/api/team', ['name' => $team['name']]);

        $response->assertStatus(201);
    }

    public function test_if_application_deny_create_a_new_team_if_team_name_already_exists()
    {
        $existing_team = Team::first()->name;

        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->postJson('/api/team', ['name' => $existing_team]);

        $response->assertStatus(400);
    }

    public function test_if_application_can_get_a_existing_team()
    {
        $existing_team = Team::first()->id;

        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->get("/api/team/$existing_team");

        $response->assertStatus(200);
    }

    public function test_if_application_return_not_found_if_try_get_a_non_team()
    {
        $response = $this->withHeaders([
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->get("/api/team/". rand(90, 500));

        $response->assertStatus(400);
    }
}
