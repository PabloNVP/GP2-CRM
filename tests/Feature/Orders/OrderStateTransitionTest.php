<?php

namespace Tests\Feature\Orders;

use App\Enums\StateEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Orders\IndexOrders;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderStateTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_applies_valid_state_transitions_in_sequence(): void
    {
        $clientId = $this->createClient('Secuencia', 'Valida', 'secuencia.valida@example.com');
        $order = $this->createOrder($clientId, StateOrderEnum::PENDING);

        Livewire::test(IndexOrders::class)
            ->call('changeState', $order->id, StateOrderEnum::PROCESSING->value)
            ->assertSee('Estado de la orden actualizado correctamente.')
            ->set('stateFilter', StateOrderEnum::PROCESSING->value)
            ->assertSee('Secuencia Valida')
            ->set('stateFilter', StateOrderEnum::PENDING->value)
            ->assertDontSee('Secuencia Valida')
            ->call('changeState', $order->id, StateOrderEnum::SHIPPED->value)
            ->assertSee('Estado de la orden actualizado correctamente.')
            ->call('changeState', $order->id, StateOrderEnum::DELIVERED->value)
            ->assertSee('Estado de la orden actualizado correctamente.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'state' => StateOrderEnum::DELIVERED->value,
        ]);
    }

    public function test_it_rejects_invalid_transition_and_keeps_current_state(): void
    {
        $clientId = $this->createClient('Invalida', 'Directa', 'invalida.directa@example.com');
        $order = $this->createOrder($clientId, StateOrderEnum::PENDING);

        Livewire::test(IndexOrders::class)
            ->call('changeState', $order->id, StateOrderEnum::DELIVERED->value)
            ->assertSee('Transicion de estado invalida para la orden seleccionada.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'state' => StateOrderEnum::PENDING->value,
        ]);
    }

    public function test_it_does_not_allow_going_back_to_pending_from_shipped_or_delivered(): void
    {
        $clientId = $this->createClient('Regla', 'Pendiente', 'regla.pendiente@example.com');
        $order = $this->createOrder($clientId, StateOrderEnum::SHIPPED);

        Livewire::test(IndexOrders::class)
            ->call('changeState', $order->id, StateOrderEnum::PENDING->value)
            ->assertSee('No se puede volver a Pendiente una orden ya Enviada o Entregada.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'state' => StateOrderEnum::SHIPPED->value,
        ]);
    }

    public function test_it_cancels_order_from_pending_state_with_confirmation_modal(): void
    {
        $clientId = $this->createClient('Cancelar', 'Pendiente', 'cancelar.pendiente@example.com');
        $order = $this->createOrder($clientId, StateOrderEnum::PENDING);

        Livewire::test(IndexOrders::class)
            ->call('openCancelModal', $order->id)
            ->assertSet('isCancelModalVisible', true)
            ->call('confirmCancel')
            ->assertSet('isCancelModalVisible', false)
            ->assertSee('Orden cancelada correctamente.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'state' => StateOrderEnum::CANCELLED->value,
        ]);
    }

    public function test_it_cancels_order_from_processing_state_with_confirmation_modal(): void
    {
        $clientId = $this->createClient('Cancelar', 'Proceso', 'cancelar.proceso@example.com');
        $order = $this->createOrder($clientId, StateOrderEnum::PROCESSING);

        Livewire::test(IndexOrders::class)
            ->call('openCancelModal', $order->id)
            ->assertSet('isCancelModalVisible', true)
            ->call('confirmCancel')
            ->assertSet('isCancelModalVisible', false)
            ->assertSee('Orden cancelada correctamente.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'state' => StateOrderEnum::CANCELLED->value,
        ]);
    }

    public function test_it_rejects_cancel_when_state_is_not_pending_or_processing(): void
    {
        $clientId = $this->createClient('No', 'Cancelable', 'no.cancelable@example.com');
        $order = $this->createOrder($clientId, StateOrderEnum::SHIPPED);

        Livewire::test(IndexOrders::class)
            ->call('openCancelModal', $order->id)
            ->assertSet('isCancelModalVisible', false)
            ->assertSee('Solo se puede cancelar una orden en estado Pendiente o En proceso.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'state' => StateOrderEnum::SHIPPED->value,
        ]);
    }

    private function createClient(string $firstname, string $lastname, string $email): int
    {
        return \App\Models\Client::query()->insertGetId([
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

    private function createOrder(int $clientId, StateOrderEnum $state): Order
    {
        return Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => $state->value,
            'total' => 100,
            'observations' => null,
        ]);
    }
}
