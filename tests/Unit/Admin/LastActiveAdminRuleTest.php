<?php

namespace Tests\Unit\Admin;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Livewire\Actions\Users\ChangeUserRole;
use App\Livewire\Actions\Users\ChangeUserState;
use App\Models\User;
use App\Support\AdminUserSecurityMessages;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LastActiveAdminRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_removing_role_from_last_active_admin(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $action = new ChangeUserRole();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(AdminUserSecurityMessages::LAST_ACTIVE_ADMIN_ROLE_CHANGE);

        $action($admin->id, RoleEnum::OPERATOR);
    }

    public function test_it_allows_removing_admin_role_when_another_admin_is_active(): void
    {
        $adminOne = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $action = new ChangeUserRole();

        $result = $action($adminOne->id, RoleEnum::OPERATOR);

        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'id' => $adminOne->id,
            'role' => RoleEnum::OPERATOR->value,
        ]);
    }

    public function test_it_rejects_deactivating_last_active_admin(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $action = new ChangeUserState();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(AdminUserSecurityMessages::LAST_ACTIVE_ADMIN_DEACTIVATION);

        $action($admin->id, StateEnum::INACTIVE, null);
    }

    public function test_it_allows_deactivating_admin_when_another_admin_is_active(): void
    {
        $adminOne = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $action = new ChangeUserState();

        $result = $action($adminOne->id, StateEnum::INACTIVE, null);

        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'id' => $adminOne->id,
            'state' => StateEnum::INACTIVE->value,
        ]);
    }
}
