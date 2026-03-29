<?php

namespace App\Livewire\Orders;

use App\Enums\StateOrderEnum;
use App\Enums\StateProductEnum;
use App\Livewire\Actions\Orders\InsertOrder;
use App\Livewire\Actions\Orders\InsertOrderDetail;
use App\Livewire\Actions\Orders\RecalculateOrderTotal;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddOrder extends Component
{
    public string $clientId = '';
    public string $date = '';
    public string $observations = '';

    /**
     * @var array<int, array{product_id:string, count:int, unit_price:string, subtotal:string}>
     */
    public array $items = [];

    public string $total = '0.00';

    public function mount(): void
    {
        $this->date = now()->toDateString();

        if ($this->items === []) {
            $this->addItem();
        }
    }

    public function render()
    {
        $clients = Client::query()
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get(['id', 'firstname', 'lastname']);

        $products = Product::query()
            ->orderBy('name')
            ->get(['id', 'name', 'unit_price', 'status']);

        return view('orders.add-order', compact('clients', 'products'));
    }

    public function updated(string $property): void
    {
        if (! str_starts_with($property, 'items.')) {
            return;
        }

        $segments = explode('.', $property);
        $itemIndex = (int) ($segments[1] ?? -1);
        $field = $segments[2] ?? '';

        if (! isset($this->items[$itemIndex])) {
            return;
        }

        if ($field === 'product_id') {
            $this->hydrateItemPrice($itemIndex);
        }

        $this->recalculateItemSubtotal($itemIndex);
        $this->recalculateTotal();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_id' => '',
            'count' => 1,
            'unit_price' => '0.00',
            'subtotal' => '0.00',
        ];

        $this->recalculateTotal();
    }

    public function removeItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if ($this->items === []) {
            $this->addItem();
            return;
        }

        foreach (array_keys($this->items) as $itemIndex) {
            $this->recalculateItemSubtotal($itemIndex);
        }

        $this->recalculateTotal();
    }

    public function saveOrder(
        InsertOrder $insertOrder,
        InsertOrderDetail $insertOrderDetail,
        RecalculateOrderTotal $recalculateOrderTotal,
    ) {
        $this->resetErrorBag();

        $validated = $this->validate();

        $productIds = collect($validated['items'])
            ->pluck('product_id')
            ->map(static fn ($id) => (int) $id)
            ->all();

        /** @var Collection<int, Product> $products */
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get(['id', 'status', 'unit_price'])
            ->keyBy('id');

        $detailsToPersist = [];

        foreach ($validated['items'] as $index => $item) {
            $productId = (int) $item['product_id'];
            $product = $products->get($productId);

            if (! $product) {
                $this->addError("items.{$index}.product_id", 'El producto seleccionado no existe.');
                return;
            }

            if ($product->status === StateProductEnum::OUT_OF_STOCK) {
                $this->addError("items.{$index}.product_id", 'No se puede agregar un producto sin stock.');
                return;
            }

            $count = (int) $item['count'];
            $unitPrice = (float) $product->unit_price;
            $subtotal = $recalculateOrderTotal->lineSubtotal($count, $unitPrice);

            $detailsToPersist[] = [
                'product_id' => $productId,
                'count' => $count,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        $total = $recalculateOrderTotal($detailsToPersist);

        $orderId = DB::transaction(function () use (
            $insertOrder,
            $insertOrderDetail,
            $validated,
            $detailsToPersist,
            $total,
        ): int {
            $order = $insertOrder([
                'client_id' => (int) $validated['clientId'],
                'date' => $validated['date'],
                'state' => StateOrderEnum::PENDING->value,
                'total' => $total,
                'observations' => $validated['observations'] === '' ? null : $validated['observations'],
            ]);

            foreach ($detailsToPersist as $detail) {
                $insertOrderDetail([
                    'order_id' => $order->id,
                    'product_id' => $detail['product_id'],
                    'count' => $detail['count'],
                    'unit_price' => $detail['unit_price'],
                    'subtotal' => $detail['subtotal'],
                ]);
            }

            return $order->id;
        });

        session()->flash('message', 'Orden creada exitosamente.');

        return redirect()->route('orders.show', $orderId);
    }

    public function rules(): array
    {
        return [
            'clientId' => ['required', 'integer', 'exists:clients,id'],
            'date' => ['required', 'date'],
            'observations' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.count' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'clientId.required' => 'El cliente es obligatorio.',
            'clientId.integer' => 'El cliente seleccionado no es valido.',
            'clientId.exists' => 'El cliente seleccionado no existe.',

            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha no tiene un formato valido.',

            'observations.string' => 'Las observaciones deben ser un texto valido.',

            'items.required' => 'Debe agregar al menos un item en la orden.',
            'items.array' => 'Los items de la orden no tienen un formato valido.',
            'items.min' => 'Debe agregar al menos un item en la orden.',

            'items.*.product_id.required' => 'Debe seleccionar un producto para cada item.',
            'items.*.product_id.integer' => 'El producto seleccionado no es valido.',
            'items.*.product_id.exists' => 'El producto seleccionado no existe.',

            'items.*.count.required' => 'La cantidad del item es obligatoria.',
            'items.*.count.integer' => 'La cantidad del item debe ser un numero entero.',
            'items.*.count.min' => 'La cantidad del item debe ser al menos 1.',
        ];
    }

    private function hydrateItemPrice(int $index): void
    {
        $productId = (int) ($this->items[$index]['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->items[$index]['unit_price'] = '0.00';
            return;
        }

        $product = Product::query()->find($productId, ['unit_price']);

        $this->items[$index]['unit_price'] = $product
            ? number_format((float) $product->unit_price, 2, '.', '')
            : '0.00';
    }

    private function recalculateItemSubtotal(int $index): void
    {
        $count = max(0, (int) ($this->items[$index]['count'] ?? 0));
        $unitPrice = (float) ($this->items[$index]['unit_price'] ?? 0);

        $subtotal = round($count * $unitPrice, 2);

        $this->items[$index]['subtotal'] = number_format($subtotal, 2, '.', '');
    }

    private function recalculateTotal(): void
    {
        $total = 0.0;

        foreach ($this->items as $item) {
            $total += (float) ($item['subtotal'] ?? 0);
        }

        $this->total = number_format(round($total, 2), 2, '.', '');
    }
}
