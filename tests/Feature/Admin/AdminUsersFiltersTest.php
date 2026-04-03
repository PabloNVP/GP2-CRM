<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Livewire\Admin\IndexUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUsersFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_users_by_name(): void
    {
        User::factory()->create([
            'name' => 'Juan Perez',
            'email' => 'juan.filter@example.com',
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'name' => 'Ana Gomez',
            'email' => 'ana.filter@example.com',
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::test(IndexUsers::class)
            ->set('search', 'juan')
            ->assertSee('Juan Perez')
            ->assertDontSee('Ana Gomez');
    }

    public function test_it_filters_users_by_email(): void
    {
        User::factory()->create([
            'name' => 'Usuario Uno',
            'email' => 'uno.email@example.com',
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'name' => 'Usuario Dos',
            'email' => 'dos.email@example.com',
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::test(IndexUsers::class)
            ->set('search', 'dos.email')
            ->assertSee('Usuario Dos')
            ->assertDontSee('Usuario Uno');
    }

    public function test_it_filters_users_by_role(): void
    {
        User::factory()->create([
            'name' => 'Usuario Operador',
            'email' => 'operador.role@example.com',
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'name' => 'Usuario Soporte',
            'email' => 'soporte.role@example.com',
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::test(IndexUsers::class)
            ->set('roleFilter', RoleEnum::SUPPORT->value)
            ->assertSee('Usuario Soporte')
            ->assertDontSee('Usuario Operador');
    }

    public function test_it_filters_users_by_state(): void
    {
        User::factory()->create([
            'name' => 'Usuario Activo',
            'email' => 'activo.state@example.com',
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'name' => 'Usuario Inactivo',
            'email' => 'inactivo.state@example.com',
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::INACTIVE->value,
        ]);

        Livewire::test(IndexUsers::class)
            ->set('stateFilter', StateEnum::INACTIVE->value)
            ->assertSee('Usuario Inactivo')
            ->assertDontSee('Usuario Activo');
    }

    public function test_it_combines_search_role_and_state_filters(): void
    {
        User::factory()->create([
            'name' => 'Carlos Target',
            'email' => 'carlos.target@example.com',
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'name' => 'Carlos Otro Rol',
            'email' => 'carlos.rol@example.com',
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'name' => 'Carlos Otro Estado',
            'email' => 'carlos.estado@example.com',
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::INACTIVE->value,
        ]);

        Livewire::test(IndexUsers::class)
            ->set('search', 'carlos')
            ->set('roleFilter', RoleEnum::SUPPORT->value)
            ->set('stateFilter', StateEnum::ACTIVE->value)
            ->assertSee('Carlos Target')
            ->assertDontSee('Carlos Otro Rol')
            ->assertDontSee('Carlos Otro Estado');
    }

    public function test_it_resets_pagination_when_filters_change(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);

            User::factory()->create([
                'name' => "Usuario {$number}",
                'email' => "usuario.filtro.{$number}@example.com",
                'role' => RoleEnum::CLIENT->value,
                'state' => StateEnum::ACTIVE->value,
            ]);
        }

        User::factory()->create([
            'name' => 'Usuario Filtro Objetivo',
            'email' => 'objetivo.filtro@example.com',
            'role' => RoleEnum::CLIENT->value,
            'state' => StateEnum::INACTIVE->value,
        ]);

        Livewire::test(IndexUsers::class)
            ->call('gotoPage', 2)
            ->assertSee('Usuario 01')
            ->set('stateFilter', StateEnum::INACTIVE->value)
            ->assertSee('Usuario Filtro Objetivo')
            ->assertDontSee('Usuario 01');
    }
}
