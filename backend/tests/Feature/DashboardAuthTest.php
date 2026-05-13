<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get('/dashboard')
            ->assertRedirect(route('dashboard.login'));
    }

    public function test_admin_can_login_with_dashboard_credentials(): void
    {
        $this->post('/dashboard/login', [
            'email' => 'vexel@admin.com',
            'password' => 'vexeladmin1',
        ])->assertRedirect(route('dashboard'));

        $this->assertTrue(session('dashboard_authenticated'));
    }

    public function test_dashboard_rejects_invalid_credentials(): void
    {
        $this->from('/dashboard/login')->post('/dashboard/login', [
            'email' => 'vexel@admin.com',
            'password' => 'wrong-password',
        ])->assertRedirect('/dashboard/login');

        $this->assertFalse((bool) session('dashboard_authenticated'));
    }

    public function test_admin_can_logout(): void
    {
        $this->asDashboardAdmin()
            ->post('/dashboard/logout')
            ->assertRedirect(route('dashboard.login'));

        $this->assertFalse((bool) session('dashboard_authenticated'));
    }
}
