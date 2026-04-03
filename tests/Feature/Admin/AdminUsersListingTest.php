<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Livewire\Admin\IndexUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUsersListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_empty_state_when_there_are_no_users(): void
    {
        Livewire::test(IndexUsers::class)
            ->assertSee('No hay usuarios registrados.');
    }

    public function test_it_displays_required_columns_on_users_listing(): void
    {
        User::factory()->create([
            'name' => 'Usuario Admin',
            'email' => 'admin.listado@example.com',
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
            'created_at' => '2026-03-31 10:00:00',
            'updated_at' => '2026-03-31 10:00:00',
        ]);

        Livewire::test(IndexUsers::class)
            ->assertSee('Nombre')
            ->assertSee('Email')
            ->assertSee('Rol')
            ->assertSee('Estado')
            ->assertSee('Fecha de alta')
            ->assertSee('Acciones')
            ->assertSee('Usuario Admin')
            ->assertSee('admin.listado@example.com')
            ->assertSee('Administrador')
            ->assertSee('Activo')
            ->assertSee('31/03/2026');
    }

    public function test_it_paginates_users_by_ten_records(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);

            User::factory()->create([
                'name' => "Usuario {$number}",
                'email' => "usuario.{$number}@example.com",
                'role' => RoleEnum::CLIENT->value,
                'state' => StateEnum::ACTIVE->value,
            ]);
        }

        Livewire::test(IndexUsers::class)
            ->assertSee('Usuario 11')
            ->assertDontSee('Usuario 01')
            ->call('gotoPage', 2)
            ->assertSee('Usuario 01')
            ->assertDontSee('Usuario 11');
    }

    public function test_admin_users_route_is_available_for_authenticated_active_admins(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Usuarios');
    }

    public function test_admin_users_route_returns_forbidden_for_non_admin_users(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertStatus(403);
    }

    public function test_admin_users_route_redirects_guests_to_login(): void
    {
        $this->get('/admin/users')
            ->assertRedirect('/login');
    }
}
