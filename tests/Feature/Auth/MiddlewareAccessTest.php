<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MiddlewareAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_role_allows_access_for_permitted_role(): void
    {
        Route::middleware(['web', 'auth', 'check.role:admin'])
            ->get('/test-role-admin', fn () => response('ok', 200));

        $user = User::factory()->create([
            'role' => 'admin',
            'state' => 'activo',
        ]);

        $response = $this->actingAs($user)->get('/test-role-admin');

        $response->assertOk();
    }

    public function test_check_role_denies_access_for_non_permitted_role(): void
    {
        Route::middleware(['web', 'auth', 'check.role:admin'])
            ->get('/test-role-denied', fn () => response('ok', 200));

        $user = User::factory()->create([
            'role' => 'cliente',
            'state' => 'activo',
        ]);

        $response = $this->actingAs($user)->get('/test-role-denied');

        $response->assertForbidden();
    }

    public function test_check_role_accepts_multiple_roles(): void
    {
        Route::middleware(['web', 'auth', 'check.role:admin,soporte'])
            ->get('/test-role-multi', fn () => response('ok', 200));

        $user = User::factory()->create([
            'role' => 'soporte',
            'state' => 'activo',
        ]);

        $response = $this->actingAs($user)->get('/test-role-multi');

        $response->assertOk();
    }

    public function test_check_state_denies_access_and_logs_out_inactive_user(): void
    {
        Route::middleware(['web', 'auth', 'check.state'])
            ->get('/test-state-inactive', fn () => response('ok', 200));

        $user = User::factory()->create([
            'role' => 'cliente',
            'state' => 'inactivo',
        ]);

        $response = $this->actingAs($user)->get('/test-state-inactive');

        $response->assertForbidden();
        $this->assertGuest();
    }
}
