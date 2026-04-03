<?php

namespace Tests\Browser;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminUsersFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_it_executes_admin_dashboard_and_user_management_flow(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $targetUser = User::factory()->create([
            'name' => 'Usuario Dusk Objetivo',
            'email' => 'usuario.dusk.objetivo@example.com',
            'role' => RoleEnum::CLIENT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $targetUser): void {
            $browser->loginAs($admin)
                ->visit('/admin/dashboard')
                ->assertSee('Dashboard de administracion')
                ->click('@nav-admin-users')
                ->waitForLocation('/admin/users')
                ->waitForText('Usuarios')
                ->assertSee('Usuario Dusk Objetivo')
                ->select('#role-user-'.$targetUser->id, RoleEnum::SUPPORT->value)
                ->waitForText('Confirmar cambio de rol')
                ->press('Confirmar')
                ->waitForText('Rol de usuario actualizado correctamente.')
                ->click('#state-action-user-'.$targetUser->id)
                ->waitForText('Confirmar cambio de estado')
                ->press('Confirmar')
                ->waitForText('Estado de usuario actualizado correctamente.');
        });

        $targetUser->refresh();

        $this->assertSame(RoleEnum::SUPPORT, $targetUser->role);
        $this->assertSame(StateEnum::INACTIVE, $targetUser->state);
    }
}
