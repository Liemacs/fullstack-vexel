<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_empty_admin_shell(): void
    {
        $this->seed();

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Vexel Admin')
            ->assertSee('Контракты')
            ->assertSee('Данных пока нет')
            ->assertSee('CRUD для участников')
            ->assertDontSee('#019')
            ->assertDontSee('Marco Volta');
    }

    public function test_dashboard_section_pages_are_reachable(): void
    {
        $this->get('/dashboard/members')
            ->assertOk()
            ->assertSee('Участники')
            ->assertSee('Создать участника');

        $this->get('/dashboard/positions')
            ->assertOk()
            ->assertSee('Должности')
            ->assertSee('Создать должность');
    }
}
