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

class AdminUsersRoleChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_change_user_role_from_listing_with_confirmation_modal(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $targetUser = User::factory()->create([
            'name' => 'Usuario Objetivo',
            'role' => RoleEnum::CLIENT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openRoleModal', $targetUser->id, RoleEnum::SUPPORT->value)
            ->assertSet('isRoleModalVisible', true)
            ->call('confirmRoleChange')
            ->assertSet('isRoleModalVisible', false)
            ->assertSee('Rol de usuario actualizado correctamente.');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => RoleEnum::SUPPORT->value,
        ]);
    }

    public function test_it_rejects_role_change_when_role_value_is_invalid(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $targetUser = User::factory()->create([
            'role' => RoleEnum::CLIENT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openRoleModal', $targetUser->id, 'rol-invalido')
            ->assertSet('isRoleModalVisible', false)
            ->assertSee('El rol seleccionado no es valido.');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => RoleEnum::CLIENT->value,
        ]);
    }

    public function test_it_rejects_role_change_when_target_user_does_not_exist(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openRoleModal', 999999, RoleEnum::SUPPORT->value)
            ->assertSet('isRoleModalVisible', false)
            ->assertSee('El usuario seleccionado no existe.');
    }

    public function test_it_rejects_role_change_when_role_is_the_same(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $targetUser = User::factory()->create([
            'role' => RoleEnum::CLIENT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openRoleModal', $targetUser->id, RoleEnum::CLIENT->value)
            ->assertSet('isRoleModalVisible', false)
            ->assertSee('El usuario ya tiene el rol seleccionado.');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => RoleEnum::CLIENT->value,
        ]);
    }

    public function test_it_blocks_removing_admin_role_from_last_active_admin(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::actingAs($admin)
            ->test(IndexUsers::class)
            ->call('openRoleModal', $admin->id, RoleEnum::OPERATOR->value)
            ->assertSet('isRoleModalVisible', true)
            ->call('confirmRoleChange')
            ->assertSet('isRoleModalVisible', false)
            ->assertSee(AdminUserSecurityMessages::LAST_ACTIVE_ADMIN_ROLE_CHANGE);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => RoleEnum::ADMIN->value,
        ]);
    }
}
