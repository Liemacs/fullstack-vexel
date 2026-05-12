<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VexelApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_vexel_endpoints_return_empty_collections_after_seed(): void
    {
        $this->seed();

        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJson(['status' => 'ok', 'service' => 'vexel-api']);

        $this->getJson('/api/v1/overview')
            ->assertOk()
            ->assertJson([
                'members' => 0,
                'active_contracts' => 0,
                'map_points' => 0,
                'timeline_events' => 0,
            ]);

        $this->getJson('/api/v1/members')
            ->assertOk()
            ->assertExactJson([]);

        $this->getJson('/api/v1/vehicles')
            ->assertOk()
            ->assertExactJson([]);

        $this->getJson('/api/v1/timeline')
            ->assertOk()
            ->assertExactJson([]);
    }
}
