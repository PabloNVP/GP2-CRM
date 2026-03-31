<?php

namespace Tests\Feature\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\StateEnum;
use App\Enums\StateProductEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Tickets\IndexTickets;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class TicketsFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_tickets_by_subject(): void
    {
        $clientId = $this->createClient('Juan', 'Soporte', 'juan-subject@example.com');

        $this->createTicket($clientId, 'Error de login', PriorityTicketEnum::HIGH, StateTicketEnum::OPEN, null, '2026-03-15');
        $this->createTicket($clientId, 'Consulta comercial', PriorityTicketEnum::MEDIUM, StateTicketEnum::OPEN, null, '2026-03-15');

        Livewire::test(IndexTickets::class)
            ->set('search', 'login')
            ->assertSee('Error de login')
            ->assertDontSee('Consulta comercial');
    }

    public function test_it_filters_tickets_by_client_name(): void
    {
        $juanId = $this->createClient('Juan', 'Perez', 'juan-client@example.com');
        $anaId = $this->createClient('Ana', 'Gomez', 'ana-client@example.com');

        $this->createTicket($juanId, 'Caso Juan', PriorityTicketEnum::HIGH, StateTicketEnum::OPEN, null, '2026-03-16');
        $this->createTicket($anaId, 'Caso Ana', PriorityTicketEnum::HIGH, StateTicketEnum::OPEN, null, '2026-03-16');

        Livewire::test(IndexTickets::class)
            ->set('search', 'juan')
            ->assertSee('Juan Perez')
            ->assertDontSee('Ana Gomez');
    }

    public function test_it_filters_tickets_by_ticket_id(): void
    {
        $clientId = $this->createClient('Numero', 'Ticket', 'numero-ticket@example.com');

        $this->createTicket($clientId, 'Ticket uno', PriorityTicketEnum::MEDIUM, StateTicketEnum::OPEN, null, '2026-03-17');
        $targetTicketId = $this->createTicket($clientId, 'Ticket dos', PriorityTicketEnum::MEDIUM, StateTicketEnum::OPEN, null, '2026-03-17');

        Livewire::test(IndexTickets::class)
            ->set('search', (string) $targetTicketId)
            ->assertSee('Ticket dos')
            ->assertDontSee('Ticket uno');
    }

    public function test_it_filters_tickets_by_priority_state_product_and_date_range(): void
    {
        $clientId = $this->createClient('Filtros', 'Multiples', 'filtros-multiples@example.com');
        $categoryId = $this->createCategory('Soporte', 'Cat soporte');
        $crmProductId = $this->createProduct($categoryId, 'CRM Pro');
        $erpProductId = $this->createProduct($categoryId, 'ERP Plus');

        $this->createTicket($clientId, 'Target', PriorityTicketEnum::CRITICAL, StateTicketEnum::IN_PROGRESS, $crmProductId, '2026-03-20');
        $this->createTicket($clientId, 'Otro prioridad', PriorityTicketEnum::LOW, StateTicketEnum::IN_PROGRESS, $crmProductId, '2026-03-20');
        $this->createTicket($clientId, 'Otro estado', PriorityTicketEnum::CRITICAL, StateTicketEnum::OPEN, $crmProductId, '2026-03-20');
        $this->createTicket($clientId, 'Otro producto', PriorityTicketEnum::CRITICAL, StateTicketEnum::IN_PROGRESS, $erpProductId, '2026-03-20');
        $this->createTicket($clientId, 'Fuera fecha', PriorityTicketEnum::CRITICAL, StateTicketEnum::IN_PROGRESS, $crmProductId, '2026-04-05');

        Livewire::test(IndexTickets::class)
            ->set('priorityFilter', PriorityTicketEnum::CRITICAL->value)
            ->set('stateFilter', StateTicketEnum::IN_PROGRESS->value)
            ->set('productFilter', (string) $crmProductId)
            ->set('fromDate', '2026-03-01')
            ->set('toDate', '2026-03-31')
            ->assertSee('Target')
            ->assertDontSee('Otro prioridad')
            ->assertDontSee('Otro estado')
            ->assertDontSee('Otro producto')
            ->assertDontSee('Fuera fecha');
    }

    public function test_it_resets_pagination_when_filters_change(): void
    {
        $clientId = $this->createClient('Paginacion', 'Base', 'paginacion-base@example.com');

        for ($index = 1; $index <= 11; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);

            $this->createTicket(
                $clientId,
                "Ticket abierto {$number}",
                PriorityTicketEnum::MEDIUM,
                StateTicketEnum::OPEN,
                null,
                '2026-03-18',
            );
        }

        $this->createTicket(
            $clientId,
            'Ticket cerrado objetivo',
            PriorityTicketEnum::MEDIUM,
            StateTicketEnum::CLOSED,
            null,
            '2026-03-18',
        );

        Livewire::test(IndexTickets::class)
            ->call('gotoPage', 2)
            ->assertSee('Ticket abierto 01')
            ->set('stateFilter', StateTicketEnum::CLOSED->value)
            ->assertSee('Ticket cerrado objetivo')
            ->assertDontSee('Ticket abierto 01');
    }

    private function createTicket(
        int $clientId,
        string $subject,
        PriorityTicketEnum $priority,
        StateTicketEnum $state,
        ?int $productId,
        string $createdAt,
    ): int {
        return DB::table('tickets')->insertGetId([
            'client_id' => $clientId,
            'product_id' => $productId,
            'subject' => $subject,
            'description' => "Descripcion de {$subject}",
            'priority' => $priority->value,
            'state' => $state->value,
            'created_at' => $createdAt.' 10:00:00',
            'updated_at' => $createdAt.' 10:00:00',
            'deleted_at' => null,
        ]);
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
            'unit_price' => 200,
            'status' => StateProductEnum::AVAILABLE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
