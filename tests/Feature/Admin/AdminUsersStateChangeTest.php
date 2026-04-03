<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Livewire\Admin\IndexUsers;
use App\Models\User;
use App\Support\AdminUserSecurityMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUsersStateChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_deactivate_other_user_from_listing_with_confirmation_modal(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $targetUser = User::factory()->create([
            'name' => 'Usuario Objetivo',
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openStateModal', $targetUser->id, StateEnum::INACTIVE->value)
            ->assertSet('isStateModalVisible', true)
            ->call('confirmStateChange')
            ->assertSet('isStateModalVisible', false)
            ->assertSee('Estado de usuario actualizado correctamente.');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'state' => StateEnum::INACTIVE->value,
        ]);
    }

    public function test_admin_can_activate_inactive_user_from_listing_with_confirmation_modal(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $targetUser = User::factory()->create([
            'state' => StateEnum::INACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openStateModal', $targetUser->id, StateEnum::ACTIVE->value)
            ->assertSet('isStateModalVisible', true)
            ->call('confirmStateChange')
            ->assertSet('isStateModalVisible', false)
            ->assertSee('Estado de usuario actualizado correctamente.');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'state' => StateEnum::ACTIVE->value,
        ]);
    }

    public function test_it_rejects_state_change_when_state_value_is_invalid(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $targetUser = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openStateModal', $targetUser->id, 'estado-invalido')
            ->assertSet('isStateModalVisible', false)
            ->assertSee('El estado seleccionado no es valido.');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'state' => StateEnum::ACTIVE->value,
        ]);
    }

    public function test_it_rejects_state_change_when_target_user_does_not_exist(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openStateModal', 999999, StateEnum::INACTIVE->value)
            ->assertSet('isStateModalVisible', false)
            ->assertSee('El usuario seleccionado no existe.');
    }

    public function test_it_blocks_self_deactivation_for_admin_user(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openStateModal', $admin->id, StateEnum::INACTIVE->value)
            ->assertSet('isStateModalVisible', false)
            ->assertSee('No puede desactivarse a si mismo.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'state' => StateEnum::ACTIVE->value,
        ]);
    }

    public function test_it_blocks_deactivation_of_last_active_admin(): void
    {
        $actorAdmin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::INACTIVE->value,
        ]);

        $targetAdmin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($actorAdmin)
            ->test(IndexUsers::class)
            ->call('openStateModal', $targetAdmin->id, StateEnum::INACTIVE->value)
            ->assertSet('isStateModalVisible', true)
            ->call('confirmStateChange')
            ->assertSet('isStateModalVisible', false)
            ->assertSee(AdminUserSecurityMessages::LAST_ACTIVE_ADMIN_DEACTIVATION);

        $this->assertDatabaseHas('users', [
            'id' => $targetAdmin->id,
            'state' => StateEnum::ACTIVE->value,
        ]);
    }
}
