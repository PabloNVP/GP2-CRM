<?php

namespace App\Livewire\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\StateEnum;
use App\Enums\StateProductEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Actions\Tickets\CreateTicket;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AddTicket extends Component
{
    public string $clientId = '';
    public string $productId = '';
    public string $subject = '';
    public string $description = '';
    public string $priority = '';

    public function mount(): void
    {
        $this->priority = PriorityTicketEnum::getDefaultValue();
    }

    public function render()
    {
        $clients = Client::query()
            ->where('state', StateEnum::ACTIVE->value)
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get(['id', 'firstname', 'lastname']);

        $products = Product::query()
            ->where('status', StateProductEnum::AVAILABLE->value)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('tickets.add-ticket', [
            'clients' => $clients,
            'products' => $products,
            'priorities' => PriorityTicketEnum::cases(),
        ]);
    }

    public function saveTicket(CreateTicket $createTicket)
    {
        $this->subject = trim($this->subject);
        $this->description = trim($this->description);

        $validated = $this->validate();

        $ticket = $createTicket([
            'client_id' => (int) $validated['clientId'],
            'product_id' => $validated['productId'] === '' ? null : (int) $validated['productId'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'state' => StateTicketEnum::OPEN->value,
        ]);

        session()->flash('message', 'Ticket creado exitosamente.');

        return redirect()->route('tickets.show', $ticket);
    }

    public function rules(): array
    {
        return [
            'clientId' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')
                    ->where('state', StateEnum::ACTIVE->value)
                    ->whereNull('deleted_at'),
            ],
            'productId' => [
                'nullable',
                'integer',
                Rule::exists('products', 'id')
                    ->where('status', StateProductEnum::AVAILABLE->value)
                    ->whereNull('deleted_at'),
            ],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', Rule::in(PriorityTicketEnum::getDatabaseValues())],
        ];
    }

    public function messages(): array
    {
        return [
            'clientId.required' => 'El cliente es obligatorio.',
            'clientId.integer' => 'El cliente seleccionado no es valido.',
            'clientId.exists' => 'El cliente debe existir y estar activo.',

            'productId.integer' => 'El producto seleccionado no es valido.',
            'productId.exists' => 'El producto debe existir y estar disponible.',

            'subject.required' => 'El asunto es obligatorio.',
            'subject.string' => 'El asunto debe ser una cadena de texto.',
            'subject.max' => 'El asunto no puede tener mas de 255 caracteres.',

            'description.required' => 'La descripcion es obligatoria.',
            'description.string' => 'La descripcion debe ser una cadena de texto.',

            'priority.required' => 'La prioridad es obligatoria.',
            'priority.in' => 'La prioridad seleccionada no es valida.',
        ];
    }
}
