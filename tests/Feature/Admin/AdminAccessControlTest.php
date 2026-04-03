<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_roles_cannot_access_admin_routes(): void
    {
        $blockedRoles = [
            RoleEnum::OPERATOR,
            RoleEnum::SUPPORT,
            RoleEnum::SALES,
            RoleEnum::ADMINISTRATIVE,
            RoleEnum::CLIENT,
        ];

        foreach ($blockedRoles as $role) {
            $user = User::factory()->create([
                'role' => $role->value,
                'state' => StateEnum::ACTIVE->value,
            ]);

            $this->actingAs($user)
                ->get('/admin/dashboard')
                ->assertStatus(403);

            $this->actingAs($user)
                ->get('/admin/users')
                ->assertStatus(403);

            auth()->logout();
        }
    }
}
