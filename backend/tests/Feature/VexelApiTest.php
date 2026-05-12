<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VexelApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_vexel_endpoints_return_seeded_data(): void
    {
        $this->seed();

        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJson(['status' => 'ok', 'service' => 'vexel-api']);

        $this->getJson('/api/v1/overview')
            ->assertOk()
            ->assertJson([
                'members' => 3,
                'active_contracts' => 1,
                'map_points' => 5,
                'timeline_events' => 5,
            ]);

        $this->getJson('/api/v1/members/marco-volta')
            ->assertOk()
            ->assertJsonPath('callSign', 'Volta')
            ->assertJsonPath('skills.0.name', 'Вождение грузового транспорта');

        $this->getJson('/api/v1/vehicles')
            ->assertOk()
            ->assertJsonPath('0.category', 'Грузовики')
            ->assertJsonPath('0.items.0.name', 'V-13 Mule');

        $this->getJson('/api/v1/timeline')
            ->assertOk()
            ->assertJsonPath('3.year', '2039')
            ->assertJsonPath('3.chapters.2.chapter', '03');
    }
}
