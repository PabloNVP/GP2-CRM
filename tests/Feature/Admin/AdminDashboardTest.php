<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_route_is_available_for_authenticated_active_admins(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Dashboard de administracion');
    }

    public function test_admin_dashboard_route_returns_forbidden_for_non_admin_users(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    public function test_admin_dashboard_route_redirects_guests_to_login(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect('/login');
    }

    public function test_admin_dashboard_displays_users_metrics_and_role_distribution(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Uno',
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'name' => 'Operador Uno',
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'name' => 'Soporte Uno',
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::INACTIVE->value,
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Total usuarios')
            ->assertSee('Usuarios activos')
            ->assertSee('Usuarios inactivos')
            ->assertSee('Distribucion por Rol')
            ->assertSee('Administrador')
            ->assertSee('Operador')
            ->assertSee('Soporte');
    }
}
