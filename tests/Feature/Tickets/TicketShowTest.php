<?php

namespace Tests\Feature\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\StateEnum;
use App\Enums\StateProductEnum;
use App\Enums\StateTicketEnum;
use App\Models\Client;
use App\Models\Product;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TicketShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_ticket_detail_with_client_product_status_priority_and_timeline(): void
    {
        $user = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        $client = Client::query()->create([
            'firstname' => 'Lucia',
            'lastname' => 'Suarez',
            'email' => 'lucia.ticket.show@example.com',
            'phone' => '11223344',
            'address' => 'Calle 123',
            'company' => 'Empresa Test',
            'state' => StateEnum::ACTIVE->value,
        ]);

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'CRM',
            'description' => 'Categoria CRM',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product = Product::query()->create([
            'category_id' => $categoryId,
            'name' => 'CRM Pro',
            'description' => 'Producto de prueba',
            'unit_price' => 100,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);

        $ticket = Ticket::query()->create([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'subject' => 'Error al iniciar sesion',
            'description' => 'No puedo iniciar sesion desde ayer',
            'priority' => PriorityTicketEnum::HIGH->value,
            'state' => StateTicketEnum::OPEN->value,
        ]);

        DB::table('ticket_responses')->insert([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Primera respuesta',
            'created_at' => '2026-03-31 10:00:00',
            'updated_at' => '2026-03-31 10:00:00',
        ]);

        DB::table('ticket_responses')->insert([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Segunda respuesta',
            'created_at' => '2026-03-31 11:00:00',
            'updated_at' => '2026-03-31 11:00:00',
        ]);

        $this->actingAs($user)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Detalle de Ticket #'.$ticket->id)
            ->assertSee('Lucia Suarez')
            ->assertSee('lucia.ticket.show@example.com')
            ->assertSee('11223344')
            ->assertSee('Empresa Test')
            ->assertSee('Error al iniciar sesion')
            ->assertSee('No puedo iniciar sesion desde ayer')
            ->assertSee('CRM Pro')
            ->assertSee('Abierto')
            ->assertSee('Alta')
            ->assertSee('Timeline de respuestas')
            ->assertSeeInOrder(['Primera respuesta', 'Segunda respuesta']);
    }

    public function test_it_returns_404_when_ticket_does_not_exist(): void
    {
        $user = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/tickets/999999')
            ->assertNotFound();
    }
}
