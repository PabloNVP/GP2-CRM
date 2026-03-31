<?php

namespace Tests\Browser;

use App\Enums\PriorityTicketEnum;
use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\StateTicketEnum;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TicketsFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_it_executes_ticket_flow_from_open_to_closed(): void
    {
        $supportUser = User::factory()->create([
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $client = Client::query()->create([
            'firstname' => 'Ticket',
            'lastname' => 'Dusk',
            'email' => 'ticket.dusk.'.uniqid().'@example.com',
            'phone' => '123456789',
            'address' => 'Calle Dusk',
            'company' => 'Empresa Dusk',
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = Ticket::query()->create([
            'client_id' => $client->id,
            'product_id' => null,
            'subject' => 'Incidencia E2E '.uniqid(),
            'description' => 'Ticket para validar flujo de estados en Dusk.',
            'priority' => PriorityTicketEnum::MEDIUM->value,
            'state' => StateTicketEnum::OPEN->value,
        ]);

        DB::table('ticket_responses')->insert([
            'ticket_id' => $ticket->id,
            'user_id' => $supportUser->id,
            'message' => 'Respuesta previa para habilitar cierre.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($supportUser, $ticket): void {
            $browser->loginAs($supportUser)
                ->visit('/tickets/'.$ticket->id)
                ->assertSee('Detalle de Ticket #'.$ticket->id)
                ->assertSee('Abierto')
                ->press('Pasar a En progreso')
                ->pause(600)
                ->refresh()
                ->assertSee('En progreso')
                ->press('Marcar resuelto')
                ->pause(600)
                ->refresh()
                ->assertSee('Resuelto')
                ->press('Cerrar ticket')
                ->waitForText('Confirmar cierre de ticket')
                ->press('Confirmar')
                ->pause(600)
                ->refresh()
                ->assertSee('Cerrado');
        });

        $ticket->refresh();

        $this->assertSame(StateTicketEnum::CLOSED, $ticket->state);
    }
}
