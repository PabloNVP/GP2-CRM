<?php

namespace Tests\Feature\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\StateProductEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Tickets\IndexTickets;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class TicketsListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_empty_state_when_there_are_no_tickets(): void
    {
        Livewire::test(IndexTickets::class)
            ->assertSee('No hay tickets registrados');
    }

    public function test_it_displays_required_columns_on_the_listing(): void
    {
        $clientId = $this->createClient('Nico', 'Martinez', 'nico.tickets@example.com');
        $categoryId = $this->createCategory('ERP', 'Suite ERP');
        $productId = $this->createProduct($categoryId, 'CRM Pro');

        DB::table('tickets')->insert([
            'client_id' => $clientId,
            'product_id' => $productId,
            'subject' => 'Error al iniciar sesion',
            'description' => 'No puedo acceder al panel',
            'priority' => PriorityTicketEnum::HIGH->value,
            'state' => StateTicketEnum::OPEN->value,
            'created_at' => '2026-03-31 10:00:00',
            'updated_at' => '2026-03-31 10:00:00',
            'deleted_at' => null,
        ]);

        Livewire::test(IndexTickets::class)
            ->assertSee('Numero de ticket')
            ->assertSee('Cliente')
            ->assertSee('Producto')
            ->assertSee('Prioridad')
            ->assertSee('Estado')
            ->assertSee('Fecha de creacion')
            ->assertSee('Nico Martinez')
            ->assertSee('CRM Pro')
            ->assertSee('Alta')
            ->assertSee('Abierto')
            ->assertSee('31/03/2026');
    }

    public function test_it_paginates_tickets_by_ten_records(): void
    {
        $this->seedTickets(11);

        Livewire::test(IndexTickets::class)
            ->assertSee('Cliente 11 Ticket 11')
            ->assertDontSee('Cliente 01 Ticket 01')
            ->call('gotoPage', 2)
            ->assertSee('Cliente 01 Ticket 01')
            ->assertDontSee('Cliente 11 Ticket 11');
    }

    public function test_tickets_route_is_available_for_authenticated_active_users(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/tickets')
            ->assertOk()
            ->assertSee('Tickets');
    }

    public function test_tickets_route_redirects_guests_to_login(): void
    {
        $this->get('/tickets')
            ->assertRedirect('/login');
    }

    private function seedTickets(int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $clientId = $this->createClient(
                "Cliente {$number}",
                "Ticket {$number}",
                "cliente.ticket.{$number}@example.com",
            );

            DB::table('tickets')->insert([
                'client_id' => $clientId,
                'product_id' => null,
                'subject' => "Asunto {$number}",
                'description' => "Descripcion {$number}",
                'priority' => PriorityTicketEnum::MEDIUM->value,
                'state' => StateTicketEnum::OPEN->value,
                'created_at' => now()->subDays($index),
                'updated_at' => now()->subDays($index),
                'deleted_at' => null,
            ]);
        }
    }

    private function createClient(string $firstname, string $lastname, string $email): int
    {
        return DB::table('clients')->insertGetId([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => '123456789',
            'address' => 'Calle 123',
            'company' => 'GP2',
            'state' => StateEnum::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }

    private function createCategory(string $name, ?string $description = null): int
    {
        return DB::table('categories')->insertGetId([
            'name' => $name,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createProduct(int $categoryId, string $name): int
    {
        return DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'name' => $name,
            'description' => null,
            'unit_price' => 100,
            'status' => StateProductEnum::AVAILABLE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
